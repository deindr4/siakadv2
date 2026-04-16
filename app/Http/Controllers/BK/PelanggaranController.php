<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\PelanggaranSiswa;
use App\Models\JenisPelanggaran;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\Semester;
use Illuminate\Http\Request;

class PelanggaranController extends Controller
{
    public function index(Request $request)
    {
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('semester_id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);

        $query = PelanggaranSiswa::with(['siswa', 'jenisPelanggaran', 'dicatatOleh'])
            ->where('status', '!=', 'dibatalkan');

        if ($semesterId) $query->where('semester_id', $semesterId);

        if ($request->filled('kategori')) {
            $query->whereHas('jenisPelanggaran', fn($q) => $q->where('kategori', $request->kategori));
        }

        if ($request->filled('rombel')) {
            $query->whereHas('siswa', fn($q) => $q->where('rombongan_belajar_id', $request->rombel));
        }

        if ($request->filled('search')) {
            $query->whereHas('siswa', fn($q) => $q->where('nama', 'like', '%'.$request->search.'%')
                ->orWhere('nisn', 'like', '%'.$request->search.'%'));
        }

        $pelanggaran = $query->orderByDesc('tanggal')->paginate(20)->withQueryString();

        $jenisList = JenisPelanggaran::where('is_active', true)->orderBy('kategori')->get();
        $rombels   = Rombel::where('is_archived', false)->where('jenis_rombel', '1')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->orderBy('tingkat')->orderBy('nama_rombel')->get();

        // Counter
        $totalHariIni = PelanggaranSiswa::whereDate('tanggal', today())
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))->count();
        $totalBulanIni = PelanggaranSiswa::whereMonth('tanggal', now()->month)
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))->count();
        $totalSemester = PelanggaranSiswa::when($semesterId, fn($q) => $q->where('semester_id', $semesterId))->count();

        $siswaList = \App\Models\Siswa::where('is_archived', false)
    ->where('status_mutasi', 'aktif')
    ->get(['id', 'nama', 'nisn', 'rombongan_belajar_id']);

        return view('bk.pelanggaran.index', compact(
            'pelanggaran', 'jenisList', 'rombels', 'semesters',
            'semesterAktif', 'semesterId', 'totalHariIni', 'totalBulanIni', 'totalSemester', 'siswaList'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id'              => 'required|exists:siswas,id',
            'jenis_pelanggaran_id'  => 'required|exists:jenis_pelanggaran,id',
            'tanggal'               => 'required|date',
            'semester_id'           => 'required|exists:semesters,id',
        ]);

        $jenis = JenisPelanggaran::findOrFail($request->jenis_pelanggaran_id);

        PelanggaranSiswa::create([
            'semester_id'          => $request->semester_id,
            'siswa_id'             => $request->siswa_id,
            'jenis_pelanggaran_id' => $request->jenis_pelanggaran_id,
            'dicatat_oleh'         => auth()->id(),
            'tanggal'              => $request->tanggal,
            'poin'                 => $jenis->poin,
            'keterangan'           => $request->keterangan,
            'tindakan'             => $request->tindakan,
            'status'               => 'aktif',
        ]);

        return back()->with('success', 'Pelanggaran berhasil dicatat!');
    }

    public function update(Request $request, PelanggaranSiswa $pelanggaran)
    {
        $request->validate([
            'tindakan' => 'nullable|string',
            'status'   => 'required|in:aktif,selesai,dibatalkan',
            'keterangan' => 'nullable|string',
        ]);

        $pelanggaran->update($request->only(['tindakan', 'status', 'keterangan']));

        return back()->with('success', 'Data pelanggaran berhasil diupdate!');
    }

    public function destroy(PelanggaranSiswa $pelanggaran)
    {
        $pelanggaran->update(['status' => 'dibatalkan']);
        return back()->with('success', 'Pelanggaran berhasil dibatalkan!');
    }
}
