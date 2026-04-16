<?php

namespace App\Http\Controllers;

use App\Models\JurnalMengajar;
use App\Models\PelanggaranSiswa;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Rombel;
use App\Models\Semester;
use App\Models\Setting;
use App\Exports\JurnalExport;
use App\Exports\PelanggaranExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    // ==========================================
    // LAPORAN JURNAL MENGAJAR
    // ==========================================
    public function jurnal(Request $request)
    {
        $isAdmin       = auth()->user()->hasRole('admin');
        $isGuru        = auth()->user()->hasRole('guru');
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('semester_id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);
        $guru          = $isGuru ? Guru::where('user_id', auth()->id())->first() : null;

        $guruList = ($isAdmin || auth()->user()->hasRole('kepala_sekolah') || auth()->user()->hasRole('wakil_kepala_sekolah'))
            ? Guru::where('is_archived', false)->orderBy('nama')->get()
            : collect();

        $rombels = Rombel::where('is_archived', false)->where('jenis_rombel', '1')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->orderBy('tingkat')->orderBy('nama_rombel')->get();

        $query = JurnalMengajar::with(['guru', 'mataPelajaran', 'semester'])
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->when($isGuru && $guru, fn($q) => $q->where('guru_id', $guru->id))
            ->when($request->filled('guru_id'), fn($q) => $q->where('guru_id', $request->guru_id))
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal', $request->bulan))
            ->when($request->filled('rombel'), fn($q) => $q->where('nama_rombel', $request->rombel))
            ->orderBy('tanggal')->orderBy('id');

        $jurnals      = $query->paginate(20)->withQueryString();
        $totalJurnal  = $query->count();

        return view('laporan.jurnal.index', compact(
            'jurnals', 'semesters', 'semesterAktif', 'semesterId',
            'guruList', 'rombels', 'isAdmin', 'isGuru', 'guru', 'totalJurnal'
        ));
    }

    public function jurnalPdf(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $bulan      = $request->get('bulan');
        $guruId     = $request->get('guru_id');
        $isGuru     = auth()->user()->hasRole('guru');
        $guru       = $isGuru ? Guru::where('user_id', auth()->id())->first() : null;

        $query = JurnalMengajar::with(['guru', 'mataPelajaran'])
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->when($isGuru && $guru, fn($q) => $q->where('guru_id', $guru->id))
            ->when($guruId && !$isGuru, fn($q) => $q->where('guru_id', $guruId))
            ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
            ->orderBy('tanggal');

        $jurnals   = $query->get();
        $semester  = $semesterId ? Semester::find($semesterId) : Semester::aktif();
        $guruData  = $guruId ? Guru::find($guruId) : ($isGuru ? $guru : null);
        $settings  = Setting::allAsArray();

        // Data untuk TTD (dari request form cetak)
        $ttd = [
            'nama_guru'       => $request->get('nama_guru', $guruData?->nama),
            'nip_guru'        => $request->get('nip_guru', $guruData?->nip),
            'golongan_guru'   => $request->get('golongan_guru', $guruData?->golongan ?? '-'),
            'nama_kepsek'     => $request->get('nama_kepsek'),
            'nip_kepsek'      => $request->get('nip_kepsek'),
            'golongan_kepsek' => $request->get('golongan_kepsek'),
            'tanggal_ttd'     => $request->get('tanggal_ttd', now()->format('Y-m-d')),
            'tempat_ttd'      => $request->get('tempat_ttd', $settings['kabupaten'] ?? ''),
        ];

        $pdf = Pdf::loadView('laporan.jurnal.pdf', compact(
            'jurnals', 'semester', 'guruData', 'settings', 'ttd', 'bulan'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('jurnal-mengajar-'.now()->format('Ymd').'.pdf');
    }

    public function jurnalExcel(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $bulan      = $request->get('bulan');
        $guruId     = $request->get('guru_id');
        $isGuru     = auth()->user()->hasRole('guru');
        $guru       = $isGuru ? Guru::where('user_id', auth()->id())->first() : null;

        $query = JurnalMengajar::with(['guru', 'mataPelajaran'])
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->when($isGuru && $guru, fn($q) => $q->where('guru_id', $guru->id))
            ->when($guruId && !$isGuru, fn($q) => $q->where('guru_id', $guruId))
            ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
            ->orderBy('tanggal');

        $filename = 'jurnal-mengajar-'.now()->format('Ymd').'.xlsx';
        return Excel::download(new JurnalExport($query, 'Jurnal Mengajar'), $filename);
    }

    // ==========================================
    // JURNAL KELAS (SISWA)
    // ==========================================
    public function jurnalKelas(Request $request)
    {
        $siswa = Siswa::where('user_id', auth()->id())->first();
        if (!$siswa) return redirect()->back()->with('error', 'Data siswa tidak ditemukan!');

        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('semester_id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);

        $query = JurnalMengajar::with(['guru', 'mataPelajaran'])
            ->where('nama_rombel', $siswa->nama_rombel ?? '')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal', $request->bulan))
            ->orderByDesc('tanggal');

        $jurnals = $query->paginate(20)->withQueryString();

        return view('laporan.jurnal.kelas', compact(
            'jurnals', 'siswa', 'semesters', 'semesterAktif', 'semesterId'
        ));
    }

    // ==========================================
    // LAPORAN PELANGGARAN
    // ==========================================
    public function pelanggaran(Request $request)
    {
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('semester_id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);

        $rombels = Rombel::where('is_archived', false)->where('jenis_rombel', '1')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->orderBy('tingkat')->orderBy('nama_rombel')->get();

        $query = PelanggaranSiswa::with(['siswa', 'jenisPelanggaran', 'dicatatOleh'])
            ->where('status', '!=', 'dibatalkan')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal', $request->bulan))
            ->when($request->filled('rombel'), fn($q) => $q->whereHas('siswa', fn($s) => $s->where('rombongan_belajar_id', $request->rombel)))
            ->when($request->filled('kategori'), fn($q) => $q->whereHas('jenisPelanggaran', fn($j) => $j->where('kategori', $request->kategori)))
            ->when($request->filled('search'), fn($q) => $q->whereHas('siswa', fn($s) => $s->where('nama', 'like', '%'.$request->search.'%')))
            ->orderByDesc('tanggal');

        $pelanggaran = $query->paginate(20)->withQueryString();

        // Rekap per siswa
        $rekapSiswa = PelanggaranSiswa::with('siswa')
            ->where('status', '!=', 'dibatalkan')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->selectRaw('siswa_id, COUNT(*) as total_kasus, SUM(poin) as total_poin')
            ->groupBy('siswa_id')
            ->orderByDesc('total_poin')
            ->limit(10)->get();

        return view('laporan.pelanggaran.index', compact(
            'pelanggaran', 'semesters', 'semesterAktif', 'semesterId',
            'rombels', 'rekapSiswa'
        ));
    }

    public function pelanggaranPdf(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $bulan      = $request->get('bulan');

        $query = PelanggaranSiswa::with(['siswa', 'jenisPelanggaran', 'dicatatOleh'])
            ->where('status', '!=', 'dibatalkan')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
            ->when($request->filled('rombel'), fn($q) => $q->whereHas('siswa', fn($s) => $s->where('rombongan_belajar_id', $request->rombel)))
            ->orderByDesc('tanggal');

        $pelanggaran = $query->get();
        $semester    = $semesterId ? Semester::find($semesterId) : Semester::aktif();
        $settings    = Setting::allAsArray();

        $ttd = [
            'nama_kepsek'     => $request->get('nama_kepsek'),
            'nip_kepsek'      => $request->get('nip_kepsek'),
            'golongan_kepsek' => $request->get('golongan_kepsek'),
            'tanggal_ttd'     => $request->get('tanggal_ttd', now()->format('Y-m-d')),
            'tempat_ttd'      => $request->get('tempat_ttd', $settings['kabupaten'] ?? ''),
        ];

        $pdf = Pdf::loadView('laporan.pelanggaran.pdf', compact(
            'pelanggaran', 'semester', 'settings', 'ttd', 'bulan'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('laporan-pelanggaran-'.now()->format('Ymd').'.pdf');
    }

    public function pelanggaranExcel(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $bulan      = $request->get('bulan');

        $query = PelanggaranSiswa::with(['siswa', 'jenisPelanggaran', 'dicatatOleh'])
            ->where('status', '!=', 'dibatalkan')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
            ->orderByDesc('tanggal');

        $filename = 'laporan-pelanggaran-'.now()->format('Ymd').'.xlsx';
        return Excel::download(new PelanggaranExport($query), $filename);
    }

    // ==========================================
    // UPLOAD SCAN JURNAL (Admin)
    // ==========================================
    public function uploadScan(Request $request, JurnalMengajar $jurnal)
    {
        $request->validate([
            'scan_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Hapus file lama
        if ($jurnal->scan_file) {
            $old = storage_path('app/public/'.$jurnal->scan_file);
            if (file_exists($old)) unlink($old);
        }

        $file     = $request->file('scan_file');
        $filename = 'scan_jurnal_'.$jurnal->id.'_'.time().'.'.$file->getClientOriginalExtension();
        $dir      = storage_path('app/public/jurnal/scan');
        if (!file_exists($dir)) mkdir($dir, 0755, true);
        $file->move($dir, $filename);

        $jurnal->update(['scan_file' => 'jurnal/scan/'.$filename]);

        return back()->with('success', 'File scan berhasil diupload!');
    }
}
