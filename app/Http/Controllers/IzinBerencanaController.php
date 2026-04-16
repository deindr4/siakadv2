<?php

namespace App\Http\Controllers;

use App\Models\IzinBerencana;
use App\Models\Siswa;
use App\Models\Semester;
use App\Models\Rombel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IzinBerencanaController extends Controller
{
    // ── INDEX (semua role) ─────────────────────────────────────────
    public function index(Request $request)
    {
        $user          = Auth::user();
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('semester_id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);

        $query = IzinBerencana::with(['siswa', 'disetujuiOleh'])
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId));

        // Siswa hanya lihat miliknya sendiri
        if ($user->hasRole('siswa')) {
            $siswa = Siswa::where('user_id', $user->id)->firstOrFail();
            $query->where('siswa_id', $siswa->id);
        }

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('jenis'))  $query->where('jenis', $request->jenis);

        if ($request->filled('rombel')) {
            $query->whereHas('siswa', fn($q) => $q->where('rombongan_belajar_id', $request->rombel));
        }

        if ($request->filled('search')) {
            $query->whereHas('siswa', fn($q) => $q->where('nama', 'like', '%'.$request->search.'%'));
        }

        $izinList = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $rombels = Rombel::where('is_archived', false)->where('jenis_rombel','1')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->orderBy('tingkat')->orderBy('nama_rombel')->get();

        // Stats
        $baseQuery = IzinBerencana::when($semesterId, fn($q) => $q->where('semester_id', $semesterId));
        if ($user->hasRole('siswa')) {
            $siswaId = Siswa::where('user_id', $user->id)->value('id');
            $baseQuery->where('siswa_id', $siswaId);
        }
        $stats = (object)[
            'pending'   => (clone $baseQuery)->where('status','pending')->count(),
            'disetujui' => (clone $baseQuery)->where('status','disetujui')->count(),
            'ditolak'   => (clone $baseQuery)->where('status','ditolak')->count(),
            'total'     => (clone $baseQuery)->count(),
        ];

        return view('izin.index', compact(
            'izinList', 'rombels', 'semesters', 'semesterAktif', 'semesterId', 'stats'
        ));
    }

    // ── CREATE (siswa) ─────────────────────────────────────────────
    public function create()
    {
        $semesterAktif = Semester::aktif();
        return view('izin.create', compact('semesterAktif'));
    }

    // ── STORE (siswa) ──────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'jenis'          => 'required|in:keperluan_keluarga,perjalanan_wisata,lainnya',
            'alasan'         => 'required|string|min:10|max:500',
            'tanggal_mulai'  => 'required|date|after_or_equal:today',
            'tanggal_selesai'=> 'required|date|after_or_equal:tanggal_mulai',
            'nama_ortu'      => 'required|string|max:100',
            'no_hp_ortu'     => 'required|string|max:20',
            'ttd_ortu'       => 'required|string', // base64 signature
        ], [
            'alasan.min'             => 'Alasan minimal 10 karakter.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh hari yang sudah lewat.',
            'ttd_ortu.required'      => 'Tanda tangan orang tua wajib diisi.',
        ]);

        $user  = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->firstOrFail();
        $sem   = Semester::aktif();

        $mulai    = Carbon::parse($request->tanggal_mulai);
        $selesai  = Carbon::parse($request->tanggal_selesai);
        $jumlahHari = $mulai->diffInDays($selesai) + 1;

        // Cek maks 2 hari untuk pengajuan mandiri
        $warning = null;
        if ($jumlahHari > 2) {
            $warning = 'Izin lebih dari 2 hari memerlukan persetujuan khusus dari Kepala Sekolah.';
        }

        $izin = IzinBerencana::create([
            'nomor_izin'      => IzinBerencana::generateNomor(),
            'semester_id'     => $sem?->id,
            'siswa_id'        => $siswa->id,
            'jenis'           => $request->jenis,
            'alasan'          => $request->alasan,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jumlah_hari'     => $jumlahHari,
            'nama_ortu'       => $request->nama_ortu,
            'no_hp_ortu'      => $request->no_hp_ortu,
            'ttd_ortu'        => $request->ttd_ortu,
            'status'          => 'pending',
        ]);

        $msg = 'Izin berhasil diajukan! Nomor: ' . $izin->nomor_izin;
        if ($warning) $msg .= ' ⚠️ ' . $warning;

        return redirect()->route('izin.index')->with('success', $msg);
    }

    // ── SHOW ───────────────────────────────────────────────────────
    public function show(IzinBerencana $izin)
    {
        $user = Auth::user();
        if ($user->hasRole('siswa')) {
            $siswa = Siswa::where('user_id', $user->id)->firstOrFail();
            abort_if($izin->siswa_id !== $siswa->id, 403);
        }
        return view('izin.show', compact('izin'));
    }

    // ── APPROVE (kepsek / admin) ───────────────────────────────────
    public function approve(Request $request, IzinBerencana $izin)
    {
        $request->validate([
            'jumlah_hari_disetujui' => 'required|integer|min:1|max:30',
            'catatan_approver'      => 'nullable|string|max:255',
        ]);

        abort_if(!Auth::user()->hasAnyRole(['admin','kepala_sekolah']), 403);
        abort_if($izin->status !== 'pending', 422, 'Izin sudah diproses.');

        $izin->update([
            'status'                => 'disetujui',
            'disetujui_oleh'        => Auth::id(),
            'disetujui_pada'        => now(),
            'jumlah_hari_disetujui' => $request->jumlah_hari_disetujui,
            'catatan_approver'      => $request->catatan_approver,
        ]);

        return back()->with('success', 'Izin ' . $izin->nomor_izin . ' berhasil disetujui!');
    }

    // ── TOLAK (kepsek / admin) ─────────────────────────────────────
    public function tolak(Request $request, IzinBerencana $izin)
    {
        $request->validate([
            'catatan_approver' => 'required|string|max:255',
        ], ['catatan_approver.required' => 'Alasan penolakan wajib diisi.']);

        abort_if(!Auth::user()->hasAnyRole(['admin','kepala_sekolah']), 403);
        abort_if($izin->status !== 'pending', 422, 'Izin sudah diproses.');

        $izin->update([
            'status'           => 'ditolak',
            'disetujui_oleh'   => Auth::id(),
            'disetujui_pada'   => now(),
            'catatan_approver' => $request->catatan_approver,
        ]);

        return back()->with('success', 'Izin ' . $izin->nomor_izin . ' ditolak.');
    }

    // ── BATALKAN (siswa, hanya jika masih pending) ─────────────────
    public function batalkan(IzinBerencana $izin)
    {
        $user  = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->firstOrFail();

        abort_if($izin->siswa_id !== $siswa->id, 403);
        abort_if($izin->status !== 'pending', 422, 'Izin sudah diproses, tidak bisa dibatalkan.');

        $izin->update(['status' => 'dibatalkan']);
        return back()->with('success', 'Pengajuan izin berhasil dibatalkan.');
    }

    // ── LAPORAN ────────────────────────────────────────────────────
    public function laporan(Request $request)
    {
        $semesters  = Semester::orderByDesc('semester_id')->get();
        $semesterId = $request->get('semester_id', Semester::aktif()?->id);

        $query = IzinBerencana::with(['siswa','disetujuiOleh'])
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('jenis'),  fn($q) => $q->where('jenis', $request->jenis));

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_mulai', $request->bulan);
        }

        $data = $query->orderByDesc('tanggal_mulai')->get();

        // Rekap per jenis
        $rekapJenis = $data->groupBy('jenis')->map(fn($g) => [
            'total'     => $g->count(),
            'disetujui' => $g->where('status','disetujui')->count(),
            'hari'      => $g->where('status','disetujui')->sum('jumlah_hari_disetujui'),
        ]);

        // Rekap per bulan
        $rekapBulan = $data->where('status','disetujui')
            ->groupBy(fn($i) => Carbon::parse($i->tanggal_mulai)->format('Y-m'))
            ->map(fn($g) => ['total' => $g->count(), 'hari' => $g->sum('jumlah_hari_disetujui')]);

        return view('izin.laporan', compact(
            'data', 'semesters', 'semesterId', 'rekapJenis', 'rekapBulan'
        ));
    }

    // ── CETAK SURAT (siswa setelah disetujui) ─────────────────────────
    public function cetakSurat(Request $request, IzinBerencana $izin)
    {
        abort_if($izin->status !== 'disetujui', 403, 'Surat izin hanya bisa dicetak setelah disetujui.');

        $user = Auth::user();
        if ($user->hasRole('siswa')) {
            $siswa = Siswa::where('user_id', $user->id)->firstOrFail();
            abort_if($izin->siswa_id !== $siswa->id, 403);
        }

        $settings = \App\Models\Setting::allAsArray();
        $ttd = [
            'nama_kepsek'     => $request->get('nama_kepsek', $izin->disetujuiOleh?->name ?? ''),
            'nip_kepsek'      => $request->get('nip_kepsek', ''),
            'golongan_kepsek' => $request->get('golongan_kepsek', ''),
            'tempat_ttd'      => $request->get('tempat_ttd', $settings['kabupaten'] ?? ''),
            'tanggal_surat'   => $request->get('tanggal_surat', now()->format('Y-m-d')),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('izin.surat-pdf', compact('izin', 'settings', 'ttd'))
            ->setPaper('a4', 'portrait');

        $filename = 'Surat-Izin-' . $izin->nomor_izin . '-' . str_replace(' ', '-', $izin->siswa?->nama ?? 'siswa') . '.pdf';

        return $pdf->stream($filename);
    }

}
