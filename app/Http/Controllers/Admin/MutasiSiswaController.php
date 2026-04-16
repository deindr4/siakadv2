<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Semester;
use Illuminate\Http\Request;

class MutasiSiswaController extends Controller
{
    public function index(Request $request)
    {
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('semester_id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);
        $tab           = $request->get('tab', 'semua');

        $statusList = ['mutasi_masuk', 'mutasi_keluar', 'putus_sekolah', 'berhenti'];

        $query = Siswa::where('is_archived', false)
            ->whereIn('status_mutasi', $statusList);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        if ($tab !== 'semua') {
            $query->where('status_mutasi', $tab);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->search.'%')
                  ->orWhere('nisn', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        $siswas = $query->orderBy('nama')->paginate(20)->withQueryString();

        $baseQuery = Siswa::where('is_archived', false)
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId));

        $countSemua       = (clone $baseQuery)->whereIn('status_mutasi', $statusList)->count();
        $countMutasiMasuk = (clone $baseQuery)->where('status_mutasi', 'mutasi_masuk')->count();
        $countMutasiKeluar= (clone $baseQuery)->where('status_mutasi', 'mutasi_keluar')->count();
        $countPutus       = (clone $baseQuery)->where('status_mutasi', 'putus_sekolah')->count();
        $countBerhenti    = (clone $baseQuery)->where('status_mutasi', 'berhenti')->count();

        return view('admin.mutasi.index', compact(
            'siswas', 'semesters', 'semesterAktif', 'semesterId', 'tab',
            'countSemua', 'countMutasiMasuk', 'countMutasiKeluar',
            'countPutus', 'countBerhenti'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id'        => 'required|exists:siswas,id',
            'status_mutasi'   => 'required|in:mutasi_masuk,mutasi_keluar,putus_sekolah,berhenti',
            'tanggal_mutasi'  => 'required|date',
            'keterangan_mutasi' => 'nullable|string',
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id);
        $siswa->update([
            'status_mutasi'     => $request->status_mutasi,
            'tanggal_mutasi'    => $request->tanggal_mutasi,
            'keterangan_mutasi' => $request->keterangan_mutasi,
        ]);

            // Jika lulus → arahkan ke alumni, selainnya ke mutasi
        if ($request->status_mutasi === 'lulus') {
            return redirect()->route('admin.alumni.index')
                ->with('success', $siswa->nama . ' berhasil ditandai sebagai Alumni!');
        }

        return redirect()->route('admin.mutasi.index')
            ->with('success', $siswa->nama . ' berhasil diproses mutasi!');
    }

    public function restore(Siswa $siswa)
    {
        $siswa->update([
            'status_mutasi'     => 'aktif',
            'tanggal_mutasi'    => null,
            'keterangan_mutasi' => null,
        ]);

        return back()->with('success', 'Siswa berhasil dikembalikan ke status aktif!');
    }
}
