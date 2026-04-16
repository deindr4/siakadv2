<?php

//app/Http/Controllers/Admin/MataPelajaranController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $query = MataPelajaran::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%'.$request->search.'%')
                  ->orWhere('kode', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('kelompok')) {
            $query->where('kelompok', $request->kelompok);
        }

        $mapels   = $query->orderBy('kelompok')->orderBy('nama')->paginate(20)->withQueryString();
        $kelompoks = MataPelajaran::distinct()->pluck('kelompok')->filter();
        $total     = MataPelajaran::count();
        $totalAktif = MataPelajaran::where('is_active', true)->count();

        return view('admin.mapel.index', compact('mapels', 'kelompoks', 'total', 'totalAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:mata_pelajaran,kode',
            'nama' => 'required|string|max:255',
        ]);

        MataPelajaran::create($request->all());
        return back()->with('success', 'Mata pelajaran berhasil ditambahkan!');
    }

    public function update(Request $request, MataPelajaran $mapel)
    {
        $request->validate([
            'kode' => 'required|unique:mata_pelajaran,kode,'.$mapel->id,
            'nama' => 'required|string|max:255',
        ]);
        $mapel->update($request->all());
        return back()->with('success', 'Mata pelajaran berhasil diupdate!');
    }

    public function destroy(MataPelajaran $mapel)
    {
        $mapel->delete();
        return back()->with('success', 'Mata pelajaran berhasil dihapus!');
    }
}
