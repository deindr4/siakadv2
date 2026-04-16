<?php
// app/Http/Controllers/LaporanTiketController.php

namespace App\Http\Controllers;

use App\Models\Tiket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TiketExport;

class LaporanTiketController extends Controller
{
    // ============================================================
    // INDEX
    // ============================================================
    public function index(Request $request)
    {
        $tahun   = $request->get('tahun', now()->year);
        $bulan   = $request->get('bulan');
        $kategori = $request->get('kategori');

        // ── Rekap per kategori ─────────────────────────────────
        $rekapKategori = Tiket::selectRaw('kategori, kategori_lainnya, COUNT(*) as total,
                SUM(CASE WHEN status="open" THEN 1 ELSE 0 END) as open,
                SUM(CASE WHEN status="diproses" THEN 1 ELSE 0 END) as diproses,
                SUM(CASE WHEN status="selesai" THEN 1 ELSE 0 END) as selesai,
                SUM(CASE WHEN status="terkunci" THEN 1 ELSE 0 END) as terkunci')
            ->whereYear('created_at', $tahun)
            ->when($bulan,    fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($kategori, fn($q) => $q->where('kategori', $kategori))
            ->groupBy('kategori', 'kategori_lainnya')
            ->orderByDesc('total')
            ->get();

        // ── Rekap per bulan ────────────────────────────────────
        $rekapBulan = Tiket::selectRaw('MONTH(created_at) as bulan_num, MONTHNAME(created_at) as bulan_nama,
                COUNT(*) as total,
                SUM(CASE WHEN status="selesai" THEN 1 ELSE 0 END) as selesai,
                SUM(CASE WHEN status IN ("open","diproses") THEN 1 ELSE 0 END) as aktif,
                SUM(CASE WHEN role_pembuat="siswa" THEN 1 ELSE 0 END) as dari_siswa,
                SUM(CASE WHEN role_pembuat="guru" THEN 1 ELSE 0 END) as dari_guru')
            ->whereYear('created_at', $tahun)
            ->when($kategori, fn($q) => $q->where('kategori', $kategori))
            ->groupBy('bulan_num', 'bulan_nama')
            ->orderBy('bulan_num')
            ->get();

        // ── Detail tiket (semua) ───────────────────────────────
        $detailTiket = Tiket::with(['user', 'respon'])
            ->whereYear('created_at', $tahun)
            ->when($bulan,    fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($kategori, fn($q) => $q->where('kategori', $kategori))
            ->orderByDesc('created_at')
            ->get();

        // ── Stats ──────────────────────────────────────────────
        $stats = (object)[
            'total'      => $detailTiket->count(),
            'open'       => $detailTiket->whereIn('status', ['open','diproses'])->count(),
            'selesai'    => $detailTiket->where('status','selesai')->count(),
            'terkunci'   => $detailTiket->where('status','terkunci')->count(),
            'dari_siswa' => $detailTiket->where('role_pembuat','siswa')->count(),
            'dari_guru'  => $detailTiket->where('role_pembuat','guru')->count(),
            'rata_respon' => $detailTiket->count()
                ? round($detailTiket->avg(fn($t) => $t->respon->count()), 1)
                : 0,
        ];

        $tahunList    = range(now()->year, now()->year - 4);
        $kategoriList = \App\Models\Tiket::kategoriList();

        return view('laporan.tiket.index', compact(
            'rekapKategori', 'rekapBulan', 'detailTiket',
            'stats', 'tahun', 'bulan', 'kategori',
            'tahunList', 'kategoriList'
        ));
    }

    // ============================================================
    // PDF
    // ============================================================
    public function pdf(Request $request)
    {
        $tahun    = $request->get('tahun', now()->year);
        $bulan    = $request->get('bulan');
        $kategori = $request->get('kategori');
        $jenis    = $request->get('jenis', 'rekap'); // rekap | detail
        $ttd      = $request->only(['nama_kepsek','nip_kepsek','golongan_kepsek','tempat_ttd','tanggal_ttd']);
        $settings = \App\Models\Setting::allAsArray();

        $rekapKategori = Tiket::selectRaw('kategori, kategori_lainnya, COUNT(*) as total,
                SUM(CASE WHEN status="open" THEN 1 ELSE 0 END) as open,
                SUM(CASE WHEN status="diproses" THEN 1 ELSE 0 END) as diproses,
                SUM(CASE WHEN status="selesai" THEN 1 ELSE 0 END) as selesai,
                SUM(CASE WHEN status="terkunci" THEN 1 ELSE 0 END) as terkunci')
            ->whereYear('created_at', $tahun)
            ->when($bulan,    fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($kategori, fn($q) => $q->where('kategori', $kategori))
            ->groupBy('kategori', 'kategori_lainnya')
            ->orderByDesc('total')
            ->get();

        $rekapBulan = Tiket::selectRaw('MONTH(created_at) as bulan_num, MONTHNAME(created_at) as bulan_nama,
                COUNT(*) as total,
                SUM(CASE WHEN status="selesai" THEN 1 ELSE 0 END) as selesai,
                SUM(CASE WHEN status IN ("open","diproses") THEN 1 ELSE 0 END) as aktif,
                SUM(CASE WHEN role_pembuat="siswa" THEN 1 ELSE 0 END) as dari_siswa,
                SUM(CASE WHEN role_pembuat="guru" THEN 1 ELSE 0 END) as dari_guru')
            ->whereYear('created_at', $tahun)
            ->when($kategori, fn($q) => $q->where('kategori', $kategori))
            ->groupBy('bulan_num', 'bulan_nama')
            ->orderBy('bulan_num')
            ->get();

        $detailTiket = Tiket::with(['user','respon'])
            ->whereYear('created_at', $tahun)
            ->when($bulan,    fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($kategori, fn($q) => $q->where('kategori', $kategori))
            ->orderByDesc('created_at')
            ->get();

        $bulanNama = $bulan ? \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') : null;

        $pdf = Pdf::loadView('laporan.tiket.pdf', compact(
            'jenis', 'rekapKategori', 'rekapBulan', 'detailTiket',
            'tahun', 'bulan', 'bulanNama', 'kategori', 'settings', 'ttd'
        ))->setPaper('a4', $jenis === 'detail' ? 'landscape' : 'portrait');

        return $pdf->stream('laporan-tiket-' . $jenis . '-' . $tahun . '.pdf');
    }

    // ============================================================
    // EXCEL
    // ============================================================
    public function excel(Request $request)
    {
        $params = $request->only(['tahun','bulan','kategori']);
        return Excel::download(new TiketExport($params), 'laporan-tiket-' . ($params['tahun'] ?? now()->year) . '.xlsx');
    }
}
