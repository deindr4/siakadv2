<?php
// app/Http/Controllers/LaporanPrestasiController.php

namespace App\Http\Controllers;

use App\Models\KategoriPrestasi;
use App\Models\Prestasi;
use App\Models\Rombel;
use App\Models\Semester;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PrestasiExport;

class LaporanPrestasiController extends Controller
{
    // ============================================================
    // INDEX - Halaman laporan utama
    // ============================================================
    public function index(Request $request)
    {
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);
        $kategoris     = KategoriPrestasi::where('is_aktif', true)->orderBy('nama')->get();
        $rombels       = Rombel::where('is_archived', false)
                            ->where('jenis_rombel', '1')
                            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
                            ->orderBy('tingkat')->orderBy('nama_rombel')->get();

        $semester    = Semester::find($semesterId);
        $kategoriId  = $request->get('kategori_id');
        $tingkat     = $request->get('tingkat');
        $rombelId    = $request->get('rombel_id');

        // ── Query dasar ────────────────────────────────────────
        $baseQuery = Prestasi::with(['kategori', 'siswas', 'semester'])
            ->where('status', 'diverifikasi')
            ->when($semesterId,   fn($q) => $q->where('semester_id', $semesterId))
            ->when($kategoriId,   fn($q) => $q->where('kategori_id', $kategoriId))
            ->when($tingkat,      fn($q) => $q->where('tingkat', $tingkat))
            ->when($rombelId, function ($q) use ($rombelId) {
                $rombel = Rombel::find($rombelId);
                if ($rombel) {
                    $q->whereHas('siswas', fn($sq) =>
                        $sq->where('rombongan_belajar_id', $rombel->rombongan_belajar_id)
                    );
                }
            });

        // ── 1. Detail semua prestasi ───────────────────────────
        $detailPrestasi = (clone $baseQuery)
            ->orderBy('juara_urut')
            ->orderBy('tanggal', 'desc')
            ->get();

