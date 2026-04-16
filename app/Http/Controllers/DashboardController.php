<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\AbsensiHarian;
use App\Models\AbsensiSiswa;
use App\Models\Semester;
use App\Models\Prestasi;
use App\Models\PrestasiSiswa;
use App\Models\Tiket;
use App\Models\JurnalMengajar;
use App\Models\PelanggaranSiswa;
use App\Models\Alumni;
use App\Models\PoinPositifSiswa;

class DashboardController extends Controller
{
    // TTL Cache (detik)
    const TTL_STATS    = 300;  // 5 menit  — total siswa/guru/rombel
    const TTL_HARIAN   = 60;   // 1 menit  — absensi hari ini, jurnal
    const TTL_SEMESTER = 600;  // 10 menit — prestasi, pelanggaran semester

    // ──────────────────────────────────────────────────────────────
    // SHARED HELPERS — dipanggil dari banyak role, di-cache bersama
    // ──────────────────────────────────────────────────────────────

    /** Stats umum: total siswa, guru, rombel, mapel — di-cache 5 menit */
    private function statsUmum(?int $semId): object
    {
        return Cache::remember("dash_stats_{$semId}", self::TTL_STATS, function () use ($semId) {
            // 2 query saja (count siswa + guru pakai DB::table agar ringan)
            [$ts, $tg, $ta, $tm] = [
                DB::table('siswas')->whereNull('deleted_at')->count(),
                DB::table('gurus')->whereNull('deleted_at')->count(),
                DB::table('alumnis')->count(),
                DB::table('mata_pelajaran')->count(),
            ];
            return (object)[
                'total_siswa'  => $ts,
                'total_guru'   => $tg,
                'total_rombel' => Rombel::where('semester_id', $semId)->count(),
                'total_mapel'  => $tm,
                'total_alumni' => $ta,
                'total_mutasi' => DB::table('siswas')->whereNotNull('deleted_at')->count(),
            ];
        });
    }

    /** Absensi hari ini — 1 query pakai GROUP BY, di-cache 1 menit */
    private function absensiHariIni(?int $semId): object
    {
        $key = "dash_absensi_hari_{$semId}_" . today()->format('Ymd');
        return Cache::remember($key, self::TTL_HARIAN, function () use ($semId) {
            $ids = AbsensiHarian::whereDate('tanggal', today())
                ->where('semester_id', $semId)->pluck('id');

            // 1 query GROUP BY, bukan 4 query terpisah
            $rekap = AbsensiSiswa::whereIn('absensi_harian_id', $ids)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            return (object)[
                'hadir' => $rekap['H'] ?? 0,
                'sakit' => $rekap['S'] ?? 0,
                'izin'  => $rekap['I'] ?? 0,
                'alpha' => $rekap['A'] ?? 0,
                'kelas' => $ids->count(),
            ];
        });
    }

    /** Grafik 7 hari — 2 query total (bukan 14 query!), di-cache 5 menit */
    private function absensi7Hari(?int $semId): \Illuminate\Support\Collection
    {
        $key = "dash_absensi_7hari_{$semId}_" . today()->format('Ymd');
        return Cache::remember($key, self::TTL_STATS, function () use ($semId) {
            $from = now()->subDays(6)->startOfDay();
            $to   = now()->endOfDay();

            // Query 1: ambil semua ID absensi harian 7 hari
            $harians = AbsensiHarian::where('semester_id', $semId)
                ->whereBetween('tanggal', [$from, $to])
                ->pluck('id', DB::raw('DATE(tanggal)'));

            // Query 2: GROUP BY tanggal + status sekaligus
            $rekap = AbsensiSiswa::whereIn('absensi_harian_id', $harians->values())
                ->join('absensi_harian', 'absensi_siswa.absensi_harian_id', '=', 'absensi_harian.id')
                ->selectRaw('DATE(absensi_harian.tanggal) as tgl, status, COUNT(*) as total')
                ->groupBy('tgl', 'status')
                ->get()
                ->groupBy('tgl');

            return collect(range(6, 0))->map(function ($i) use ($rekap) {
                $tgl  = now()->subDays($i)->format('Y-m-d');
                $data = $rekap->get($tgl, collect())->pluck('total', 'status');
                return (object)[
                    'label' => now()->subDays($i)->translatedFormat('D'),
                    'hadir' => $data['H'] ?? 0,
                    'alpha' => $data['A'] ?? 0,
                ];
            });
        });
    }

