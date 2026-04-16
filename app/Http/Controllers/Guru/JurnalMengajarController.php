<?php

// app/Http/Controllers/Guru/JurnalMengajarController.php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JurnalMengajar;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\Semester;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JurnalMengajarController extends Controller
{
    private function getGuru()
    {
        return Guru::where('user_id', auth()->id())->first();
    }

    public function index(Request $request)
    {
        $guru          = $this->getGuru();
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('semester_id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);
        $isAdmin       = auth()->user()->hasRole('admin');

        $query = JurnalMengajar::with(['guru', 'mataPelajaran', 'semester']);

        // Guru hanya lihat jurnal sendiri
        if (!$isAdmin) {
            if (!$guru) return redirect()->back()->with('error', 'Data guru tidak ditemukan!');
            $query->where('guru_id', $guru->id);
        }

        if ($semesterId) $query->where('semester_id', $semesterId);

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        if ($request->filled('guru_id') && $isAdmin) {
            $query->where('guru_id', $request->guru_id);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('materi', 'like', '%'.$request->search.'%')
                  ->orWhere('nama_rombel', 'like', '%'.$request->search.'%')
                  ->orWhereHas('mataPelajaran', fn($q2) => $q2->where('nama', 'like', '%'.$request->search.'%'));
            });
        }

        $jurnals = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(15)->withQueryString();

        // Stat
        $totalBulanIni = JurnalMengajar::when(!$isAdmin && $guru, fn($q) => $q->where('guru_id', $guru?->id))
            ->whereMonth('tanggal', now()->month)->count();
        $totalSemester = JurnalMengajar::when(!$isAdmin && $guru, fn($q) => $q->where('guru_id', $guru?->id))
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))->count();

        $mapels   = MataPelajaran::where('is_active', true)->orderBy('nama')->get();
        $rombels  = Rombel::where('is_archived', false)->where('jenis_rombel', '1')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->orderBy('tingkat')->orderBy('nama_rombel')->get();
        $guruList = $isAdmin ? Guru::where('is_archived', false)->orderBy('nama')->get() : collect();

        return view('guru.jurnal.index', compact(
            'jurnals', 'mapels', 'rombels', 'semesters', 'semesterAktif',
            'semesterId', 'guru', 'isAdmin', 'totalBulanIni', 'totalSemester', 'guruList'
        ));
    }

    public function create()
    {
        $guru          = $this->getGuru();
        $isAdmin       = auth()->user()->hasRole('admin');
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('semester_id')->get();
        $mapels        = MataPelajaran::where('is_active', true)->orderBy('nama')->get();
        $rombels       = Rombel::where('is_archived', false)->where('jenis_rombel', '1')
            ->when($semesterAktif, fn($q) => $q->where('semester_id', $semesterAktif->id))
            ->orderBy('tingkat')->orderBy('nama_rombel')->get();
        $guruList      = $isAdmin ? Guru::where('is_archived', false)->orderBy('nama')->get() : collect();

        // Hitung pertemuan terakhir
        $pertemuanTerakhir = 0;
        if ($guru) {
            $pertemuanTerakhir = JurnalMengajar::where('guru_id', $guru->id)
                ->where('semester_id', $semesterAktif?->id)
                ->max('pertemuan_ke') ?? 0;
        }

        return view('guru.jurnal.create', compact(
            'guru', 'isAdmin', 'semesterAktif', 'semesters', 'mapels',
            'rombels', 'guruList', 'pertemuanTerakhir'
        ));
    }

    public function store(Request $request)
    {
        $isAdmin = auth()->user()->hasRole('admin');
        $guru    = $this->getGuru();

        $request->validate([
            'semester_id'       => 'required|exists:semesters,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'nama_rombel'       => 'required|string',
            'tanggal'           => 'required|date',
            'materi'            => 'required|string',
            'kegiatan'          => 'required|string',
            'tanda_tangan'      => 'required|string',
            'foto_pendukung'    => 'nullable|image|max:10240', // max 10MB sebelum compress
        ]);

        // Guru hanya bisa input hari ini
        if (!$isAdmin && $request->tanggal !== date('Y-m-d')) {
            return back()->with('error', 'Jurnal hanya bisa diisi untuk hari ini!')->withInput();
        }

        // Tentukan guru_id
        $guruId = $isAdmin && $request->filled('guru_id') ? $request->guru_id : $guru?->id;
        if (!$guruId) return back()->with('error', 'Data guru tidak ditemukan!');

        // Upload foto - sudah dicompress di client-side (JavaScript)
        $fotoPath = null;
        if ($request->hasFile('foto_pendukung')) {
            $foto      = $request->file('foto_pendukung');
            $filename  = 'jurnal_'.time().'_'.uniqid().'.jpg';
            $directory = 'jurnal/foto';

            $fullPath = storage_path('app/public/'.$directory);
            if (!file_exists($fullPath)) mkdir($fullPath, 0755, true);

            $foto->move($fullPath, $filename);
            $fotoPath = $directory.'/'.$filename;
        }

        JurnalMengajar::create([
            'semester_id'           => $request->semester_id,
            'guru_id'               => $guruId,
            'mata_pelajaran_id'     => $request->mata_pelajaran_id,
            'nama_rombel'           => $request->nama_rombel,
            'rombongan_belajar_id'  => $request->rombongan_belajar_id,
            'tanggal'               => $request->tanggal,
            'jam_ke'                => $request->jam_ke,
            'jam_mulai'             => $request->jam_mulai,
            'jam_selesai'           => $request->jam_selesai,
            'pertemuan_ke'          => $request->pertemuan_ke,
            'materi'                => $request->materi,
            'kegiatan'              => $request->kegiatan,
            'catatan'               => $request->catatan,
            'jumlah_hadir'          => $request->jumlah_hadir,
            'jumlah_tidak_hadir'    => $request->jumlah_tidak_hadir,
            'foto_pendukung'        => $fotoPath,
            'tanda_tangan'          => $request->tanda_tangan,
            'status'                => 'submitted',
        ]);

        $redirect = $isAdmin ? route('admin.jurnal.index') : route('guru.jurnal.index');
        return redirect($redirect)->with('success', 'Jurnal mengajar berhasil disimpan!');
    }

    public function show(JurnalMengajar $jurnal)
    {
        $isAdmin = auth()->user()->hasRole('admin');
        return view('guru.jurnal.show', compact('jurnal', 'isAdmin'));
    }

    public function destroy(JurnalMengajar $jurnal)
    {
        if (!auth()->user()->hasRole('admin')) {
            return back()->with('error', 'Tidak memiliki akses!');
        }

        // Hapus foto jika ada
        if ($jurnal->foto_pendukung) {
            $fullPath = storage_path('app/public/'.$jurnal->foto_pendukung);
            if (file_exists($fullPath)) unlink($fullPath);
        }

        $jurnal->delete();
        return back()->with('success', 'Jurnal berhasil dihapus!');
    }
}
