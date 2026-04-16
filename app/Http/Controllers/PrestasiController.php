<?php
// app/Http/Controllers/PrestasiController.php

namespace App\Http\Controllers;

use App\Models\KategoriPrestasi;
use App\Models\Prestasi;
use App\Models\PrestasiSiswa;
use App\Models\Rombel;
use App\Models\Semester;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PrestasiController extends Controller
{
    // ============================================================
    // INDEX - List semua prestasi
    // ============================================================
    public function index(Request $request)
    {
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('id')->get();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);
        $kategoris     = KategoriPrestasi::where('is_aktif', true)->orderBy('nama')->get();
        $user          = Auth::user();

        $query = Prestasi::with(['kategori', 'siswas', 'semester', 'dibuatOleh'])
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->when($request->filled('kategori_id'), fn($q) => $q->where('kategori_id', $request->kategori_id))
            ->when($request->filled('tingkat'), fn($q) => $q->where('tingkat', $request->tingkat))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('tipe'), fn($q) => $q->where('tipe', $request->tipe))
            ->when($request->filled('search'), fn($q) => $q->where('nama_lomba', 'like', '%'.$request->search.'%'));

        // Siswa hanya lihat prestasi diri sendiri
        if ($user->hasRole('siswa')) {
            $siswa = Siswa::where('user_id', $user->id)->first();
            if ($siswa) {
                $query->whereHas('siswas', fn($q) => $q->where('siswas.id', $siswa->id));
            }
        }

        $prestasi = $query->orderBy('tanggal', 'desc')->paginate(20)->withQueryString();
        $semester = Semester::find($semesterId);

        return view('prestasi.index', compact(
            'prestasi', 'kategoris', 'semesters', 'semesterAktif',
            'semesterId', 'semester'
        ));
    }

    // ============================================================
    // CREATE
    // ============================================================
    public function create(Request $request)
    {
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('id')->get();
        $kategoris     = KategoriPrestasi::where('is_aktif', true)->orderBy('jenis')->orderBy('nama')->get();
        $rombels       = Rombel::where('is_archived', false)
                            ->where('jenis_rombel', '1')
                            ->when($semesterAktif, fn($q) => $q->where('semester_id', $semesterAktif->id))
                            ->orderBy('tingkat')->orderBy('nama_rombel')->get();

        // Jika siswa input sendiri, pre-fill data dirinya
        $siswaSelf = null;
        if (Auth::user()->hasRole('siswa')) {
            $siswaSelf = Siswa::where('user_id', Auth::id())->first();
        }

        return view('prestasi.create', compact(
            'semesterAktif', 'semesters', 'kategoris', 'rombels', 'siswaSelf'
        ));
    }

    // ============================================================
    // STORE
    // ============================================================
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id'  => 'nullable|exists:kategori_prestasi,id',
            'semester_id'  => 'required|exists:semesters,id',
            'nama_lomba'   => 'required|string|max:255',
            'penyelenggara'=> 'nullable|string|max:255',
            'tingkat'      => 'required|in:sekolah,kecamatan,kabupaten,provinsi,nasional,internasional',
            'tanggal'      => 'required|date',
            'tempat'       => 'nullable|string|max:255',
            'juara'        => 'required|string|max:100',
            'juara_urut'   => 'nullable|integer|min:1',
            'tipe'         => 'required|in:individu,tim',
            'nama_tim'     => 'nullable|string|max:255',
            'siswa_ids'    => 'required|array|min:1',
            'siswa_ids.*'  => 'exists:siswas,id',
            'peran.*'      => 'nullable|string|max:100',
            'keterangan'   => 'nullable|string',
            'file_sertifikat' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'siswa_ids.required' => 'Pilih minimal 1 siswa',
            'nama_lomba.required'=> 'Nama lomba wajib diisi',
            'juara.required'     => 'Juara/peringkat wajib diisi',
        ]);

        DB::beginTransaction();
        try {
            $user   = Auth::user();
            $status = $user->hasRole('admin') || $user->hasRole('bk') || $user->hasRole('tata_usaha')
                    ? 'diverifikasi' : 'pending';

            // Upload & compress sertifikat
            $filePath = null;
            $fileOriginal = null;
            if ($request->hasFile('file_sertifikat')) {
                ['path' => $filePath, 'original' => $fileOriginal] = $this->uploadSertifikat($request->file('file_sertifikat'));
            }

            $prestasi = Prestasi::create([
                'kategori_id'              => $request->kategori_id,
                'semester_id'              => $request->semester_id,
                'nama_lomba'               => $request->nama_lomba,
                'penyelenggara'            => $request->penyelenggara,
                'tingkat'                  => $request->tingkat,
                'tanggal'                  => $request->tanggal,
                'tempat'                   => $request->tempat,
                'juara'                    => $request->juara,
                'juara_urut'               => $request->juara_urut ?? 99,
                'tipe'                     => $request->tipe,
                'nama_tim'                 => $request->tipe === 'tim' ? $request->nama_tim : null,
                'status'                   => $status,
                'diverifikasi_oleh'        => $status === 'diverifikasi' ? $user->id : null,
                'diverifikasi_pada'        => $status === 'diverifikasi' ? now() : null,
                'dibuat_oleh'              => $user->id,
                'role_pembuat'             => $user->getRoleNames()->first(),
                'file_sertifikat'          => $filePath,
                'file_sertifikat_original' => $fileOriginal,
                'keterangan'               => $request->keterangan,
            ]);

            // Simpan siswa
            foreach ($request->siswa_ids as $i => $siswaId) {
                PrestasiSiswa::create([
                    'prestasi_id' => $prestasi->id,
                    'siswa_id'    => $siswaId,
                    'peran'       => $request->peran[$i] ?? null,
                ]);
            }

            DB::commit();

            $msg = $status === 'diverifikasi'
                 ? 'Prestasi berhasil disimpan dan langsung terverifikasi!'
                 : 'Prestasi berhasil diajukan, menunggu verifikasi.';

            return redirect()->route('prestasi.index')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    // ============================================================
    // SHOW
    // ============================================================
    public function show(Prestasi $prestasi)
    {
        $prestasi->load(['kategori', 'siswas', 'semester', 'dibuatOleh', 'diverifikasiOleh', 'prestasiSiswa.siswa']);
        return view('prestasi.show', compact('prestasi'));
    }

    // ============================================================
    // EDIT
    // ============================================================
    public function edit(Prestasi $prestasi)
    {
        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('id')->get();
        $kategoris     = KategoriPrestasi::where('is_aktif', true)->orderBy('nama')->get();
        $rombels       = Rombel::where('is_archived', false)->where('jenis_rombel', '1')
                            ->orderBy('tingkat')->orderBy('nama_rombel')->get();
        $prestasi->load('prestasiSiswa.siswa');

        return view('prestasi.edit', compact('prestasi', 'semesterAktif', 'semesters', 'kategoris', 'rombels'));
    }

    // ============================================================
    // UPDATE
    // ============================================================
    public function update(Request $request, Prestasi $prestasi)
    {
        $request->validate([
            'kategori_id'  => 'nullable|exists:kategori_prestasi,id',
            'semester_id'  => 'required|exists:semesters,id',
            'nama_lomba'   => 'required|string|max:255',
            'penyelenggara'=> 'nullable|string|max:255',
            'tingkat'      => 'required|in:sekolah,kecamatan,kabupaten,provinsi,nasional,internasional',
            'tanggal'      => 'required|date',
            'tempat'       => 'nullable|string|max:255',
            'juara'        => 'required|string|max:100',
            'juara_urut'   => 'nullable|integer',
            'tipe'         => 'required|in:individu,tim',
            'nama_tim'     => 'nullable|string|max:255',
            'siswa_ids'    => 'required|array|min:1',
            'siswa_ids.*'  => 'exists:siswas,id',
            'keterangan'   => 'nullable|string',
            'file_sertifikat' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $filePath     = $prestasi->file_sertifikat;
            $fileOriginal = $prestasi->file_sertifikat_original;

            if ($request->hasFile('file_sertifikat')) {
                // Hapus file lama
                if ($filePath) Storage::disk('public')->delete($filePath);
                ['path' => $filePath, 'original' => $fileOriginal] = $this->uploadSertifikat($request->file('file_sertifikat'));
            }

            if ($request->boolean('hapus_sertifikat') && $filePath) {
                Storage::disk('public')->delete($filePath);
                $filePath = null;
                $fileOriginal = null;
            }

            $prestasi->update([
                'kategori_id'              => $request->kategori_id,
                'semester_id'              => $request->semester_id,
                'nama_lomba'               => $request->nama_lomba,
                'penyelenggara'            => $request->penyelenggara,
                'tingkat'                  => $request->tingkat,
                'tanggal'                  => $request->tanggal,
                'tempat'                   => $request->tempat,
                'juara'                    => $request->juara,
                'juara_urut'               => $request->juara_urut ?? 99,
                'tipe'                     => $request->tipe,
                'nama_tim'                 => $request->tipe === 'tim' ? $request->nama_tim : null,
                'file_sertifikat'          => $filePath,
                'file_sertifikat_original' => $fileOriginal,
                'keterangan'               => $request->keterangan,
            ]);

            // Sync siswa
            PrestasiSiswa::where('prestasi_id', $prestasi->id)->delete();
            foreach ($request->siswa_ids as $i => $siswaId) {
                PrestasiSiswa::create([
                    'prestasi_id' => $prestasi->id,
                    'siswa_id'    => $siswaId,
                    'peran'       => $request->peran[$i] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('prestasi.show', $prestasi)->with('success', 'Prestasi berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    // ============================================================
    // DESTROY
    // ============================================================
    public function destroy(Prestasi $prestasi)
    {
        if ($prestasi->file_sertifikat) {
            Storage::disk('public')->delete($prestasi->file_sertifikat);
        }
        $prestasi->delete();
        return redirect()->route('prestasi.index')->with('success', 'Data prestasi dihapus.');
    }

    // ============================================================
    // VERIFIKASI (admin/bk/tu)
    // ============================================================
    public function verifikasi(Request $request, Prestasi $prestasi)
    {
        $request->validate([
            'status'  => 'required|in:diverifikasi,ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        $prestasi->update([
            'status'               => $request->status,
            'diverifikasi_oleh'    => Auth::id(),
            'diverifikasi_pada'    => now(),
            'catatan_verifikasi'   => $request->catatan,
        ]);

        $msg = $request->status === 'diverifikasi' ? 'Prestasi berhasil diverifikasi!' : 'Prestasi ditolak.';
        return back()->with('success', $msg);
    }

    // ============================================================
    // GET SISWA BY ROMBEL (AJAX)
    // ============================================================
    public function getSiswaByRombel(Request $request)
    {
        $rombelId = $request->get('rombel_id');
        $rombel   = Rombel::find($rombelId);

        if (!$rombel) return response()->json([]);

        $siswas = Siswa::where('rombongan_belajar_id', $rombel->rombongan_belajar_id)
            ->where(function ($q) { $q->where('is_archived', false)->orWhereNull('is_archived'); })
            ->whereNotIn('status', ['mutasi', 'keluar'])
            ->orderBy('nama')
            ->get(['id', 'nama', 'nisn']);

        return response()->json($siswas);
    }

    // ============================================================
    // KATEGORI MANAGEMENT (admin)
    // ============================================================
    public function kategoriIndex()
    {
        $kategoris = KategoriPrestasi::withCount('prestasi')->orderBy('jenis')->orderBy('nama')->get();
        return view('prestasi.kategori', compact('kategoris'));
    }

    public function kategoriStore(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:100|unique:kategori_prestasi,nama',
            'jenis' => 'required|in:akademik,non_akademik',
            'warna' => 'required|string|max:7',
        ]);
        KategoriPrestasi::create($request->only('nama', 'jenis', 'warna') + ['is_aktif' => true]);
        return back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function kategoriUpdate(Request $request, KategoriPrestasi $kategori)
    {
        $request->validate([
            'nama'  => 'required|string|max:100|unique:kategori_prestasi,nama,' . $kategori->id,
            'jenis' => 'required|in:akademik,non_akademik',
            'warna' => 'required|string|max:7',
        ]);
        $kategori->update($request->only('nama', 'jenis', 'warna'));
        return back()->with('success', 'Kategori diperbarui!');
    }

    public function kategoriDestroy(KategoriPrestasi $kategori)
    {
        if ($kategori->prestasi()->count() > 0) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih digunakan.');
        }
        $kategori->delete();
        return back()->with('success', 'Kategori dihapus.');
    }

    // ============================================================
    // HELPER: Upload & Compress Sertifikat
    // ============================================================
    private function uploadSertifikat($file): array
    {
        $original  = $file->getClientOriginalName();
        $ext       = strtolower($file->getClientOriginalExtension());
        $filename  = 'sertifikat_' . uniqid() . '.' . ($ext === 'pdf' ? 'pdf' : 'jpg');
        $directory = 'prestasi/sertifikat';

        if ($ext === 'pdf') {
            // PDF: simpan langsung tanpa compress
            $path = $file->storeAs($directory, $filename, 'public');
        } else {
            // Gambar: compress dengan Intervention Image
            try {
                $manager = new ImageManager(new Driver());
                $image   = $manager->read($file->getPathname());

                // Resize jika terlalu besar (max 1200px lebar)
                if ($image->width() > 1200) {
                    $image->scale(width: 1200);
                }

                // Encode ke JPEG quality 75%
                $encoded = $image->toJpeg(quality: 75);

                Storage::disk('public')->put($directory . '/' . $filename, $encoded);
                $path = $directory . '/' . $filename;

            } catch (\Exception $e) {
                // Fallback: simpan asli jika Intervention gagal
                $path = $file->storeAs($directory, $filename, 'public');
            }
        }

        return ['path' => $path, 'original' => $original];
    }
}