    /** Prestasi stats — di-cache 10 menit */
    private function prestasiStats(?int $semId): object
    {
        return Cache::remember("dash_prestasi_{$semId}", self::TTL_SEMESTER, function () use ($semId) {
            // 1 query pakai CASE WHEN
            $rekap = Prestasi::where('semester_id', $semId)
                ->selectRaw("
                    COUNT(CASE WHEN status='diverifikasi' THEN 1 END) as total,
                    COUNT(CASE WHEN status='pending' THEN 1 END) as pending,
                    COUNT(CASE WHEN status='diverifikasi' AND tingkat IN ('nasional','internasional') THEN 1 END) as nasional,
                    COUNT(CASE WHEN status='diverifikasi' AND tingkat='provinsi' THEN 1 END) as provinsi
                ")->first();
            return (object)[
                'total'    => $rekap->total    ?? 0,
                'pending'  => $rekap->pending  ?? 0,
                'nasional' => $rekap->nasional ?? 0,
                'provinsi' => $rekap->provinsi ?? 0,
            ];
        });
    }

    /** Tiket stats — di-cache 1 menit */
    private function tiketStats(): object
    {
        return Cache::remember('dash_tiket_stats', self::TTL_HARIAN, function () {
            // 1 query pakai CASE WHEN
            $rekap = Tiket::selectRaw("
                COUNT(CASE WHEN status IN ('open','diproses') THEN 1 END) as open,
                COUNT(CASE WHEN status='terkunci' THEN 1 END) as terkunci,
                COUNT(CASE WHEN status='selesai' THEN 1 END) as selesai,
                COUNT(CASE WHEN status IN ('open','diproses') AND prioritas='tinggi' THEN 1 END) as tinggi
            ")->first();
            return (object)[
                'open'     => $rekap->open     ?? 0,
                'terkunci' => $rekap->terkunci ?? 0,
                'selesai'  => $rekap->selesai  ?? 0,
                'tinggi'   => $rekap->tinggi   ?? 0,
            ];
        });
    }

    // ──────────────────────────────────────────────────────────────
    // ADMIN
    // ──────────────────────────────────────────────────────────────
    public function admin()
    {
        $sem           = Semester::aktif();
        $stats         = $this->statsUmum($sem?->id);
        $absensiStats  = $this->absensiHariIni($sem?->id);
        $prestasiStats = $this->prestasiStats($sem?->id);
        $tiketStats    = $this->tiketStats();
        $absensi7Hari  = $this->absensi7Hari($sem?->id);

        $jurnalHariIni = Cache::remember(
            'dash_jurnal_hari_' . today()->format('Ymd'), self::TTL_HARIAN,
            fn() => JurnalMengajar::whereDate('tanggal', today())->count()
        );

        $pelanggaranBulanIni = Cache::remember(
            'dash_pelanggaran_bulan_' . now()->format('Ym'), self::TTL_STATS,
            fn() => PelanggaranSiswa::whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)->count()
        );

        // Feed terbaru — real-time, tidak di-cache
        $tiketTerbaru    = Tiket::with('user')
            ->whereIn('status', ['open', 'diproses'])
            ->orderByDesc('created_at')->limit(5)->get();

        $prestasiTerbaru = Prestasi::with(['kategori', 'siswas'])
            ->where('semester_id', $sem?->id)->where('status', 'diverifikasi')
            ->orderByDesc('created_at')->limit(5)->get();

        return view('dashboard.admin', compact(
            'sem', 'stats', 'absensiStats', 'prestasiStats', 'tiketStats',
            'jurnalHariIni', 'pelanggaranBulanIni',
            'tiketTerbaru', 'prestasiTerbaru', 'absensi7Hari'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    // KEPALA SEKOLAH
    // ──────────────────────────────────────────────────────────────
    public function kepalaSekolah()
    {
        $sem           = Semester::aktif();
        $stats         = $this->statsUmum($sem?->id);
        $absensiStats  = $this->absensiHariIni($sem?->id);
        $prestasiStats = $this->prestasiStats($sem?->id);
        $tiketStats    = $this->tiketStats();
        $absensi7Hari  = $this->absensi7Hari($sem?->id);

        $pelanggaranBulanIni = Cache::remember(
            'dash_pelanggaran_bulan_' . now()->format('Ym'), self::TTL_STATS,
            fn() => PelanggaranSiswa::whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)->count()
        );

        // Feed real-time
        $tiketPrioritasTinggi = Tiket::with('user')
            ->whereIn('status', ['open', 'diproses'])->where('prioritas', 'tinggi')
            ->orderByDesc('created_at')->limit(5)->get();

        $prestasiTerbaru = Prestasi::with(['kategori', 'siswas'])
            ->where('semester_id', $sem?->id)->where('status', 'diverifikasi')
            ->whereIn('tingkat', ['nasional', 'internasional', 'provinsi'])
            ->orderByDesc('created_at')->limit(5)->get();

        return view('dashboard.kepala_sekolah', compact(
            'sem', 'stats', 'absensiStats', 'prestasiStats', 'tiketStats',
            'pelanggaranBulanIni', 'tiketPrioritasTinggi', 'prestasiTerbaru', 'absensi7Hari'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    // WAKIL KEPALA
    // ──────────────────────────────────────────────────────────────
    public function wakilKepala()
    {
        $sem            = Semester::aktif();
        $stats          = $this->statsUmum($sem?->id);
        $absensiHariIni = $this->absensiHariIni($sem?->id);
        $absensi7Hari   = $this->absensi7Hari($sem?->id);
        $prestasiStats  = $this->prestasiStats($sem?->id);

        $jurnalStats = Cache::remember(
            'dash_jurnal_stats_' . now()->format('Ym'), self::TTL_HARIAN,
            function () {
                // 1 query CASE WHEN
                $r = JurnalMengajar::selectRaw("
                    COUNT(CASE WHEN DATE(tanggal)=CURDATE() THEN 1 END) as hari_ini,
                    COUNT(CASE WHEN MONTH(tanggal)=MONTH(NOW()) AND YEAR(tanggal)=YEAR(NOW()) THEN 1 END) as bulan_ini
                ")->first();
                return (object)['hari_ini' => $r->hari_ini ?? 0, 'bulan_ini' => $r->bulan_ini ?? 0];
            }
        );

        $pelanggaranStats = Cache::remember(
            'dash_pel_stats_wk_' . $sem?->id . '_' . now()->format('Ym'), self::TTL_STATS,
            function () use ($sem) {
                $r = PelanggaranSiswa::selectRaw("
                    COUNT(CASE WHEN MONTH(tanggal)=MONTH(NOW()) AND YEAR(tanggal)=YEAR(NOW()) THEN 1 END) as bulan_ini,
                    COUNT(CASE WHEN semester_id=? THEN 1 END) as semester
                ", [$sem?->id])->first();
                return (object)['bulan_ini' => $r->bulan_ini ?? 0, 'semester' => $r->semester ?? 0];
            }
        );

        return view('dashboard.wakil_kepala', compact(
            'sem', 'stats', 'absensiHariIni', 'jurnalStats', 'pelanggaranStats', 'prestasiStats', 'absensi7Hari'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    // GURU — cache per user_id
    // ──────────────────────────────────────────────────────────────
    public function guru()
    {
        $user = Auth::user();
        $sem  = Semester::aktif();
        $guru = Guru::where('user_id', $user->id)->first();
        $gId  = $guru?->id;

        $jurnalStats = Cache::remember(
            "dash_jurnal_guru_{$gId}_" . now()->format('Ym'), self::TTL_HARIAN,
            function () use ($gId, $sem) {
                $r = JurnalMengajar::where('guru_id', $gId)->selectRaw("
                    COUNT(CASE WHEN DATE(tanggal)=CURDATE() THEN 1 END) as hari_ini,
                    COUNT(CASE WHEN MONTH(tanggal)=MONTH(NOW()) AND YEAR(tanggal)=YEAR(NOW()) THEN 1 END) as bulan_ini,
                    COUNT(CASE WHEN semester_id=? THEN 1 END) as semester
                ", [$sem?->id])->first();
                return (object)[
                    'hari_ini'  => $r->hari_ini  ?? 0,
                    'bulan_ini' => $r->bulan_ini ?? 0,
                    'semester'  => $r->semester  ?? 0,
                ];
            }
        );

        $absensiStats = Cache::remember(
            "dash_absensi_guru_{$gId}_" . now()->format('Ym'), self::TTL_HARIAN,
            function () use ($gId) {
                $r = AbsensiHarian::where('guru_id', $gId)->selectRaw("
                    COUNT(CASE WHEN DATE(tanggal)=CURDATE() THEN 1 END) as hari_ini,
                    COUNT(CASE WHEN MONTH(tanggal)=MONTH(NOW()) THEN 1 END) as bulan_ini
                ")->first();
                return (object)['hari_ini' => $r->hari_ini ?? 0, 'bulan_ini' => $r->bulan_ini ?? 0];
            }
        );

        $prestasiStats = Cache::remember(
            "dash_prestasi_guru_{$user->id}_{$sem?->id}", self::TTL_SEMESTER,
            function () use ($user, $sem) {
                $r = Prestasi::where('dibuat_oleh', $user->id)->selectRaw("
                    COUNT(CASE WHEN semester_id=? THEN 1 END) as total,
                    COUNT(CASE WHEN status='pending' THEN 1 END) as pending
                ", [$sem?->id])->first();
                return (object)['total' => $r->total ?? 0, 'pending' => $r->pending ?? 0];
            }
        );

        $tiketStats = Cache::remember(
            "dash_tiket_user_{$user->id}", self::TTL_HARIAN,
            function () use ($user) {
                $r = Tiket::where('user_id', $user->id)->selectRaw("
                    COUNT(CASE WHEN status IN ('open','diproses') THEN 1 END) as open,
                    COUNT(CASE WHEN status='selesai' THEN 1 END) as selesai
                ")->first();
                return (object)['open' => $r->open ?? 0, 'selesai' => $r->selesai ?? 0];
            }
        );

        // Feed terbaru — real-time
        $jurnalTerbaru = JurnalMengajar::where('guru_id', $gId)
            ->with('mataPelajaran')->orderByDesc('tanggal')->limit(5)->get();

        return view('dashboard.guru', compact(
            'sem', 'guru', 'jurnalStats', 'absensiStats', 'prestasiStats', 'tiketStats', 'jurnalTerbaru'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    // BK
    // ──────────────────────────────────────────────────────────────
    public function bk()
    {
        $sem = Semester::aktif();

        $pelanggaranStats = Cache::remember(
            "dash_pel_bk_{$sem?->id}_" . now()->format('Ym'), self::TTL_STATS,
            function () use ($sem) {
                $r = PelanggaranSiswa::selectRaw("
                    COUNT(CASE WHEN DATE(tanggal)=CURDATE() THEN 1 END) as hari_ini,
                    COUNT(CASE WHEN MONTH(tanggal)=MONTH(NOW()) AND YEAR(tanggal)=YEAR(NOW()) THEN 1 END) as bulan_ini,
                    COUNT(CASE WHEN semester_id=? THEN 1 END) as semester
                ", [$sem?->id])->first();
                return (object)[
                    'hari_ini'  => $r->hari_ini  ?? 0,
                    'bulan_ini' => $r->bulan_ini ?? 0,
                    'semester'  => $r->semester  ?? 0,
                ];
            }
        );

        $siswaPoinTertinggi = Cache::remember(
            "dash_poin_tertinggi_{$sem?->id}", self::TTL_STATS,
            fn() => PelanggaranSiswa::with('siswa')
                ->where('semester_id', $sem?->id)
                ->selectRaw('siswa_id, SUM(poin) as total_poin')
                ->groupBy('siswa_id')->orderByDesc('total_poin')->limit(5)->get()
        );

        $rekapJenis = Cache::remember(
            "dash_rekap_jenis_{$sem?->id}", self::TTL_STATS,
            fn() => PelanggaranSiswa::with('jenisPelanggaran')
                ->where('semester_id', $sem?->id)
                ->selectRaw('jenis_pelanggaran_id, COUNT(*) as total, SUM(poin) as total_poin')
                ->groupBy('jenis_pelanggaran_id')->orderByDesc('total')->limit(5)->get()
        );

        $prestasiStats = $this->prestasiStats($sem?->id);
        $tiketStats    = $this->tiketStats();

        $pelanggaran7Hari = Cache::remember(
            'dash_pel_7hari_' . today()->format('Ymd'), self::TTL_STATS,
            function () {
                // 1 query saja
                $data = PelanggaranSiswa::whereBetween('tanggal', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
                    ->selectRaw('DATE(tanggal) as tgl, COUNT(*) as total')
                    ->groupBy('tgl')->pluck('total', 'tgl');

                return collect(range(6, 0))->map(function ($i) use ($data) {
                    $tgl = now()->subDays($i)->format('Y-m-d');
                    return (object)[
                        'label' => now()->subDays($i)->translatedFormat('D'),
                        'total' => $data[$tgl] ?? 0,
                    ];
                });
            }
        );

        return view('dashboard.bk', compact(
            'sem', 'pelanggaranStats', 'siswaPoinTertinggi', 'rekapJenis',
            'prestasiStats', 'tiketStats', 'pelanggaran7Hari'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    // TATA USAHA
    // ──────────────────────────────────────────────────────────────
    public function tataUsaha()
    {
        $sem           = Semester::aktif();
        $stats         = $this->statsUmum($sem?->id);
        $prestasiStats = $this->prestasiStats($sem?->id);
        $tiketStats    = $this->tiketStats();

        $prestasiTerbaru = Prestasi::with(['kategori', 'siswas'])
            ->where('semester_id', $sem?->id)->where('status', 'diverifikasi')
            ->orderByDesc('created_at')->limit(5)->get();

        return view('dashboard.tata_usaha', compact(
            'sem', 'stats', 'prestasiStats', 'tiketStats', 'prestasiTerbaru'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    // SISWA — cache per siswa_id
    // ──────────────────────────────────────────────────────────────
    public function siswa()
    {
        $user  = Auth::user();
        $sem   = Semester::aktif();
        $siswa = Siswa::where('user_id', $user->id)->first();
        $sId   = $siswa?->id;

        $absensiSaya = Cache::remember(
            "dash_absensi_siswa_{$sId}_{$sem?->id}", self::TTL_HARIAN,
            function () use ($sId, $sem) {
                if (!$sId) return (object)['hadir'=>0,'sakit'=>0,'izin'=>0,'alpha'=>0,'dispensasi'=>0];
                $semIds = AbsensiHarian::where('semester_id', $sem?->id)->pluck('id');
                $rekap  = AbsensiSiswa::where('siswa_id', $sId)
                    ->whereIn('absensi_harian_id', $semIds)
                    ->selectRaw('status, COUNT(*) as total')
                    ->groupBy('status')->pluck('total', 'status');
                return (object)[
                    'hadir'      => $rekap['H'] ?? 0,
                    'sakit'      => $rekap['S'] ?? 0,
                    'izin'       => $rekap['I'] ?? 0,
                    'alpha'      => $rekap['A'] ?? 0,
                    'dispensasi' => $rekap['D'] ?? 0,
                ];
            }
        );

        $prestasiStats = Cache::remember(
            "dash_prestasi_siswa_{$sId}_{$sem?->id}", self::TTL_SEMESTER,
            fn() => (object)[
                'total'    => PrestasiSiswa::where('siswa_id', $sId)->whereHas('prestasi', fn($q) => $q->where('status','diverifikasi'))->count(),
                'semester' => PrestasiSiswa::where('siswa_id', $sId)->whereHas('prestasi', fn($q) => $q->where('semester_id', $sem?->id)->where('status','diverifikasi'))->count(),
            ]
        );

        $pelanggaranStats = Cache::remember(
            "dash_pel_siswa_{$sId}_{$sem?->id}", self::TTL_HARIAN,
            function () use ($sId, $sem) {
                $poinPelanggaran = PelanggaranSiswa::where('siswa_id', $sId)
                    ->where('semester_id', $sem?->id)
                    ->where('status', '!=', 'dibatalkan')
                    ->sum('poin');
                $poinPositif = PoinPositifSiswa::where('siswa_id', $sId)
                    ->where('semester_id', $sem?->id)
                    ->where('status', 'aktif')
                    ->sum('poin');
                $kasus = PelanggaranSiswa::where('siswa_id', $sId)
                    ->where('semester_id', $sem?->id)
                    ->where('status', '!=', 'dibatalkan')
                    ->count();
                return (object)[
                    'semester'     => $kasus,
                    'poin'         => $poinPelanggaran,
                    'poin_positif' => $poinPositif,
                    'net_poin'     => max(0, $poinPelanggaran - $poinPositif),
                ];
            }
        );

        $tiketStats = Cache::remember(
            "dash_tiket_user_{$user->id}", self::TTL_HARIAN,
            function () use ($user) {
                $r = Tiket::where('user_id', $user->id)->selectRaw("
                    COUNT(CASE WHEN status IN ('open','diproses') THEN 1 END) as open,
                    COUNT(CASE WHEN status='selesai' THEN 1 END) as selesai
                ")->first();
                return (object)['open' => $r->open ?? 0, 'selesai' => $r->selesai ?? 0];
            }
        );

        // Feed real-time — tidak di-cache
        $prestasiSaya = PrestasiSiswa::where('siswa_id', $sId)
            ->whereHas('prestasi', fn($q) => $q->where('semester_id', $sem?->id)->where('status','diverifikasi'))
            ->with('prestasi.kategori')->orderByDesc('created_at')->limit(5)->get();

        $tiketTerbaru = Tiket::where('user_id', $user->id)->orderByDesc('created_at')->limit(3)->get();

        return view('dashboard.siswa', compact(
            'sem', 'siswa', 'absensiSaya', 'prestasiSaya', 'prestasiStats',
            'pelanggaranStats', 'tiketStats', 'tiketTerbaru'
        ));
    }
}
