<?php
// app/Http/Controllers/Admin/SemesterController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Rombel;
use App\Models\Semester;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SemesterController extends Controller
{
    // ── WIZARD VIEW ──
    public function wizard()
    {
        $semesterAktif  = Semester::aktif();
        $semesters      = Semester::orderByDesc('semester_id')->get();

        $rombelAktif = Rombel::where('is_archived', false)
            ->where('jenis_rombel', '1')
            ->when($semesterAktif, fn($q) => $q->where('semester_id', $semesterAktif->id))
            ->orderBy('tingkat')->orderBy('nama_rombel')
            ->get()
            ->map(function($rombel) {
                $rombel->jumlah_siswa = Siswa::where('rombongan_belajar_id', $rombel->rombongan_belajar_id)
                    ->where('is_archived', false)
                    ->whereNotIn('status_mutasi', ['lulus', 'mutasi', 'keluar'])
                    ->count();
                return $rombel;
            });

        $rombelXIIIds = Rombel::where('tingkat', 12)
            ->where('is_archived', false)
            ->pluck('rombongan_belajar_id');

        $siswaXII = Siswa::where('is_archived', false)
            ->where('status_mutasi', 'aktif')
            ->whereIn('rombongan_belajar_id', $rombelXIIIds)
            ->count();

        $stats = [
            'total_siswa'  => Siswa::where('is_archived', false)->where('status_mutasi', 'aktif')->count(),
            'total_rombel' => $rombelAktif->count(),
            'siswa_xii'    => $siswaXII,
        ];

        return view('admin.semester.wizard', compact(
            'semesterAktif', 'semesters', 'rombelAktif', 'stats'
        ));
    }

    // ── TAMBAH SEMESTER MANUAL ──────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'tipe'            => 'required|in:ganjil,genap',
            'tahun_ajaran'    => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ], [
            'tahun_ajaran.regex'          => 'Format tahun ajaran harus YYYY/YYYY, contoh: 2025/2026',
            'tanggal_selesai.after'       => 'Tanggal selesai harus setelah tanggal mulai',
        ]);

        // Generate semester_id otomatis dari tahun ajaran + tipe
        // Format Dapodik: tahun awal + tipe (1=ganjil, 2=genap)
        $tahunAwal   = substr($request->tahun_ajaran, 0, 4);
        $tipeAngka   = $request->tipe === 'ganjil' ? '1' : '2';
        $semesterId  = $tahunAwal . $tipeAngka;

        // Cek duplikat
        if (Semester::where('semester_id', $semesterId)->exists()) {
            return back()->with('error', "Semester {$request->tahun_ajaran} " . ucfirst($request->tipe) . " sudah ada!");
        }

        $nama = 'Semester ' . ucfirst($request->tipe) . ' ' . $request->tahun_ajaran;

        Semester::create([
            'semester_id'     => $semesterId,
            'nama'            => $nama,
            'tahun_ajaran'    => $request->tahun_ajaran,
            'tipe'            => $request->tipe,
            'is_aktif'        => false,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        return back()->with('success', "Semester {$nama} berhasil ditambahkan!");
    }

    // ── HAPUS SEMESTER (hanya yang tidak aktif & tidak punya data) ──
    public function destroy(Semester $semester)
    {
        if ($semester->is_aktif) {
            return back()->with('error', 'Semester aktif tidak bisa dihapus!');
        }

        // Cek apakah ada data terkait
        $hasRombel = Rombel::where('semester_id', $semester->id)->exists();
        if ($hasRombel) {
            return back()->with('error', 'Semester tidak bisa dihapus karena masih memiliki data rombel!');
        }

        $semester->delete();
        return back()->with('success', 'Semester berhasil dihapus!');
    }

    // ── SET AKTIF ──────────────────────────────────────────────
    public function setAktif(Semester $semester)
    {
        Semester::query()->update(['is_aktif' => false]);
        $semester->update(['is_aktif' => true]);

        return back()->with('success', "Semester {$semester->nama} berhasil diaktifkan!");
    }

    // ── STEP 1A: Ganti Semester (Ganjil ↔ Genap) ──
    public function gantiSemester(Request $request)
    {
        $request->validate([
            'semester_id_baru' => 'required|exists:semesters,id',
        ]);

        $semesterLama = Semester::aktif();
        $semesterBaru = Semester::findOrFail($request->semester_id_baru);

        if ($semesterLama && $semesterLama->id === $semesterBaru->id) {
            return response()->json([
                'status'  => false,
                'message' => 'Semester yang dipilih sudah aktif saat ini.',
            ]);
        }

        DB::transaction(function () use ($semesterLama, $semesterBaru, $request) {
            if ($semesterLama && $request->arsip_rombel) {
                Rombel::where('semester_id', $semesterLama->id)
                    ->where('is_archived', false)
                    ->update(['is_archived' => true]);
            }

            Semester::query()->update(['is_aktif' => false]);
            $semesterBaru->update(['is_aktif' => true]);
        });

        return response()->json([
            'status'  => true,
            'message' => 'Semester berhasil diganti ke <strong>' . $semesterBaru->nama . '</strong>.' .
                         ($request->arsip_rombel ? ' Rombel semester lama telah diarsipkan.' : ''),
        ]);
    }

    // ── STEP 1B: Preview Kelulusan Massal ──
    public function previewKelulusan(Request $request)
    {
        $semesterAktif = Semester::aktif();

        $rombelXII = Rombel::where('is_archived', false)
            ->where('jenis_rombel', '1')
            ->when($semesterAktif, fn($q) => $q->where('semester_id', $semesterAktif->id))
            ->where('tingkat', 12)
            ->orderBy('nama_rombel')
            ->withCount(['siswas as jumlah_siswa' => fn($q) => $q->where('is_archived', false)
                ->whereNotIn('status_mutasi', ['lulus', 'mutasi', 'keluar'])])
            ->get();

        return response()->json([
            'status'   => true,
            'rombel'   => $rombelXII,
            'semester' => $semesterAktif,
        ]);
    }

    // ── STEP 2B: Eksekusi Naik Kelas / Tahun Ajaran Baru ──
    public function naikKelas(Request $request)
    {
        $request->validate([
            'semester_id_baru' => 'required|exists:semesters,id',
            'tahun_lulus'      => 'required|string',
            'tanggal_lulus'    => 'required|date',
            'arsip_rombel'     => 'nullable|boolean',
        ]);

        $semesterLama = Semester::aktif();
        $semesterBaru = Semester::findOrFail($request->semester_id_baru);
        $countLulus   = 0;

        DB::transaction(function () use ($semesterLama, $semesterBaru, $request, &$countLulus) {

            $rombelXIIIds = Rombel::where('tingkat', 12)
                ->where('is_archived', false)
                ->pluck('rombongan_belajar_id');

            $siswasXII = Siswa::where('is_archived', false)
                ->where('status_mutasi', 'aktif')
                ->whereIn('rombongan_belajar_id', $rombelXIIIds)
                ->get();

            foreach ($siswasXII as $siswa) {
                Alumni::updateOrCreate(
                    ['nisn' => $siswa->nisn, 'tahun_lulus' => $request->tahun_lulus],
                    [
                        'semester_id'           => $siswa->semester_id,
                        'siswa_id'              => $siswa->id,
                        'peserta_didik_id'      => $siswa->peserta_didik_id,
                        'nisn'                  => $siswa->nisn,
                        'nipd'                  => $siswa->nipd,
                        'nama'                  => $siswa->nama,
                        'jenis_kelamin'         => $siswa->jenis_kelamin,
                        'nik'                   => $siswa->nik,
                        'tempat_lahir'          => $siswa->tempat_lahir,
                        'tanggal_lahir'         => $siswa->tanggal_lahir,
                        'agama'                 => $siswa->agama,
                        'nama_rombel'           => $siswa->nama_rombel,
                        'tingkat_pendidikan_id' => $siswa->tingkat_pendidikan_id,
                        'kurikulum'             => $siswa->kurikulum,
                        'sekolah_asal'          => $siswa->sekolah_asal,
                        'tahun_lulus'           => $request->tahun_lulus,
                        'tanggal_lulus'         => $request->tanggal_lulus,
                        'nama_ayah'             => $siswa->nama_ayah,
                        'nama_ibu'              => $siswa->nama_ibu,
                        'nama_wali'             => $siswa->nama_wali,
                        'no_hp_ortu'            => $siswa->no_hp_ortu,
                        'no_hp'                 => $siswa->no_hp,
                        'email'                 => $siswa->email,
                        'sumber_data'           => 'dapodik',
                    ]
                );

                $siswa->update([
                    'status_mutasi'     => 'lulus',
                    'tanggal_mutasi'    => $request->tanggal_lulus,
                    'keterangan_mutasi' => 'Lulus tahun ' . $request->tahun_lulus,
                ]);

                $countLulus++;
            }

            if ($semesterLama && $request->arsip_rombel != false) {
                Rombel::where('semester_id', $semesterLama->id)
                    ->where('is_archived', false)
                    ->update(['is_archived' => true]);
            }

            Semester::query()->update(['is_aktif' => false]);
            $semesterBaru->update(['is_aktif' => true]);
        });

        return response()->json([
            'status'  => true,
            'message' => 'Berhasil! <strong>' . $countLulus . ' siswa</strong> diluluskan. ' .
                         'Semester aktif sekarang: <strong>' . $semesterBaru->nama . '</strong>. ' .
                         'Silakan tarik data Dapodik untuk mengisi data siswa baru.',
            'lulus'   => $countLulus,
        ]);
    }
}
