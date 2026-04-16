<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class GuruController extends Controller
{
    public function index(Request $request)
{
    $query = Guru::where('is_archived', false);

    // Tab filter: guru / tendik / semua
    $tab = $request->get('tab', 'semua');
    if ($tab === 'guru') {
        $query->where('jenis_ptk', 'Guru');
    } elseif ($tab === 'tendik') {
        $query->where('jenis_ptk', '!=', 'Guru')->whereNotNull('jenis_ptk');
    }

    // Search
    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('nama', 'like', '%'.$request->search.'%')
              ->orWhere('nip', 'like', '%'.$request->search.'%')
              ->orWhere('nuptk', 'like', '%'.$request->search.'%')
              ->orWhere('jabatan', 'like', '%'.$request->search.'%');
        });
    }

    // Filter status kepegawaian
    if ($request->filled('status_kepegawaian')) {
        $query->where('status_kepegawaian', $request->status_kepegawaian);
    }

    $gurus = $query->latest()->paginate(15)->withQueryString();

    $statusKepegawaian = Guru::where('is_archived', false)
        ->distinct()->pluck('status_kepegawaian')->filter();

    // Hitung per tab
    $totalSemua  = Guru::where('is_archived', false)->count();
    $totalGuru   = Guru::where('is_archived', false)->where('jenis_ptk', 'Guru')->count();
    $totalTendik = Guru::where('is_archived', false)->where('jenis_ptk', '!=', 'Guru')->whereNotNull('jenis_ptk')->count();

    return view('admin.guru.index', compact(
        'gurus', 'statusKepegawaian', 'tab',
        'totalSemua', 'totalGuru', 'totalTendik'
    ));
}

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'         => 'required|string|max:255',
            'jenis_kelamin'=> 'required|in:L,P',
            'nip'          => 'nullable|unique:gurus,nip',
            'nuptk'        => 'nullable|unique:gurus,nuptk',
        ], [
            'nama.required'          => 'Nama wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'nip.unique'             => 'NIP sudah terdaftar',
            'nuptk.unique'           => 'NUPTK sudah terdaftar',
        ]);

        Guru::create([
            ...$request->only([
                'nip', 'nuptk', 'nik', 'nama', 'jenis_kelamin',
                'tempat_lahir', 'tanggal_lahir', 'agama',
                'jenis_ptk', 'jabatan', 'status_kepegawaian',
                'pendidikan_terakhir', 'bidang_studi',
                'no_hp', 'email', 'status',
            ]),
            'sumber_data' => 'manual',
        ]);

        return redirect()->route('admin.guru.index')
            ->with('success', 'Data guru berhasil ditambahkan!');
    }

    public function show(Guru $guru)
    {
        return view('admin.guru.show', compact('guru'));
    }

    public function edit(Guru $guru)
    {
    return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'nip'           => 'nullable|unique:gurus,nip,'.$guru->id,
            'nuptk'         => 'nullable|unique:gurus,nuptk,'.$guru->id,
        ], [
            'nama.required'          => 'Nama wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'nip.unique'             => 'NIP sudah terdaftar',
            'nuptk.unique'           => 'NUPTK sudah terdaftar',
        ]);

        $guru->update($request->only([
            'nip', 'nuptk', 'nik', 'nama', 'jenis_kelamin',
            'tempat_lahir', 'tanggal_lahir', 'agama',
            'jenis_ptk', 'jabatan', 'status_kepegawaian',
            'pendidikan_terakhir', 'bidang_studi',
            'no_hp', 'email', 'status',
        ]));

        return redirect()->route('admin.guru.index')
            ->with('success', 'Data guru berhasil diperbarui!');
    }

    public function destroy(Guru $guru)
    {
        $nama = $guru->nama;
        $guru->delete();

        return redirect()->route('admin.guru.index')
            ->with('success', 'Data guru ' . $nama . ' berhasil dihapus!');
    }
}
