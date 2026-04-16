<?php
namespace App\Services;

use App\Models\Guru;
use App\Models\PengaturanDapodik;
use App\Models\Rombel;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SyncLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DapodikService
{
    protected PengaturanDapodik $config;

    public function __construct()
    {
        $this->config = PengaturanDapodik::aktif();
    }

    // ==========================================
    // CEK KONEKSI
    // ==========================================
    public function isConnected(): bool
    {
        try {
            $response = Http::timeout(10)
                ->withToken($this->config->bearer_token)
                ->get($this->config->base_url . '/WebService/getSekolah?npsn=' . $this->config->npsn);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    // ==========================================
    // REQUEST KE API DAPODIK (dengan pagination)
    // ==========================================
    private function fetchAll(string $endpoint): array
    {
        $allData = [];
        $start   = 0;
        $limit   = 100;

        do {
            try {
                $url = $this->config->base_url
                     . '/WebService/' . $endpoint
                     . '?npsn=' . $this->config->npsn
                     . '&start=' . $start
                     . '&limit=' . $limit;

                $response = Http::timeout(30)
                    ->withToken($this->config->bearer_token)
                    ->get($url);

                if (!$response->successful()) break;

                $json    = $response->json();
                $rows    = $json['rows']    ?? [];
                $total   = $json['results'] ?? 0;
                $allData = array_merge($allData, $rows);
                $start  += $limit;

            } catch (\Exception $e) {
                Log::error("Dapodik fetch error [{$endpoint}]: " . $e->getMessage());
                break;
            }
        } while (count($allData) < $total);

        return $allData;
    }

    // ==========================================
    // SYNC SEMESTER
    // ==========================================
    public function syncSemester(): array
{
    $start  = now();
    $result = ['created' => 0, 'updated' => 0, 'failed' => 0, 'errors' => []];

    try {
        // Ambil sedikit data saja untuk ekstrak semester_id
        $url = $this->config->base_url
             . '/WebService/getPesertaDidik'
             . '?npsn=' . $this->config->npsn
             . '&start=0&limit=100';

        $response = Http::timeout(30)
            ->withToken($this->config->bearer_token)
            ->get($url);

        if (!$response->successful()) {
            $result['errors'][] = 'Gagal mengambil data dari Dapodik';
            $this->simpanLog('semester', null, $result, now()->diffInSeconds($start));
            return $result;
        }

        $semesterIds = collect($response->json('rows', []))
            ->pluck('semester_id')
            ->filter()
            ->unique()
            ->values();

        if ($semesterIds->isEmpty()) {
            $result['errors'][] = 'Tidak ada semester_id ditemukan';
            $this->simpanLog('semester', null, $result, now()->diffInSeconds($start));
            return $result;
        }

        foreach ($semesterIds as $semesterId) {
            $payload = [
                'semester_id'  => $semesterId,
                'nama'         => Semester::parseNama($semesterId),
                'tahun_ajaran' => Semester::parseTahunAjaran($semesterId),
                'tipe'         => Semester::parseTipe($semesterId),
            ];

            $semester = Semester::where('semester_id', $semesterId)->first();
            if ($semester) {
                $semester->update($payload);
                $result['updated']++;
            } else {
                Semester::create($payload);
                $result['created']++;
            }
        }

    } catch (\Exception $e) {
        $result['failed']++;
        $result['errors'][] = $e->getMessage();
    }

    $this->simpanLog('semester', null, $result, now()->diffInSeconds($start));
    return $result;
}

    // ==========================================
    // SYNC SISWA
    // ==========================================
    public function syncSiswa(string $semesterId): array
    {
        $start  = now();
        $result = ['created' => 0, 'updated' => 0, 'archived' => 0, 'failed' => 0, 'errors' => []];

        // Arsipkan data semester lama
        $archived = Siswa::where('is_archived', false)
            ->whereHas('semester', fn($q) => $q->where('semester_id', '!=', $semesterId))
            ->count();

        Siswa::where('is_archived', false)
            ->whereHas('semester', fn($q) => $q->where('semester_id', '!=', $semesterId))
            ->update(['is_archived' => true, 'semester_arsip' => $semesterId]);

        $result['archived'] = $archived;

        // Ambil semester lokal
        $semester = Semester::where('semester_id', $semesterId)->first();

        // Tarik data dari Dapodik
        $rows = $this->fetchAll('getPesertaDidik');

        foreach ($rows as $data) {
            // Filter berdasarkan semester
            if (($data['semester_id'] ?? '') !== $semesterId) continue;

            try {
                $nisn  = $data['nisn']             ?? null;
                $pdId  = $data['peserta_didik_id'] ?? null;
                $nama  = $data['nama']             ?? '';
                $tglLahir = $data['tanggal_lahir'] ?? null;

                $payload = $this->mapSiswa($data, $semester?->id);
                $siswa   = null;

                // Prioritas 1: cari by NISN
                if ($nisn) {
                    $siswa = Siswa::withTrashed()->where('nisn', $nisn)->first();
                }

                // Prioritas 2: cari by peserta_didik_id
                if (!$siswa && $pdId) {
                    $siswa = Siswa::withTrashed()->where('peserta_didik_id', $pdId)->first();
                }

                // Prioritas 3: fallback nama + tanggal lahir (untuk siswa manual)
                if (!$siswa && $nama && $tglLahir) {
                    $siswa = Siswa::withTrashed()
                        ->where('nama', $nama)
                        ->where('tanggal_lahir', $tglLahir)
                        ->first();
                }

                if ($siswa) {
                    if ($siswa->trashed()) $siswa->restore();
                    $siswa->update($payload);
                    $result['updated']++;
                } else {
                    Siswa::create($payload);
                    $result['created']++;
                }

            } catch (\Exception $e) {
                $result['failed']++;
                $result['errors'][] = ($data['nama'] ?? '?') . ': ' . $e->getMessage();
                Log::error('Sync siswa error: ' . $e->getMessage());
            }
        }

        $this->simpanLog('siswa', $semesterId, $result, now()->diffInSeconds($start));
        return $result;
    }

    // ==========================================
    // SYNC GURU/GTK
    // ==========================================
    public function syncGuru(string $semesterId): array
    {
        $start  = now();
        $result = ['created' => 0, 'updated' => 0, 'archived' => 0, 'failed' => 0, 'errors' => []];

        // Ambil tahun ajaran dari semester
        $tahunAjaran = Semester::parseTahunAjaran($semesterId);

        // Arsipkan data tahun ajaran lama
        $archived = Guru::where('is_archived', false)
            ->where('tahun_ajaran', '!=', $tahunAjaran)
            ->count();

        Guru::where('is_archived', false)
            ->where('tahun_ajaran', '!=', $tahunAjaran)
            ->update(['is_archived' => true, 'tahun_arsip' => $tahunAjaran]);

        $result['archived'] = $archived;

        $rows = $this->fetchAll('getGtk');

        foreach ($rows as $data) {
            try {
                $nuptk = $data['nuptk'] ?? null;
                $nip   = $data['nip']   ?? null;
                $payload = $this->mapGuru($data);

                // Cari berdasarkan NUPTK dulu, lalu NIP
                $guru = null;
                if ($nuptk) $guru = Guru::withTrashed()->where('nuptk', $nuptk)->first();
                if (!$guru && $nip) $guru = Guru::withTrashed()->where('nip', $nip)->first();

                if ($guru) {
                    if ($guru->trashed()) $guru->restore();
                    $guru->update($payload);
                    $result['updated']++;
                } else {
                    Guru::create($payload);
                    $result['created']++;
                }
            } catch (\Exception $e) {
                $result['failed']++;
                $result['errors'][] = ($data['nama'] ?? '?') . ': ' . $e->getMessage();
                Log::error('Sync guru error: ' . $e->getMessage());
            }
        }

        $this->simpanLog('guru', $semesterId, $result, now()->diffInSeconds($start));
        return $result;
    }

    // ==========================================
// SYNC ROMBEL
// ==========================================
public function syncRombel(string $semesterId): array
{
    $start    = now();
    $result   = ['created' => 0, 'updated' => 0, 'failed' => 0, 'errors' => []];
    $semester = Semester::where('semester_id', $semesterId)->first();

    $rows = $this->fetchAll('getRombonganBelajar');

    foreach ($rows as $data) {
        if (($data['semester_id'] ?? '') !== $semesterId) continue;

        try {
            $rombelId = $data['rombongan_belajar_id'] ?? $data['id'] ?? null;

            // Deteksi jenis_rombel
            $jenisRombel    = (string)($data['jenis_rombel'] ?? '1');
            $jenisRombelStr = $data['jenis_rombel_str'] ?? 'Kelas';

            // Fallback jika nilai tidak dikenal
            if (!in_array($jenisRombel, ['1', '16', '51'])) {
                $namaLower = strtolower($data['nama'] ?? '');
                if (isset($data['id_ekskul']) || $jenisRombelStr === 'Ekstrakurikuler') {
                    $jenisRombel    = '51';
                    $jenisRombelStr = 'Ekstrakurikuler';
                } elseif (str_contains($namaLower, 'mp ') || str_contains($namaLower, 'mapel')) {
                    $jenisRombel    = '16';
                    $jenisRombelStr = 'Matapelajaran Pilihan';
                } else {
                    $jenisRombel    = '1';
                    $jenisRombelStr = 'Kelas';
                }
            }

            $payload = [
                'semester_id'          => $semester?->id,
                'rombongan_belajar_id' => $rombelId,
                'nama_rombel'          => $data['nama_rombel'] ?? $data['nama'] ?? '',
                'tingkat'              => $data['tingkat_pendidikan_id'] ?? null,
                'kurikulum'            => $data['kurikulum_id_str'] ?? null,
                'jenis_rombel'         => $jenisRombel,
                'jenis_rombel_str'     => $jenisRombelStr,
                'wali_kelas'           => $data['ptk_id_str'] ?? null,
                'ptk_id'               => $data['ptk_id'] ?? null,
                'is_archived'          => false,
            ];

            $rombel = Rombel::where('rombongan_belajar_id', $rombelId)->first();
            if ($rombel) {
                $rombel->update($payload);
                $result['updated']++;
            } else {
                Rombel::create($payload);
                $result['created']++;
            }
        } catch (\Exception $e) {
            $result['failed']++;
            $result['errors'][] = $e->getMessage();
        }
    }

    $this->simpanLog('rombel', $semesterId, $result, now()->diffInSeconds($start));
    return $result;
}

    // ==========================================
    // SYNC SEMUA
    // ==========================================
    public function syncSemua(string $semesterId): array
    {
        return [
            'semester' => $this->syncSemester(),
            'rombel'   => $this->syncRombel($semesterId),
            'guru'     => $this->syncGuru($semesterId),
            'siswa'    => $this->syncSiswa($semesterId),
        ];
    }

    // ==========================================
    // MAPPING DATA SISWA
    // ==========================================
    private function mapSiswa(array $d, ?int $semesterId): array
    {
        return [
            'semester_id'            => $semesterId,
            'nisn'                   => $d['nisn']                   ?? null,
            'nipd'                   => $d['nipd']                   ?? null,
            'peserta_didik_id'       => $d['peserta_didik_id']       ?? null,
            'registrasi_id'          => $d['registrasi_id']          ?? null,
            'nama'                   => $d['nama']                   ?? '',
            'jenis_kelamin'          => $d['jenis_kelamin']          ?? 'L',
            'nik'                    => $d['nik']                    ?? null,
            'tempat_lahir'           => $d['tempat_lahir']           ?? null,
            'tanggal_lahir'          => $d['tanggal_lahir']          ?? null,
            'agama'                  => $d['agama_id_str']           ?? null,
            'no_hp'                  => $d['nomor_telepon_seluler']  ?? null,
            'no_hp_rumah'            => $d['nomor_telepon_rumah']    ?? null,
            'email'                  => $d['email']                  ?? null,
            'tinggi_badan'           => $d['tinggi_badan']           ?? null,
            'berat_badan'            => $d['berat_badan']            ?? null,
            'kebutuhan_khusus'       => $d['kebutuhan_khusus']       ?? null,
            'nama_ayah'              => $d['nama_ayah']              ?? null,
            'pekerjaan_ayah'         => $d['pekerjaan_ayah_id_str']  ?? null,
            'nama_ibu'               => $d['nama_ibu']               ?? null,
            'pekerjaan_ibu'          => $d['pekerjaan_ibu_id_str']   ?? null,
            'nama_wali'              => $d['nama_wali']              ?? null,
            'anak_keberapa'          => $d['anak_keberapa']          ?? null,
            'nama_rombel'            => $d['nama_rombel']            ?? null,
            'rombongan_belajar_id'   => $d['rombongan_belajar_id']   ?? null,
            'tingkat_pendidikan_id'  => $d['tingkat_pendidikan_id']  ?? null,
            'kurikulum'              => $d['kurikulum_id_str']        ?? null,
            'sekolah_asal'           => $d['sekolah_asal']           ?? null,
            'tanggal_masuk_sekolah'  => $d['tanggal_masuk_sekolah']  ?? null,
            'jenis_pendaftaran'      => $d['jenis_pendaftaran_id_str'] ?? null,
            'is_archived'            => false,
            'sumber_data'            => 'dapodik',
            'last_sync_dapodik'      => now(),
        ];
    }

    // ==========================================
    // MAPPING DATA GURU
    // ==========================================
    private function mapGuru(array $d): array
    {
        $tahunAjaran = isset($d['tahun_ajaran_id'])
            ? $d['tahun_ajaran_id'] . '/' . ((int)$d['tahun_ajaran_id'] + 1)
            : null;

        return [
            'ptk_id'              => $d['ptk_id']                       ?? null,
            'ptk_terdaftar_id'    => $d['ptk_terdaftar_id']             ?? null,
            'nuptk'               => $d['nuptk']                        ?? null,
            'nip'                 => $d['nip']                          ?? null,
            'nik'                 => $d['nik']                          ?? null,
            'nama'                => $d['nama']                         ?? '',
            'jenis_kelamin'       => $d['jenis_kelamin']                ?? 'L',
            'tempat_lahir'        => $d['tempat_lahir']                 ?? null,
            'tanggal_lahir'       => $d['tanggal_lahir']                ?? null,
            'agama'               => $d['agama_id_str']                 ?? null,
            'jenis_ptk'           => $d['jenis_ptk_id_str']             ?? null,
            'jabatan'             => $d['jabatan_ptk_id_str']           ?? null,
            'status_kepegawaian'  => $d['status_kepegawaian_id_str']    ?? null,
            'pangkat_golongan'    => $d['pangkat_golongan_terakhir']    ?? null,
            'pendidikan_terakhir' => $d['pendidikan_terakhir']          ?? null,
            'bidang_studi'        => $d['bidang_studi_terakhir']        ?? null,
            'tahun_ajaran'        => $tahunAjaran,
            'tanggal_surat_tugas' => $d['tanggal_surat_tugas']          ?? null,
            'is_archived'         => false,
            'sumber_data'         => 'dapodik',
            'last_sync_dapodik'   => now(),
        ];
    }

    // ==========================================
    // SIMPAN LOG
    // ==========================================
    private function simpanLog(string $jenis, ?string $semesterId, array $result, int $durasi): void
    {
        $total  = ($result['created'] ?? 0) + ($result['updated'] ?? 0);
        $failed = $result['failed'] ?? 0;

        SyncLog::create([
            'user_id'      => auth()->id(),
            'jenis'        => $jenis,
            'semester_id'  => $semesterId,
            'total_data'   => $total,
            'created'      => $result['created']  ?? 0,
            'updated'      => $result['updated']  ?? 0,
            'archived'     => $result['archived'] ?? 0,
            'failed'       => $failed,
            'errors'       => $result['errors']   ?? [],
            'status'       => $failed === 0 ? 'sukses' : ($total > 0 ? 'sebagian' : 'gagal'),
            'durasi_detik' => $durasi,
        ]);
    }
}
