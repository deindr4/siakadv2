<?php

namespace App\Http\Controllers;

use App\Models\AbsensiHarian;
use App\Models\AbsensiSiswa;
use App\Models\Rombel;
use App\Models\Semester;
use App\Models\Siswa;
use App\Exports\AbsensiRekapExport;
use App\Exports\AbsensiSiswaExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanAbsensiController extends Controller
{
    // ============================================================
    // REKAP PER KELAS PER BULAN
    // ============================================================
    public function rekapKelas(Request $request)
    {
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);

        $rombels = Rombel::where('is_archived', false)
            ->where('jenis_rombel', '1')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->orderBy('tingkat')->orderBy('nama_rombel')->get();

        // Rekap per kelas per bulan
        $rekap = AbsensiHarian::with('semester')
            ->selectRaw('
                rombongan_belajar_id,
                nama_rombel,
                MONTH(tanggal) as bulan,
                YEAR(tanggal) as tahun,
                COUNT(*) as total_hari,
                SUM((SELECT COUNT(*) FROM absensi_siswa WHERE absensi_harian_id = absensi_harian.id AND status = "H")) as hadir,
                SUM((SELECT COUNT(*) FROM absensi_siswa WHERE absensi_harian_id = absensi_harian.id AND status = "S")) as sakit,
                SUM((SELECT COUNT(*) FROM absensi_siswa WHERE absensi_harian_id = absensi_harian.id AND status = "I")) as izin,
                SUM((SELECT COUNT(*) FROM absensi_siswa WHERE absensi_harian_id = absensi_harian.id AND status = "A")) as alpa,
                SUM((SELECT COUNT(*) FROM absensi_siswa WHERE absensi_harian_id = absensi_harian.id AND status = "D")) as dispensasi
            ')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->when($request->filled('rombel_id'), fn($q) => $q->where('rombongan_belajar_id', $request->rombel_id))
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal', $request->bulan))
            ->groupBy('rombongan_belajar_id', 'nama_rombel', DB::raw('MONTH(tanggal)'), DB::raw('YEAR(tanggal)'))
            ->orderBy('nama_rombel')
            ->orderBy(DB::raw('YEAR(tanggal)'))
            ->orderBy(DB::raw('MONTH(tanggal)'))
            ->get();

        $semester  = Semester::find($semesterId);
        $isAdmin   = auth()->user()->hasRole('admin');

        return view('laporan.absensi.rekap-kelas', compact(
            'rekap', 'rombels', 'semesters', 'semesterAktif',
            'semesterId', 'semester', 'isAdmin'
        ));
    }

    // ============================================================
    // REKAP PER KELAS - PDF
    // ============================================================
    public function rekapKelasPdf(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $semester   = Semester::find($semesterId);
        $bulan      = $request->get('bulan');
        $rombelId   = $request->get('rombel_id');

        $rekap = AbsensiHarian::selectRaw('
                rombongan_belajar_id,
                nama_rombel,
                MONTH(tanggal) as bulan,
                YEAR(tanggal) as tahun,
                COUNT(*) as total_hari,
                SUM((SELECT COUNT(*) FROM absensi_siswa WHERE absensi_harian_id = absensi_harian.id AND status = "H")) as hadir,
                SUM((SELECT COUNT(*) FROM absensi_siswa WHERE absensi_harian_id = absensi_harian.id AND status = "S")) as sakit,
                SUM((SELECT COUNT(*) FROM absensi_siswa WHERE absensi_harian_id = absensi_harian.id AND status = "I")) as izin,
                SUM((SELECT COUNT(*) FROM absensi_siswa WHERE absensi_harian_id = absensi_harian.id AND status = "A")) as alpa,
                SUM((SELECT COUNT(*) FROM absensi_siswa WHERE absensi_harian_id = absensi_harian.id AND status = "D")) as dispensasi
            ')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->when($rombelId, fn($q) => $q->where('rombongan_belajar_id', $rombelId))
            ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
            ->groupBy('rombongan_belajar_id', 'nama_rombel', DB::raw('MONTH(tanggal)'), DB::raw('YEAR(tanggal)'))
            ->orderBy('nama_rombel')
            ->orderBy(DB::raw('MONTH(tanggal)'))
            ->get();

        $settings  = \App\Models\Setting::allAsArray();
        $ttd       = $request->only(['nama_kepsek', 'nip_kepsek', 'golongan_kepsek', 'tempat_ttd', 'tanggal_ttd']);

        $pdf = Pdf::loadView('laporan.absensi.rekap-kelas-pdf', compact(
            'rekap', 'semester', 'bulan', 'settings', 'ttd'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('rekap-absensi-kelas.pdf');
    }

    // ============================================================
    // REKAP PER KELAS - EXCEL
    // ============================================================
    public function rekapKelasExcel(Request $request)
    {
        return Excel::download(
            new AbsensiRekapExport($request->all()),
            'rekap-absensi-kelas-' . date('Ymd') . '.xlsx'
        );
    }

    // ============================================================
    // DETAIL ABSENSI HARIAN PER KELAS
    // ============================================================
    public function detailKelas(Request $request)
    {
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);
        $rombelId      = $request->get('rombel_id');

        $rombels = Rombel::where('is_archived', false)
            ->where('jenis_rombel', '1')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->orderBy('tingkat')->orderBy('nama_rombel')->get();

        $absensiList = collect();
        $rombel      = null;
        $siswas      = collect();

        if ($rombelId) {
            $rombel = Rombel::find($rombelId);

            // Ambil semua tanggal absensi kelas ini
            $absensiList = AbsensiHarian::where('rombongan_belajar_id', $rombelId)
                ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
                ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal', $request->bulan))
                ->orderBy('tanggal')
                ->get();

            // Ambil siswa kelas ini
            $siswas = Siswa::where('rombongan_belajar_id', $rombel->rombongan_belajar_id)
                ->where(function ($q) {
                    $q->where('is_archived', false)->orWhereNull('is_archived');
                })
                ->whereNotIn('status', ['mutasi', 'keluar'])
                ->orderBy('nama')
                ->get();
        }

        $semester = Semester::find($semesterId);
        $isAdmin  = auth()->user()->hasRole('admin');

        return view('laporan.absensi.detail-kelas', compact(
            'absensiList', 'rombels', 'rombel', 'siswas',
            'semesters', 'semesterAktif', 'semesterId', 'semester',
            'rombelId', 'isAdmin'
        ));
    }

    // ============================================================
    // DETAIL KELAS - PDF (daftar hadir format tabel besar)
    // ============================================================
    public function detailKelasPdf(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $rombelId   = $request->get('rombel_id');
        $bulan      = $request->get('bulan');

        $rombel = Rombel::find($rombelId);
        if (!$rombel) abort(404);

        $absensiList = AbsensiHarian::where('rombongan_belajar_id', $rombelId)
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
            ->with('absensiSiswa')
            ->orderBy('tanggal')
            ->get();

        $siswas = Siswa::where('rombongan_belajar_id', $rombel->rombongan_belajar_id)
            ->where(function ($q) {
                $q->where('is_archived', false)->orWhereNull('is_archived');
            })
            ->whereNotIn('status', ['mutasi', 'keluar'])
            ->orderBy('nama')
            ->get();

        $semester = Semester::find($semesterId);
        $settings = \App\Models\Setting::allAsArray();
        $ttd      = $request->only(['nama_kepsek', 'nip_kepsek', 'golongan_kepsek',
                                    'nama_wali', 'nip_wali', 'tempat_ttd', 'tanggal_ttd']);

        $pdf = Pdf::loadView('laporan.absensi.detail-kelas-pdf', compact(
            'absensiList', 'siswas', 'rombel', 'semester', 'bulan', 'settings', 'ttd'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('detail-absensi-' . $rombel->nama_rombel . '.pdf');
    }

    // ============================================================
    // REKAP PER SISWA PER SEMESTER
    // ============================================================
    public function rekapSiswa(Request $request)
    {
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);

        $rombels = Rombel::where('is_archived', false)
            ->where('jenis_rombel', '1')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->orderBy('tingkat')->orderBy('nama_rombel')->get();

        $rekapSiswa = collect();
        $rombelId   = $request->get('rombel_id');
        $sudahFilter = $request->filled('rombel_id'); // hanya tampil jika kelas dipilih

        if ($sudahFilter) {
            $rombel = Rombel::find($rombelId);

            $siswas = Siswa::when($rombel, fn($q) => $q->where('rombongan_belajar_id', $rombel->rombongan_belajar_id))
                ->where(function ($q) {
                    $q->where('is_archived', false)->orWhereNull('is_archived');
                })
                ->whereNotIn('status', ['mutasi', 'keluar'])
                ->orderBy('nama')
                ->get();

            // Hitung rekap per siswa
            $rekapSiswa = $siswas->map(function ($siswa) use ($semesterId) {
                $absensi = AbsensiSiswa::where('siswa_id', $siswa->id)
                    ->whereHas('absensiHarian', fn($q) => $q->when($semesterId, fn($q) => $q->where('semester_id', $semesterId)))
                    ->selectRaw('status, COUNT(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status');

                return (object) [
                    'siswa'       => $siswa,
                    'hadir'       => $absensi['H'] ?? 0,
                    'sakit'       => $absensi['S'] ?? 0,
                    'izin'        => $absensi['I'] ?? 0,
                    'alpa'        => $absensi['A'] ?? 0,
                    'dispensasi'  => $absensi['D'] ?? 0,
                    'total'       => $absensi->sum(),
                ];
            });
        }

        $semester = Semester::find($semesterId);
        $isAdmin  = auth()->user()->hasRole('admin');

        return view('laporan.absensi.rekap-siswa', compact(
            'rekapSiswa', 'rombels', 'semesters', 'semesterAktif',
            'semesterId', 'semester', 'isAdmin', 'sudahFilter', 'rombelId'
        ));
    }

    // ============================================================
    // REKAP SISWA - PDF
    // ============================================================
    public function rekapSiswaPdf(Request $request)
    {
        $semesterId  = $request->get('semester_id');
        $rombelId    = $request->get('rombel_id');
        $semester    = Semester::find($semesterId);

        $siswas = Siswa::when($rombelId, function ($q) use ($rombelId) {
                $rombel = Rombel::find($rombelId);
                if ($rombel) $q->where('rombongan_belajar_id', $rombel->rombongan_belajar_id);
            })
            ->when(!$rombelId && $semesterId, function ($q) use ($semesterId) {
                $uuids = Rombel::where('semester_id', $semesterId)->where('is_archived', false)->pluck('rombongan_belajar_id');
                $q->whereIn('rombongan_belajar_id', $uuids);
            })
            ->where(function ($q) { $q->where('is_archived', false)->orWhereNull('is_archived'); })
            ->orderBy('nama_rombel')->orderBy('nama')
            ->get();

        $rekapSiswa = $siswas->map(function ($siswa) use ($semesterId) {
            $absensi = AbsensiSiswa::where('siswa_id', $siswa->id)
                ->whereHas('absensiHarian', fn($q) => $q->when($semesterId, fn($q) => $q->where('semester_id', $semesterId)))
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            return (object) [
                'siswa'      => $siswa,
                'hadir'      => $absensi['H'] ?? 0,
                'sakit'      => $absensi['S'] ?? 0,
                'izin'       => $absensi['I'] ?? 0,
                'alpa'       => $absensi['A'] ?? 0,
                'dispensasi' => $absensi['D'] ?? 0,
                'total'      => $absensi->sum(),
            ];
        });

        $rombel   = $rombelId ? Rombel::find($rombelId) : null;
        $settings = \App\Models\Setting::allAsArray();
        $ttd      = $request->only(['nama_kepsek', 'nip_kepsek', 'golongan_kepsek', 'tempat_ttd', 'tanggal_ttd']);

        $pdf = Pdf::loadView('laporan.absensi.rekap-siswa-pdf', compact(
            'rekapSiswa', 'semester', 'rombel', 'settings', 'ttd'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('rekap-absensi-siswa.pdf');
    }

    // ============================================================
    // REKAP SISWA - EXCEL
    // ============================================================
    public function rekapSiswaExcel(Request $request)
    {
        return Excel::download(
            new AbsensiSiswaExport($request->all()),
            'rekap-absensi-siswa-' . date('Ymd') . '.xlsx'
        );
    }
}
