<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AlumniVerifikasiController extends Controller
{
    // Halaman form verifikasi (publik)
    public function index()
    {
        return view('public.alumni.verifikasi');
    }

    // Proses verifikasi
    public function verify(Request $request)
    {
        // Rate limiting — maks 10x per menit per IP
        $key = 'alumni-verify:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.")
                         ->withInput();
        }
        RateLimiter::hit($key, 60);

        $request->validate([
            'nisn'          => 'required|string|min:4|max:20',
            'tanggal_lahir' => 'required|date|before:today',
            'no_ijazah'     => 'required|string|min:3|max:50',
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.min'      => 'NISN minimal 4 karakter.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.before'   => 'Tanggal lahir tidak valid.',
            'no_ijazah.required'     => 'Nomor ijazah wajib diisi.',
        ]);

        // Cari alumni — match NISN + tanggal lahir + no ijazah
        $alumni = Alumni::where('nisn', trim($request->nisn))
            ->whereDate('tanggal_lahir', $request->tanggal_lahir)
            ->where(function ($q) use ($request) {
                // Toleransi spasi dan huruf besar/kecil di nomor ijazah
                $q->whereRaw('LOWER(REPLACE(no_ijazah, " ", "")) = ?', [
                    strtolower(str_replace(' ', '', $request->no_ijazah))
                ]);
            })
            ->first();

        if (!$alumni) {
            return back()->with('error', 'Data alumni tidak ditemukan. Periksa kembali NISN, tanggal lahir, dan nomor ijazah Anda.')
                         ->withInput();
        }

        // Data yang aman ditampilkan — tanpa NIK, alamat, HP, nama ortu
        $data = [
            'nama'          => $alumni->nama,
            'nisn'          => $alumni->nisn,
            'jenis_kelamin' => $alumni->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            'tempat_lahir'  => $alumni->tempat_lahir,
            'tanggal_lahir' => $alumni->tanggal_lahir,
            'nama_rombel'   => $alumni->nama_rombel,
            'tahun_lulus'   => $alumni->tahun_lulus,
            'tanggal_lulus' => $alumni->tanggal_lulus,
            'no_ijazah'     => $alumni->no_ijazah,
            'no_skhun'      => $alumni->no_skhun,
            'nilai_rata'    => $alumni->nilai_rata,
            'sekolah_asal'  => $alumni->sekolah_asal,
            'kurikulum'     => $alumni->kurikulum,
        ];

        return back()->with('alumni', $data)->withInput();
    }
}
