<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\PelanggaranSiswa;
use App\Models\PoinPositifSiswa;
use App\Models\Siswa;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;

class PelanggaranSiswaController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->firstOrFail();
        $sem   = Semester::aktif();

        $semesters  = Semester::orderByDesc('semester_id')->get();
        $semesterId = request('semester_id', $sem?->id);

        $pelanggaran = PelanggaranSiswa::with(['jenisPelanggaran', 'dicatatOleh'])
            ->where('siswa_id', $siswa->id)
            ->where('status', '!=', 'dibatalkan')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->orderByDesc('tanggal')
            ->paginate(15)->withQueryString();

        // Poin pelanggaran
        $totalPoin = PelanggaranSiswa::where('siswa_id', $siswa->id)
            ->where('status', '!=', 'dibatalkan')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->sum('poin');

        $totalKasus = PelanggaranSiswa::where('siswa_id', $siswa->id)
            ->where('status', '!=', 'dibatalkan')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->count();

        $kasusSelesai = PelanggaranSiswa::where('siswa_id', $siswa->id)
            ->where('status', 'selesai')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->count();

        // Poin kebaikan
        $totalPoinPositif = PoinPositifSiswa::where('siswa_id', $siswa->id)
            ->where('status', 'aktif')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->sum('poin');

        $riwayatPositif = PoinPositifSiswa::with(['jenisKegiatan'])
            ->where('siswa_id', $siswa->id)
            ->where('status', 'aktif')
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->orderByDesc('tanggal')
            ->get();

        // Net poin (tidak bisa minus)
        $netPoin = max(0, $totalPoin - $totalPoinPositif);

        // Indikator berdasarkan NET poin
        $warningLevel = match(true) {
            $netPoin >= 75 => 'danger',
            $netPoin >= 50 => 'warning',
            $netPoin >= 25 => 'info',
            default        => 'success',
        };

        return view('siswa.pelanggaran.index', compact(
            'siswa', 'pelanggaran', 'semesters',
            'semesterId', 'sem',
            'totalPoin', 'totalKasus', 'kasusSelesai',
            'totalPoinPositif', 'riwayatPositif',
            'netPoin', 'warningLevel'
        ));
    }
}
