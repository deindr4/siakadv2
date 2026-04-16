<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\PoinPositifSiswa;
use App\Models\JenisKegiatanPositif;
use App\Models\PelanggaranSiswa;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\Semester;
use Illuminate\Http\Request;

class PoinPositifController extends Controller
{
    public function index(Request $request)
    {
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('semester_id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);

        $query = PoinPositifSiswa::with(['siswa', 'jenisKegiatan', 'dicatatOleh'])
            ->where('status', 'aktif');

        if ($semesterId) $query->where('semester_id', $semesterId);

        if ($request->filled('kategori')) {
            $query->whereHas('jenisKegiatan', fn($q) => $q->where('kategori', $request->kategori));
        }

        if ($request->filled('rombel')) {
            $query->whereHas('siswa', fn($q) => $q->where('rombongan_belajar_id', $request->rombel));
        }

        if ($request->filled('search')) {
            $query->whereHas('siswa', fn($q) => $q->where('nama', 'like', '%'.$request->search.'%')
                ->orWhere('nisn', 'like', '%'.$request->search.'%'));
        }

        $records = $query->orderByDesc('tanggal')->paginate(20)->withQueryString();

        $jenisList  = JenisKegiatanPositif::where('is_active', true)->orderBy('kategori')->get();
        $rombels    = Rombel::where('is_archived', false)->where('jenis_rombel', '1')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->orderBy('tingkat')->orderBy('nama_rombel')->get();
        $siswaList  = Siswa::where('is_archived', false)->where('status_mutasi', 'aktif')
            ->get(['id', 'nama', 'nisn', 'rombongan_belajar_id']);

        $totalHariIni  = PoinPositifSiswa::where('status','aktif')->whereDate('tanggal', today())
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))->count();
        $totalBulanIni = PoinPositifSiswa::where('status','aktif')->whereMonth('tanggal', now()->month)
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))->count();
        $totalSemester = PoinPositifSiswa::where('status','aktif')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))->count();

        return view('bk.poin_positif.index', compact(
            'records', 'jenisList', 'rombels', 'siswaList', 'semesters',
            'semesterAktif', 'semesterId',
            'totalHariIni', 'totalBulanIni', 'totalSemester'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id'          => 'required|exists:siswas,id',
            'jenis_kegiatan_id' => 'required|exists:jenis_kegiatan_positif,id',
            'tanggal'           => 'required|date',
            'semester_id'       => 'required|exists:semesters,id',
        ]);

        $jenis = JenisKegiatanPositif::findOrFail($request->jenis_kegiatan_id);

        PoinPositifSiswa::create([
            'semester_id'       => $request->semester_id,
            'siswa_id'          => $request->siswa_id,
            'jenis_kegiatan_id' => $request->jenis_kegiatan_id,
            'dicatat_oleh'      => auth()->id(),
            'tanggal'           => $request->tanggal,
            'poin'              => $jenis->poin,
            'keterangan'        => $request->keterangan,
            'status'            => 'aktif',
        ]);

        return back()->with('success', 'Poin kebaikan berhasil dicatat!');
    }

    public function destroy(PoinPositifSiswa $poinPositif)
    {
        $poinPositif->update(['status' => 'dibatalkan']);
        return back()->with('success', 'Poin kebaikan berhasil dibatalkan!');
    }

    // ── Master Jenis Kegiatan ──────────────────────────────────────
    public function jenisIndex()
    {
        $jenis = JenisKegiatanPositif::orderBy('kategori')->orderBy('nama')->paginate(20);
        return view('bk.poin_positif.jenis', compact('jenis'));
    }

    public function jenisStore(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'kategori' => 'required|in:akademik,olahraga_seni,organisasi,sosial_keagamaan,lainnya',
            'poin'     => 'required|integer|min:1|max:50',
        ]);

        JenisKegiatanPositif::create($request->only(['nama','kategori','poin','keterangan','is_active']));
        return back()->with('success', 'Jenis kegiatan berhasil ditambahkan!');
    }

    public function jenisUpdate(Request $request, JenisKegiatanPositif $jenis)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'kategori' => 'required|in:akademik,olahraga_seni,organisasi,sosial_keagamaan,lainnya',
            'poin'     => 'required|integer|min:1|max:50',
        ]);

        $jenis->update($request->only(['nama','kategori','poin','keterangan','is_active']));
        return back()->with('success', 'Jenis kegiatan berhasil diupdate!');
    }

    public function jenisDestroy(JenisKegiatanPositif $jenis)
    {
        $jenis->update(['is_active' => false]);
        return back()->with('success', 'Jenis kegiatan dinonaktifkan!');
    }

    // ── Rekap Net Poin per Siswa ───────────────────────────────────
    public function rekap(Request $request)
    {
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('semester_id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);

        $rombels = Rombel::where('is_archived', false)->where('jenis_rombel', '1')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->orderBy('tingkat')->orderBy('nama_rombel')->get();

        $query = Siswa::where('is_archived', false)->where('status_mutasi', 'aktif');

        if ($request->filled('rombel')) {
            $query->where('rombongan_belajar_id', $request->rombel);
        }
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%'.$request->search.'%');
        }

        $siswaList = $query->orderBy('nama')->paginate(20)->withQueryString();

        // Hitung poin per siswa
        $siswaList->each(function ($siswa) use ($semesterId) {
            $siswa->poin_pelanggaran = PelanggaranSiswa::where('siswa_id', $siswa->id)
                ->where('status', '!=', 'dibatalkan')
                ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
                ->sum('poin');

            $siswa->poin_positif = PoinPositifSiswa::where('siswa_id', $siswa->id)
                ->where('status', 'aktif')
                ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
                ->sum('poin');

            $siswa->net_poin = max(0, $siswa->poin_pelanggaran - $siswa->poin_positif);
        });

        return view('bk.poin_positif.rekap', compact(
            'siswaList', 'rombels', 'semesters', 'semesterAktif', 'semesterId'
        ));
    }
}
