<?php

//app/Http/Controllers/Admin/RombelController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rombel;
use App\Models\Semester;
use App\Models\Siswa;
use Illuminate\Http\Request;

class RombelController extends Controller
{
    public function index(Request $request)
    {
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('semester_id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);
        $tab           = $request->get('tab', 'kelas'); // kelas | mapel | ekskul | semua

        $query = Rombel::with('semester')
            ->where('is_archived', false);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        // Filter tab
        if ($tab === 'kelas') {
            $query->where('jenis_rombel', '1');
        } elseif ($tab === 'mapel') {
            $query->where('jenis_rombel', '16');
        } elseif ($tab === 'ekskul') {
            $query->where('jenis_rombel', '51');
        }

        if ($request->filled('search')) {
            $query->where('nama_rombel', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('tingkat') && in_array($tab, ['kelas', 'semua'])) {
            $query->where('tingkat', $request->tingkat);
        }

        $rombels = $query->orderBy('tingkat')->orderBy('nama_rombel')->paginate(20)->withQueryString();

        // Hitung jumlah siswa per rombel
        foreach ($rombels as $rombel) {
            $rombel->jumlah_siswa_aktif = Siswa::where('rombongan_belajar_id', $rombel->rombongan_belajar_id)
                ->where('is_archived', false)->count();
        }

        $tingkats = Rombel::where('is_archived', false)
            ->whereIn('jenis_rombel', ['1', '16'])
            ->distinct()->pluck('tingkat')->filter()->sort();

        // Counter tab
        $baseQuery = Rombel::where('is_archived', false)
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId));

        $countKelas  = (clone $baseQuery)->where('jenis_rombel', '1')->count();
        $countMapel  = (clone $baseQuery)->where('jenis_rombel', '16')->count();
        $countEkskul = (clone $baseQuery)->where('jenis_rombel', '51')->count();
        $countSemua  = (clone $baseQuery)->count();
        $totalSiswa  = Siswa::where('is_archived', false)->count();

        return view('admin.rombel.index', compact(
            'rombels', 'semesters', 'semesterAktif', 'semesterId',
            'tingkats', 'countKelas', 'countMapel', 'countEkskul', 'countSemua',
            'totalSiswa', 'tab'
        ));
    }

    public function show(Rombel $rombel, Request $request)
    {
        $query = Siswa::where('rombongan_belajar_id', $rombel->rombongan_belajar_id)
            ->where('is_archived', false);

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
        $totalL = Siswa::where('rombongan_belajar_id', $rombel->rombongan_belajar_id)->where('is_archived', false)->where('jenis_kelamin', 'L')->count();
        $totalP = Siswa::where('rombongan_belajar_id', $rombel->rombongan_belajar_id)->where('is_archived', false)->where('jenis_kelamin', 'P')->count();

        return view('admin.rombel.show', compact('rombel', 'siswas', 'totalL', 'totalP'));
    }
}
