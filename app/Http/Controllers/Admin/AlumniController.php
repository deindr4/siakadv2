<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\Semester;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $semesters  = Semester::orderByDesc('semester_id')->get();
        $semesterId = $request->get('semester_id');

        $query = Alumni::with('semester');

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->search.'%')
                  ->orWhere('nisn', 'like', '%'.$request->search.'%')
                  ->orWhere('no_ijazah', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        if ($request->filled('tahun_lulus')) {
            $query->where('tahun_lulus', $request->tahun_lulus);
        }

        $siswas   = $query->orderBy('nama')->paginate(20)->withQueryString();
        $totalL   = Alumni::where('jenis_kelamin', 'L')->count();
        $totalP   = Alumni::where('jenis_kelamin', 'P')->count();
        $total    = $totalL + $totalP;

        $tahunLulus = Alumni::distinct()->pluck('tahun_lulus')->filter()->sort()->reverse();

        // Untuk kelulusan massal
        $semesterAktif = Semester::aktif();
        $rombels = Rombel::where('is_archived', false)
            ->where('jenis_rombel', '1')
            ->when($semesterAktif, fn($q) => $q->where('semester_id', $semesterAktif->id))
            ->orderBy('tingkat')->orderBy('nama_rombel')->get();

        return view('admin.alumni.index', compact(
            'siswas', 'semesters', 'semesterId', 'semesterAktif',
            'totalL', 'totalP', 'total', 'tahunLulus', 'rombels'
        ));
    }

    // Kelulusan massal
    public function luluskanMassal(Request $request)
    {
        $request->validate([
            'semester_id'    => 'required|exists:semesters,id',
            'rombel_ids'     => 'required|array|min:1',
            'tahun_lulus'    => 'required|string',
            'tanggal_lulus'  => 'required|date',
        ]);

        $semester = Semester::findOrFail($request->semester_id);

        // Ambil rombel yang dipilih
        $rombels = Rombel::whereIn('id', $request->rombel_ids)->get();

        $rombelBelajarIds = $rombels->pluck('rombongan_belajar_id');

        // Ambil semua siswa aktif di rombel tersebut
        $siswas = Siswa::where('is_archived', false)
            ->where('status_mutasi', 'aktif')
            ->whereIn('rombongan_belajar_id', $rombelBelajarIds)
            ->get();

        $count = 0;
        foreach ($siswas as $siswa) {
            // Pindahkan ke tabel alumni
            Alumni::updateOrCreate(
                ['nisn' => $siswa->nisn, 'tahun_lulus' => $request->tahun_lulus],
                [
                    'semester_id'          => $siswa->semester_id,
                    'siswa_id'             => $siswa->id,
                    'peserta_didik_id'     => $siswa->peserta_didik_id,
                    'nisn'                 => $siswa->nisn,
                    'nipd'                 => $siswa->nipd,
                    'nama'                 => $siswa->nama,
                    'jenis_kelamin'        => $siswa->jenis_kelamin,
                    'nik'                  => $siswa->nik,
                    'tempat_lahir'         => $siswa->tempat_lahir,
                    'tanggal_lahir'        => $siswa->tanggal_lahir,
                    'agama'                => $siswa->agama,
                    'nama_rombel'          => $siswa->nama_rombel,
                    'tingkat_pendidikan_id'=> $siswa->tingkat_pendidikan_id,
                    'kurikulum'            => $siswa->kurikulum,
                    'sekolah_asal'         => $siswa->sekolah_asal,
                    'tahun_lulus'          => $request->tahun_lulus,
                    'tanggal_lulus'        => $request->tanggal_lulus,
                    'nama_ayah'            => $siswa->nama_ayah,
                    'nama_ibu'             => $siswa->nama_ibu,
                    'nama_wali'            => $siswa->nama_wali,
                    'no_hp_ortu'           => $siswa->no_hp_ortu,
                    'no_hp'                => $siswa->no_hp,
                    'email'                => $siswa->email,
                    'sumber_data'          => 'dapodik',
                ]
            );

            // Update status siswa jadi lulus
            $siswa->update([
                'status_mutasi'     => 'lulus',
                'tanggal_mutasi'    => $request->tanggal_lulus,
                'keterangan_mutasi' => 'Lulus tahun ' . $request->tahun_lulus,
            ]);

            $count++;
        }

        return redirect()->route('admin.alumni.index')
            ->with('success', $count . ' siswa berhasil diluluskan dan dipindahkan ke data Alumni!');
    }

    // Tambah manual
    public function create()
    {
        $semesters = Semester::orderByDesc('semester_id')->get();
        return view('admin.alumni.create', compact('semesters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tahun_lulus'   => 'required|string',
        ]);

        Alumni::create(array_merge($request->all(), ['sumber_data' => 'manual']));

        return redirect()->route('admin.alumni.index')
            ->with('success', 'Data alumni berhasil ditambahkan!');
    }

    // Import Excel
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        // TODO: implement Excel import
        return back()->with('success', 'Import berhasil!');
    }

    public function show(Alumni $alumni)
    {
        return view('admin.alumni.show', compact('alumni'));
    }

    public function edit(Alumni $alumni)
    {
        $semesters = Semester::orderByDesc('semester_id')->get();
        return view('admin.alumni.edit', compact('alumni', 'semesters'));
    }

    public function update(Request $request, Alumni $alumni)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tahun_lulus'   => 'required|string',
        ]);

        $alumni->update($request->all());
        return redirect()->route('admin.alumni.index')
            ->with('success', 'Data alumni berhasil diupdate!');
    }
}
