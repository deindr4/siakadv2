<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\PelanggaranSiswa;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\Semester;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('semester_id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);

        $rombels = Rombel::where('is_archived', false)->where('jenis_rombel', '1')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->orderBy('tingkat')->orderBy('nama_rombel')->get();

        // Rekap poin per siswa
        $query = Siswa::where('is_archived', false)
            ->where('status_mutasi', 'aktif')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->withCount(['pelanggaranAktif as total_pelanggaran'])
            ->withSum(['pelanggaranAktif as total_poin'], 'poin')
            ->having('total_poin', '>', 0);

        if ($request->filled('rombel')) {
            $query->where('rombongan_belajar_id', $request->rombel);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->search.'%')
                  ->orWhere('nisn', 'like', '%'.$request->search.'%');
            });
        }

        $siswas = $query->orderByDesc('total_poin')->paginate(20)->withQueryString();

        // Siswa dengan poin tertinggi
        $topPelanggaran = Siswa::where('is_archived', false)
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->withSum(['pelanggaranAktif as total_poin'], 'poin')
            ->orderByDesc('total_poin')
            ->limit(5)->get();

        return view('bk.rekap.index', compact(
            'siswas', 'semesters', 'semesterAktif', 'semesterId',
            'rombels', 'topPelanggaran'
        ));
    }
}
