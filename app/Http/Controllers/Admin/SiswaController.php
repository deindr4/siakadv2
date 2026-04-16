<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\Semester;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('semester_id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);

        $query = Siswa::with('semester')
            ->where('is_archived', false)
            ->where('status_mutasi', 'aktif');

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->search.'%')
                  ->orWhere('nisn', 'like', '%'.$request->search.'%')
                  ->orWhere('nipd', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('tingkat')) {
            $query->where('tingkat_pendidikan_id', $request->tingkat);
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        if ($request->filled('rombel')) {
            $query->where('rombongan_belajar_id', $request->rombel);
        }

        $siswas = $query->orderBy('nama')->paginate(20)->withQueryString();

        // Counter
        $baseQuery = Siswa::where('is_archived', false)
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId));

        $totalAktif  = (clone $baseQuery)->where('status_mutasi', 'aktif')->count();
        $totalL      = (clone $baseQuery)->where('status_mutasi', 'aktif')->where('jenis_kelamin', 'L')->count();
        $totalP      = (clone $baseQuery)->where('status_mutasi', 'aktif')->where('jenis_kelamin', 'P')->count();

        // Untuk filter dropdown
        $tingkats = Siswa::where('is_archived', false)
            ->where('status_mutasi', 'aktif')
            ->distinct()->pluck('tingkat_pendidikan_id')->filter()->sort();

        $rombels = Rombel::where('is_archived', false)
            ->where('jenis_rombel', '1')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->orderBy('tingkat')->orderBy('nama_rombel')->get();

        return view('admin.siswa.index', compact(
            'siswas', 'semesters', 'semesterAktif', 'semesterId',
            'tingkats', 'rombels', 'totalAktif', 'totalL', 'totalP'
        ));
    }

    public function create()
    {
        $semesters = Semester::orderByDesc('semester_id')->get();
        $rombels   = Rombel::where('is_archived', false)->where('jenis_rombel', '1')
                        ->orderBy('tingkat')->orderBy('nama_rombel')->get();
        return view('admin.siswa.create', compact('semesters', 'rombels'));
    }

    public function store(Request $request)
{
    $request->validate([
        'nama'          => 'required|string|max:255',
        'jenis_kelamin' => 'required|in:L,P',
        'nisn'          => 'nullable|unique:siswas,nisn',
        'semester_id'   => 'required|exists:semesters,id',
    ]);

    // Cek duplikat nama + tanggal lahir
    if ($request->filled('tanggal_lahir')) {
        $exists = Siswa::where('nama', $request->nama)
            ->where('tanggal_lahir', $request->tanggal_lahir)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'nama' => 'Siswa dengan nama dan tanggal lahir yang sama sudah ada di sistem!'
            ])->withInput();
        }
    }

    Siswa::create(array_merge($request->all(), [
        'status_mutasi' => 'aktif',
        'sumber_data'   => 'manual',
        'is_archived'   => false,
    ]));

    return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan!');
}

    public function show(Siswa $siswa)
    {
        $rombel = Rombel::where('rombongan_belajar_id', $siswa->rombongan_belajar_id)->first();
        return view('admin.siswa.show', compact('siswa', 'rombel'));
    }

    public function edit(Siswa $siswa)
    {
        $semesters = Semester::orderByDesc('semester_id')->get();
        $rombels   = Rombel::where('is_archived', false)->where('jenis_rombel', '1')
                        ->orderBy('tingkat')->orderBy('nama_rombel')->get();
        return view('admin.siswa.edit', compact('siswa', 'semesters', 'rombels'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'nisn'          => 'nullable|unique:siswas,nisn,'.$siswa->id,
            'semester_id'   => 'required|exists:semesters,id',
        ]);

        $siswa->update($request->all());
        return redirect()->route('admin.siswa.show', $siswa)->with('success', 'Data siswa berhasil diupdate!');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil dihapus!');
    }
}