        // ── 2. Rekap per kategori ──────────────────────────────
        $rekapKategori = (clone $baseQuery)
            ->get()
            ->groupBy('kategori_id')
            ->map(function ($items) {
                $first = $items->first();
                return (object)[
                    'kategori'  => $first->kategori,
                    'total'     => $items->count(),
                    'individu'  => $items->where('tipe', 'individu')->count(),
                    'tim'       => $items->where('tipe', 'tim')->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();

        // ── 3. Rekap per tingkat ───────────────────────────────
        $urutTingkat = ['internasional'=>1,'nasional'=>2,'provinsi'=>3,'kabupaten'=>4,'kecamatan'=>5,'sekolah'=>6];
        $rekapTingkat = (clone $baseQuery)
            ->get()
            ->groupBy('tingkat')
            ->map(function ($items, $tingkatKey) use ($urutTingkat) {
                return (object)[
                    'tingkat' => $tingkatKey,
                    'urut'    => $urutTingkat[$tingkatKey] ?? 99,
                    'total'   => $items->count(),
                    'juara1'  => $items->where('juara_urut', 1)->count(),
                    'juara2'  => $items->where('juara_urut', 2)->count(),
                    'juara3'  => $items->where('juara_urut', 3)->count(),
                    'lainnya' => $items->whereNotIn('juara_urut', [1,2,3])->count(),
                ];
            })
            ->sortBy('urut')
            ->values();

        // ── 4. Rekap per siswa ─────────────────────────────────
        $rekapSiswa = \App\Models\PrestasiSiswa::whereHas('prestasi', function ($q) use ($semesterId, $kategoriId, $tingkat) {
                $q->where('status', 'diverifikasi')
                  ->when($semesterId,  fn($q) => $q->where('semester_id', $semesterId))
                  ->when($kategoriId,  fn($q) => $q->where('kategori_id', $kategoriId))
                  ->when($tingkat,     fn($q) => $q->where('tingkat', $tingkat));
            })
            ->with(['siswa', 'prestasi.kategori'])
            ->get()
            ->groupBy('siswa_id')
            ->map(function ($items) {
                $siswa = $items->first()->siswa;
                return (object)[
                    'siswa'    => $siswa,
                    'total'    => $items->count(),
                    'nasional' => $items->filter(fn($i) => in_array($i->prestasi?->tingkat, ['nasional','internasional']))->count(),
                    'provinsi' => $items->filter(fn($i) => $i->prestasi?->tingkat === 'provinsi')->count(),
                    'prestasi' => $items,
                ];
            })
            ->sortByDesc('total')
            ->values();

        // ── Summary stats ──────────────────────────────────────
        $stats = (object)[
            'total'         => $detailPrestasi->count(),
            'nasional_up'   => $detailPrestasi->whereIn('tingkat', ['nasional','internasional'])->count(),
            'provinsi'      => $detailPrestasi->where('tingkat', 'provinsi')->count(),
            'total_siswa'   => $rekapSiswa->count(),
        ];

        return view('laporan.prestasi.index', compact(
            'semesters', 'semesterAktif', 'semesterId', 'semester',
            'kategoris', 'rombels',
            'kategoriId', 'tingkat', 'rombelId',
            'detailPrestasi', 'rekapKategori', 'rekapTingkat', 'rekapSiswa',
            'stats'
        ));
    }

    // ============================================================
    // PDF
    // ============================================================
    public function pdf(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $kategoriId = $request->get('kategori_id');
        $tingkat    = $request->get('tingkat');
        $rombelId   = $request->get('rombel_id');
        $jenis      = $request->get('jenis', 'detail'); // detail|kategori|tingkat|siswa

        $semester  = Semester::find($semesterId);
        $kategori  = KategoriPrestasi::find($kategoriId);
        $rombel    = Rombel::find($rombelId);
        $settings  = \App\Models\Setting::allAsArray();
        $ttd       = $request->only(['nama_kepsek','nip_kepsek','golongan_kepsek','tempat_ttd','tanggal_ttd']);

        $baseQuery = Prestasi::with(['kategori', 'siswas', 'semester'])
            ->where('status', 'diverifikasi')
            ->when($semesterId,  fn($q) => $q->where('semester_id', $semesterId))
            ->when($kategoriId,  fn($q) => $q->where('kategori_id', $kategoriId))
            ->when($tingkat,     fn($q) => $q->where('tingkat', $tingkat))
            ->when($rombelId, function ($q) use ($rombelId) {
                $rombel = Rombel::find($rombelId);
                if ($rombel) {
                    $q->whereHas('siswas', fn($sq) =>
                        $sq->where('rombongan_belajar_id', $rombel->rombongan_belajar_id)
                    );
                }
            });

        $detailPrestasi = (clone $baseQuery)->orderBy('juara_urut')->orderBy('tanggal','desc')->get();

        $urutTingkat = ['internasional'=>1,'nasional'=>2,'provinsi'=>3,'kabupaten'=>4,'kecamatan'=>5,'sekolah'=>6];

        $rekapKategori = $detailPrestasi->groupBy('kategori_id')->map(function ($items) {
            return (object)[
                'kategori' => $items->first()->kategori,
                'total'    => $items->count(),
                'individu' => $items->where('tipe','individu')->count(),
                'tim'      => $items->where('tipe','tim')->count(),
            ];
        })->sortByDesc('total')->values();

        $rekapTingkat = $detailPrestasi->groupBy('tingkat')->map(function ($items, $t) use ($urutTingkat) {
            return (object)[
                'tingkat' => $t,
                'urut'    => $urutTingkat[$t] ?? 99,
                'total'   => $items->count(),
                'juara1'  => $items->where('juara_urut',1)->count(),
                'juara2'  => $items->where('juara_urut',2)->count(),
                'juara3'  => $items->where('juara_urut',3)->count(),
                'lainnya' => $items->whereNotIn('juara_urut',[1,2,3])->count(),
            ];
        })->sortBy('urut')->values();

        $rekapSiswa = \App\Models\PrestasiSiswa::whereHas('prestasi', function ($q) use ($semesterId, $kategoriId, $tingkat) {
                $q->where('status','diverifikasi')
                  ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
                  ->when($kategoriId, fn($q) => $q->where('kategori_id', $kategoriId))
                  ->when($tingkat,    fn($q) => $q->where('tingkat', $tingkat));
            })
            ->with(['siswa','prestasi.kategori'])
            ->get()
            ->groupBy('siswa_id')
            ->map(function ($items) {
                $siswa = $items->first()->siswa;
                return (object)[
                    'siswa'    => $siswa,
                    'total'    => $items->count(),
                    'nasional' => $items->filter(fn($i) => in_array($i->prestasi?->tingkat,['nasional','internasional']))->count(),
                    'provinsi' => $items->filter(fn($i) => $i->prestasi?->tingkat === 'provinsi')->count(),
                    'prestasi' => $items,
                ];
            })
            ->sortByDesc('total')->values();

        $pdf = Pdf::loadView('laporan.prestasi.pdf', compact(
            'jenis', 'detailPrestasi', 'rekapKategori', 'rekapTingkat', 'rekapSiswa',
            'semester', 'kategori', 'rombel', 'settings', 'ttd', 'tingkat'
        ))->setPaper('a4', $jenis === 'detail' ? 'landscape' : 'portrait');

        return $pdf->stream('laporan-prestasi-' . $jenis . '.pdf');
    }

    // ============================================================
    // EXCEL
    // ============================================================
    public function excel(Request $request)
    {
        $params = $request->only(['semester_id','kategori_id','tingkat','rombel_id','jenis']);
        return Excel::download(new PrestasiExport($params), 'laporan-prestasi.xlsx');
    }
}
