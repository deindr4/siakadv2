<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\JenisPelanggaran;
use Illuminate\Http\Request;

class JenisPelanggaranController extends Controller
{
    public function index(Request $request)
    {
        $query = JenisPelanggaran::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->search.'%')
                  ->orWhere('kode', 'like', '%'.$request->search.'%');
            });
        }

        $jenis   = $query->orderBy('kategori')->orderBy('poin')->paginate(20)->withQueryString();
        $totalRingan = JenisPelanggaran::where('kategori', 'ringan')->count();
        $totalSedang = JenisPelanggaran::where('kategori', 'sedang')->count();
        $totalBerat  = JenisPelanggaran::where('kategori', 'berat')->count();

        return view('bk.jenis-pelanggaran.index', compact(
            'jenis', 'totalRingan', 'totalSedang', 'totalBerat'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode'     => 'required|unique:jenis_pelanggaran,kode',
            'nama'     => 'required|string|max:255',
            'kategori' => 'required|in:ringan,sedang,berat',
            'poin'     => 'required|integer|min:1',
        ]);

        JenisPelanggaran::create($request->all());

        return back()->with('success', 'Jenis pelanggaran berhasil ditambahkan!');
    }

    public function update(Request $request, JenisPelanggaran $jenisPelanggaran)
    {
        $request->validate([
            'kode'     => 'required|unique:jenis_pelanggaran,kode,'.$jenisPelanggaran->id,
            'nama'     => 'required|string|max:255',
            'kategori' => 'required|in:ringan,sedang,berat',
            'poin'     => 'required|integer|min:1',
        ]);

        $jenisPelanggaran->update($request->all());

        return back()->with('success', 'Jenis pelanggaran berhasil diupdate!');
    }

    public function destroy(JenisPelanggaran $jenisPelanggaran)
    {
        $jenisPelanggaran->delete();
        return back()->with('success', 'Jenis pelanggaran berhasil dihapus!');
    }
}
