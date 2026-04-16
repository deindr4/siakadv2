<?php

namespace App\Http\Controllers;

use App\Models\AbsensiHarian;
use App\Models\AbsensiSiswa;
use App\Models\Rombel;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    // ==========================================
    // INDEX - Daftar absensi
    // ==========================================
    public function index(Request $request)
    {
        $isAdmin       = auth()->user()->hasRole('admin');
        $isGuru        = auth()->user()->hasRole('guru');
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);

        $rombels = Rombel::where('is_archived', false)
            ->where('jenis_rombel', '1')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->orderBy('tingkat')->orderBy('nama_rombel')->get();

        $query = AbsensiHarian::with(['guru', 'semester'])
            ->withCount([
                'absensiSiswa as hadir_count'      => fn($q) => $q->where('status', 'H'),
                'absensiSiswa as sakit_count'      => fn($q) => $q->where('status', 'S'),
                'absensiSiswa as izin_count'       => fn($q) => $q->where('status', 'I'),
                'absensiSiswa as alpa_count'       => fn($q) => $q->where('status', 'A'),
                'absensiSiswa as dispensasi_count' => fn($q) => $q->where('status', 'D'),
            ])
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->when($request->filled('rombel_id'), fn($q) => $q->where('rombongan_belajar_id', $request->rombel_id))
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal', $request->bulan))
            ->when($request->filled('tanggal'), fn($q) => $q->whereDate('tanggal', $request->tanggal))
            ->orderByDesc('tanggal');

        $absensi = $query->paginate(20)->withQueryString();

        // Cek absensi hari ini sudah ada belum (untuk tombol absen) - pakai integer ID rombel
        $absensiHariIni = AbsensiHarian::whereDate('tanggal', today())
            ->where('semester_id', $semesterId)
            ->pluck('rombongan_belajar_id')
            ->toArray();

        $user = auth()->user();
        if ($user->hasRole('admin'))  $routePrefix = 'admin';
        elseif ($user->hasRole('bk')) $routePrefix = 'bk';
        else                          $routePrefix = 'guru';

        return view('absensi.index', compact(
            'absensi', 'rombels', 'semesters', 'semesterAktif',
            'semesterId', 'isAdmin', 'isGuru', 'absensiHariIni', 'routePrefix'
        ));
    }

    // ==========================================
    // CREATE - Form input absensi
    // ==========================================
    public function create(Request $request)
    {
        $isAdmin       = auth()->user()->hasRole('admin');
        $isBK          = auth()->user()->hasRole('bk');
        $canBackdate   = $isAdmin || $isBK; // Admin & BK boleh pilih tanggal sebelumnya
        $semesterAktif = Semester::aktif();
        $tanggal       = $request->get('tanggal', date('Y-m-d'));
        $rombelId      = $request->get('rombel_id'); // integer ID dari tabel rombels

        $rombels = Rombel::where('is_archived', false)
            ->where('jenis_rombel', '1')
            ->when($semesterAktif, fn($q) => $q->where('semester_id', $semesterAktif->id))
            ->orderBy('tingkat')->orderBy('nama_rombel')->get();

        $existingAbsensi = null;
        $siswas          = collect();
        $rombel          = null;
        $isLocked        = false;

        if ($rombelId) {
            // Ambil rombel - dapatkan UUID rombongan_belajar_id
            $rombel = Rombel::find($rombelId);

            if (!$rombel) {
                return redirect()->back()->with('error', 'Kelas tidak ditemukan!');
            }

            // Cek apakah sudah ada absensi hari ini (pakai integer ID)
            $existingAbsensi = AbsensiHarian::with('absensiSiswa.siswa')
                ->where('rombongan_belajar_id', $rombelId)
                ->whereDate('tanggal', $tanggal)
                ->first();

            $isLocked = $existingAbsensi?->is_locked && !$canBackdate;

            // PENTING: Siswa memakai UUID rombongan_belajar_id dari dapodik
            // bukan integer ID dari tabel rombels
            $siswas = Siswa::where('rombongan_belajar_id', $rombel->rombongan_belajar_id)
                ->where(function ($q) {
                    $q->where('is_archived', false)->orWhereNull('is_archived');
                })
                ->whereNotIn('status', ['mutasi', 'keluar'])
                ->orderBy('nama')
                ->get();
        }

        $user = auth()->user();
        if ($user->hasRole('admin'))  $routePrefix = 'admin';
        elseif ($user->hasRole('bk')) $routePrefix = 'bk';
        else                          $routePrefix = 'guru';

        return view('absensi.create', compact(
            'rombels', 'rombel', 'semesterAktif', 'tanggal',
            'rombelId', 'siswas', 'existingAbsensi', 'isLocked', 'isAdmin', 'canBackdate',
            'routePrefix'
        ));
    }

    // ==========================================
    // STORE - Simpan absensi
    // ==========================================
    public function store(Request $request)
    {
        $isAdmin     = auth()->user()->hasRole('admin');
        $isBK        = auth()->user()->hasRole('bk');
        $canBackdate = $isAdmin || $isBK; // Admin & BK boleh input hari sebelumnya
        $guru        = Guru::where('user_id', auth()->id())->first();

        $request->validate([
            'semester_id'          => 'required|exists:semesters,id',
            'rombongan_belajar_id' => 'required|exists:rombels,id',
            'tanggal'              => 'required|date',
            'siswa'                => 'required|array',
            'siswa.*.status'       => 'required|in:H,S,I,A,D',
        ]);

        // Guru hanya bisa absen hari ini, Admin & BK boleh hari sebelumnya
        if (!$canBackdate && $request->tanggal !== date('Y-m-d')) {
            return back()->with('error', 'Absensi hanya bisa dilakukan untuk hari ini!')->withInput();
        }

        // Tanggal tidak boleh lebih dari hari ini (semua role)
        if ($request->tanggal > date('Y-m-d')) {
            return back()->with('error', 'Tidak bisa input absensi untuk tanggal yang akan datang!')->withInput();
        }

        // Cek sudah ada absensi & terkunci
        $existing = AbsensiHarian::where('rombongan_belajar_id', $request->rombongan_belajar_id)
            ->whereDate('tanggal', $request->tanggal)
            ->first();

        if ($existing && $existing->is_locked && !$canBackdate) {
            return back()->with('error', 'Absensi kelas ini sudah terkunci! Tidak bisa diubah.');
        }

        $rombel = Rombel::find($request->rombongan_belajar_id);

        DB::transaction(function () use ($request, $rombel, $guru) {

            $absensiHarian = AbsensiHarian::updateOrCreate(
                [
                    'rombongan_belajar_id' => $request->rombongan_belajar_id,
                    'tanggal'              => $request->tanggal,
                ],
                [
                    'semester_id'  => $request->semester_id,
                    'nama_rombel'  => $rombel?->nama_rombel,
                    'guru_id'      => $guru?->id,
                    'nama_guru'    => $guru?->nama ?? auth()->user()->name,
                    'diabsen_pada' => now(),
                    'ip_address'   => request()->ip(),
                    'is_locked'    => true,
                    'locked_at'    => now(),
                    'catatan'      => $request->catatan,
                ]
            );

            // Hapus detail lama
            $absensiHarian->absensiSiswa()->delete();

            // Simpan per siswa
            foreach ($request->siswa as $siswaId => $data) {
                AbsensiSiswa::create([
                    'absensi_harian_id' => $absensiHarian->id,
                    'siswa_id'          => $siswaId,
                    'status'            => $data['status'],
                    'keterangan'        => $data['keterangan'] ?? null,
                ]);
            }
        });

        $user = auth()->user();
        if ($user->hasRole('admin'))       $redirect = route('admin.absensi.index');
        elseif ($user->hasRole('bk'))      $redirect = route('bk.absensi.index');
        else                               $redirect = route('guru.absensi.index');
        return redirect($redirect)->with('success', 'Absensi berhasil disimpan dan dikunci!');
    }

    // ==========================================
    // SHOW - Detail absensi
    // ==========================================
    public function show(AbsensiHarian $absensi)
    {
        $absensi->load(['absensiSiswa.siswa', 'guru', 'semester']);
        $isAdmin = auth()->user()->hasRole('admin');
        return view('absensi.show', compact('absensi', 'isAdmin'));
    }

    // ==========================================
    // EDIT - Edit absensi (admin only)
    // ==========================================
    public function edit(AbsensiHarian $absensi)
    {
        if (!auth()->user()->hasRole('admin')) {
            return back()->with('error', 'Tidak memiliki akses!');
        }

        $absensi->load(['absensiSiswa.siswa', 'semester']);
        $semesterAktif = Semester::aktif();

        return view('absensi.edit', compact('absensi', 'semesterAktif'));
    }

    // ==========================================
    // UPDATE - Update absensi (admin only)
    // ==========================================
    public function update(Request $request, AbsensiHarian $absensi)
    {
        if (!auth()->user()->hasRole('admin')) {
            return back()->with('error', 'Tidak memiliki akses!');
        }

        $request->validate([
            'siswa'          => 'required|array',
            'siswa.*.status' => 'required|in:H,S,I,A,D',
        ]);

        DB::transaction(function () use ($request, $absensi) {
            $absensi->update([
                'catatan'   => $request->catatan,
                'is_locked' => $request->has('is_locked'),
            ]);

            foreach ($request->siswa as $siswaId => $data) {
                AbsensiSiswa::updateOrCreate(
                    [
                        'absensi_harian_id' => $absensi->id,
                        'siswa_id'          => $siswaId,
                    ],
                    [
                        'status'     => $data['status'],
                        'keterangan' => $data['keterangan'] ?? null,
                    ]
                );
            }
        });

        return redirect()->route('admin.absensi.index')
            ->with('success', 'Absensi berhasil diperbarui!');
    }

    // ==========================================
    // TOGGLE LOCK - Admin unlock/lock
    // ==========================================
    public function toggleLock(AbsensiHarian $absensi)
    {
        if (!auth()->user()->hasRole('admin')) {
            return back()->with('error', 'Tidak memiliki akses!');
        }

        $absensi->update([
            'is_locked' => !$absensi->is_locked,
            'locked_by' => auth()->id(),
            'locked_at' => now(),
        ]);

        $status = $absensi->is_locked ? 'dikunci' : 'dibuka';
        return back()->with('success', "Absensi berhasil {$status}!");
    }

    // ==========================================
    // DESTROY - Hapus absensi (admin only)
    // ==========================================
    public function destroy(AbsensiHarian $absensi)
    {
        if (!auth()->user()->hasRole('admin')) {
            return back()->with('error', 'Tidak memiliki akses!');
        }

        $absensi->absensiSiswa()->delete();
        $absensi->delete();

        return back()->with('success', 'Absensi berhasil dihapus!');
    }

    // ==========================================
    // SISWA - Lihat absensi diri sendiri
    // ==========================================
    public function milikSiswa(Request $request)
    {
        $siswa = Siswa::where('user_id', auth()->id())->first();
        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan!');
        }

        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);

        $absensi = AbsensiSiswa::with(['absensiHarian'])
            ->where('siswa_id', $siswa->id)
            ->whereHas('absensiHarian', function ($q) use ($semesterId, $request) {
                $q->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
                  ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal', $request->bulan));
            })
            ->orderByDesc(
                AbsensiHarian::select('tanggal')
                    ->whereColumn('id', 'absensi_siswa.absensi_harian_id')
                    ->limit(1)
            )
            ->paginate(20)->withQueryString();

        // Rekap total per status
        $rekap = AbsensiSiswa::where('siswa_id', $siswa->id)
            ->whereHas('absensiHarian', fn($q) => $q->when($semesterId, fn($q) => $q->where('semester_id', $semesterId)))
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('absensi.siswa', compact(
            'absensi', 'siswa', 'semesters', 'semesterAktif', 'semesterId', 'rekap'
        ));
    }
}
