<?php
// app/Http/Controllers/TiketController.php

namespace App\Http\Controllers;

use App\Models\Tiket;
use App\Models\TiketRespon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TiketController extends Controller
{
    // ============================================================
    // INDEX
    // ============================================================
    public function index(Request $request)
    {
        $user    = Auth::user();
        $isAdmin = $user->hasRole('admin') || $user->hasRole('kepala_sekolah');

        // Auto-lock tiket yang sudah 7 hari tidak ada respon
        $this->autoLockTikets();

        $query = Tiket::with(['user', 'respon'])
            ->when($request->filled('kategori'), fn($q) => $q->where('kategori', $request->kategori))
            ->when($request->filled('status'),   fn($q) => $q->where('status', $request->status))
            ->when($request->filled('search'),   fn($q) => $q->where('judul', 'like', '%'.$request->search.'%'));

        // Siswa & Guru hanya lihat tiket sendiri
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        $tikets   = $query->orderByRaw("FIELD(status, 'open', 'diproses', 'selesai', 'terkunci')")
                          ->orderByDesc('created_at')
                          ->paginate(20)->withQueryString();

        $stats = (object)[
            'total'    => $isAdmin ? Tiket::count() : Tiket::where('user_id', $user->id)->count(),
            'open'     => $isAdmin ? Tiket::whereIn('status',['open','diproses'])->count() : Tiket::where('user_id',$user->id)->whereIn('status',['open','diproses'])->count(),
            'selesai'  => $isAdmin ? Tiket::where('status','selesai')->count() : Tiket::where('user_id',$user->id)->where('status','selesai')->count(),
            'terkunci' => $isAdmin ? Tiket::where('status','terkunci')->count() : 0,
        ];

        return view('tiket.index', compact('tikets', 'stats', 'isAdmin'));
    }

    // ============================================================
    // CREATE
    // ============================================================
    public function create()
    {
        $user = Auth::user();
        if (!$user->hasRole('siswa') && !$user->hasRole('guru') && !$user->hasRole('wakil_kepala_sekolah')) {
            return redirect()->route('tiket.index')->with('error', 'Hanya siswa dan guru yang bisa membuat tiket.');
        }

        return view('tiket.create', [
            'kategoriList' => Tiket::kategoriList(),
        ]);
    }

    // ============================================================
    // STORE
    // ============================================================
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('siswa') && !$user->hasRole('guru') && !$user->hasRole('wakil_kepala_sekolah')) {
            abort(403);
        }

        $request->validate([
            'judul'            => 'required|string|max:200',
            'kategori'         => 'required|in:' . implode(',', array_keys(Tiket::kategoriList())),
            'kategori_lainnya' => 'nullable|string|max:100',
            'isi'              => 'required|string|min:10',
            'is_anonim'        => 'nullable|boolean',
            'prioritas'        => 'nullable|in:rendah,sedang,tinggi',
            'foto'             => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ], [
            'judul.required' => 'Judul tiket wajib diisi',
            'isi.required'   => 'Isi tiket wajib diisi',
            'isi.min'        => 'Isi tiket minimal 10 karakter',
        ]);

        $fotoPath = null;
        $fotoOriginal = null;
        if ($request->hasFile('foto')) {
            ['path' => $fotoPath, 'original' => $fotoOriginal] = $this->uploadFoto($request->file('foto'), 'tiket');
        }

        $tiket = Tiket::create([
            'user_id'           => $user->id,
            'role_pembuat'      => $user->getRoleNames()->first(),
            'is_anonim'         => $request->boolean('is_anonim'),
            'judul'             => $request->judul,
            'kategori'          => $request->kategori,
            'kategori_lainnya'  => $request->kategori_lainnya,
            'isi'               => $request->isi,
            'foto'              => $fotoPath,
            'foto_original'     => $fotoOriginal,
            'status'            => 'open',
            'prioritas'         => $request->prioritas ?? 'sedang',
            'last_response_at'  => now(),
        ]);

        return redirect()->route('tiket.show', $tiket)->with('success', 'Tiket berhasil dibuat! Kami akan segera merespons.');
    }

    // ============================================================
    // SHOW + Thread Respon
    // ============================================================
    public function show(Tiket $tiket)
    {
        $user    = Auth::user();
        $isAdmin = $user->hasRole('admin') || $user->hasRole('kepala_sekolah');

        // Siswa/guru hanya bisa lihat tiket sendiri
        if (!$isAdmin && $tiket->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        $tiket->load(['user', 'respon.user', 'lockedBy', 'unlockedBy', 'closedBy']);

        return view('tiket.show', compact('tiket', 'isAdmin'));
    }

    // ============================================================
    // RESPON (Balas tiket)
    // ============================================================
    public function respon(Request $request, Tiket $tiket)
    {
        $user    = Auth::user();
        $isAdmin = $user->hasRole('admin') || $user->hasRole('kepala_sekolah');

        // Cek akses
        if (!$isAdmin && $tiket->user_id !== $user->id) abort(403);

        // Cek tiket terkunci
        if ($tiket->isLocked()) {
            return back()->with('error', 'Tiket ini terkunci. Hanya Admin atau Kepala Sekolah yang bisa membukanya.');
        }

        if ($tiket->status === 'selesai') {
            return back()->with('error', 'Tiket ini sudah ditandai selesai.');
        }

        $request->validate([
            'isi'       => 'required|string|min:2',
            'is_anonim' => 'nullable|boolean',
            'foto'      => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $fotoPath = null;
        $fotoOriginal = null;
        if ($request->hasFile('foto')) {
            ['path' => $fotoPath, 'original' => $fotoOriginal] = $this->uploadFoto($request->file('foto'), 'tiket/respon');
        }

        DB::transaction(function () use ($request, $tiket, $user, $isAdmin, $fotoPath, $fotoOriginal) {
            TiketRespon::create([
                'tiket_id'       => $tiket->id,
                'user_id'        => $user->id,
                'role_responder' => $user->getRoleNames()->first(),
                'is_anonim'      => !$isAdmin && $request->boolean('is_anonim'),
                'isi'            => $request->isi,
                'foto'           => $fotoPath,
                'foto_original'  => $fotoOriginal,
            ]);

            // Update status jadi diproses kalau admin/kepsek yang balas
            $newStatus = $tiket->status;
            if ($isAdmin && $tiket->status === 'open') {
                $newStatus = 'diproses';
            }

            $tiket->update([
                'status'           => $newStatus,
                'last_response_at' => now(),
            ]);
        });

        return back()->with('success', 'Respon berhasil dikirim!');
    }

    // ============================================================
    // TUTUP TIKET (admin/kepsek)
    // ============================================================
    public function tutup(Request $request, Tiket $tiket)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->hasRole('kepala_sekolah')) abort(403);

        $tiket->update([
            'status'    => 'selesai',
            'closed_by' => $user->id,
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Tiket berhasil ditutup.');
    }

    // ============================================================
    // BUKA TIKET TERKUNCI (admin/kepsek)
    // ============================================================
    public function buka(Request $request, Tiket $tiket)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->hasRole('kepala_sekolah')) abort(403);

        $request->validate(['alasan' => 'nullable|string|max:255']);

        $tiket->update([
            'status'        => 'diproses',
            'locked_at'     => null,
            'unlocked_by'   => $user->id,
            'unlocked_at'   => now(),
            'alasan_unlock' => $request->alasan,
            'last_response_at' => now(), // reset timer 7 hari
        ]);

        return back()->with('success', 'Tiket berhasil dibuka kembali.');
    }

    // ============================================================
    // UPDATE PRIORITAS (admin/kepsek)
    // ============================================================
    public function updatePrioritas(Request $request, Tiket $tiket)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->hasRole('kepala_sekolah')) abort(403);

        $request->validate(['prioritas' => 'required|in:rendah,sedang,tinggi']);
        $tiket->update(['prioritas' => $request->prioritas]);

        return back()->with('success', 'Prioritas diperbarui.');
    }

    // ============================================================
    // AUTO-LOCK (dipanggil setiap index load)
    // ============================================================
    private function autoLockTikets(): void
    {
        Tiket::whereIn('status', ['open', 'diproses'])
            ->where(function ($q) {
                $q->where('last_response_at', '<=', now()->subDays(7))
                  ->orWhere(function ($q2) {
                      $q2->whereNull('last_response_at')
                         ->where('created_at', '<=', now()->subDays(7));
                  });
            })
            ->update([
                'status'    => 'terkunci',
                'locked_at' => now(),
            ]);
    }

    // ============================================================
    // HELPER: Upload & Compress Foto
    // ============================================================
    private function uploadFoto($file, string $folder): array
    {
        $original = $file->getClientOriginalName();
        $filename = 'foto_' . uniqid() . '.jpg';

        try {
            $manager = new ImageManager(new Driver());
            $image   = $manager->read($file->getPathname());

            if ($image->width() > 1200) $image->scale(width: 1200);

            $encoded = $image->toJpeg(quality: 75);
            Storage::disk('public')->put($folder . '/' . $filename, $encoded);
            $path = $folder . '/' . $filename;

        } catch (\Exception $e) {
            $path = $file->storeAs($folder, $filename, 'public');
        }

        return ['path' => $path, 'original' => $original];
    }
}
