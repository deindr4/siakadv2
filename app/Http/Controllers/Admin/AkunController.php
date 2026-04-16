<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AkunController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'siswa');

        $totalSiswaAkunAktif = User::role('siswa')->count();
        $totalGuruAkunAktif  = User::role('guru')->count();
        $totalSiswaBelumAkun = Siswa::where('is_archived', false)
            ->where('status_mutasi', 'aktif')
            ->whereNull('user_id')->count();
        $totalGuruBelumAkun  = Guru::where('is_archived', false)
            ->whereNull('user_id')->count();

        $semesterAktif = Semester::aktif();
        $semesters     = Semester::orderByDesc('semester_id')->get();
        $rombels       = Rombel::where('is_archived', false)
            ->where('jenis_rombel', '1')
            ->when($semesterAktif, fn($q) => $q->where('semester_id', $semesterAktif->id))
            ->orderBy('tingkat')->orderBy('nama_rombel')->get();

        $siswas = collect();
        $gurus  = collect();

        if ($tab === 'siswa') {
            $semesterId = $request->get('semester_id', $semesterAktif?->id);
            $query = Siswa::where('is_archived', false)->where('status_mutasi', 'aktif');

            if ($semesterId) $query->where('semester_id', $semesterId);
            if ($request->filled('rombel')) $query->where('rombongan_belajar_id', $request->rombel);
            if ($request->filled('status_akun')) {
                if ($request->status_akun === 'sudah') $query->whereNotNull('user_id');
                else $query->whereNull('user_id');
            }
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('nama', 'like', '%'.$request->search.'%')
                      ->orWhere('nisn', 'like', '%'.$request->search.'%');
                });
            }
            $siswas = $query->orderBy('nama')->paginate(20)->withQueryString();
        }

        if ($tab === 'guru') {
            $query = Guru::where('is_archived', false);

            if ($request->filled('status_akun')) {
                if ($request->status_akun === 'sudah') $query->whereNotNull('user_id');
                else $query->whereNull('user_id');
            }
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('nama', 'like', '%'.$request->search.'%')
                      ->orWhere('nip', 'like', '%'.$request->search.'%')
                      ->orWhere('nuptk', 'like', '%'.$request->search.'%');
                });
            }
            $gurus = $query->orderBy('nama')->paginate(20)->withQueryString();
        }

        return view('admin.akun.index', compact(
            'tab', 'siswas', 'gurus', 'rombels', 'semesters',
            'semesterAktif', 'totalSiswaAkunAktif', 'totalGuruAkunAktif',
            'totalSiswaBelumAkun', 'totalGuruBelumAkun'
        ));
    }

    public function generateSiswaMassal(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|exists:semesters,id',
            'rombel_ids'  => 'required|array|min:1',
        ]);

        $rombels          = Rombel::whereIn('id', $request->rombel_ids)->get();
        $rombelBelajarIds = $rombels->pluck('rombongan_belajar_id');

        $siswas = Siswa::where('is_archived', false)
            ->where('status_mutasi', 'aktif')
            ->where('semester_id', $request->semester_id)
            ->whereIn('rombongan_belajar_id', $rombelBelajarIds)
            ->whereNull('user_id')
            ->get();

        $role    = Role::firstOrCreate(['name' => 'siswa']);
        $created = 0;
        $skipped = 0;

        foreach ($siswas as $siswa) {
            if (!$siswa->nisn) { $skipped++; continue; }

            if (User::where('username', $siswa->nisn)->exists()) {
                $user = User::where('username', $siswa->nisn)->first();
                $siswa->update(['user_id' => $user->id]);
                $skipped++;
                continue;
            }

            $user = User::create([
                'name'                  => $siswa->nama,
                'username'              => $siswa->nisn,
                'email'                 => $siswa->email ?? $siswa->nisn.'@siswa.sch.id',
                'password'              => Hash::make('12345678'),
                'is_default_password'   => true,
                'default_password_hint' => '12345678',
            ]);
            $user->assignRole($role);
            $siswa->update(['user_id' => $user->id]);
            $created++;
        }

        return back()->with('success', "$created akun siswa berhasil dibuat, $skipped dilewati.");
    }

    public function generateSiswaSingle(Request $request)
    {
        $request->validate(['siswa_id' => 'required|exists:siswas,id']);
        $siswa = Siswa::findOrFail($request->siswa_id);

        if (!$siswa->nisn) return back()->with('error', 'Siswa '.$siswa->nama.' tidak memiliki NISN!');
        if ($siswa->user_id) return back()->with('error', 'Siswa '.$siswa->nama.' sudah memiliki akun!');
        if (User::where('username', $siswa->nisn)->exists()) return back()->with('error', 'Username '.$siswa->nisn.' sudah digunakan!');

        $role = Role::firstOrCreate(['name' => 'siswa']);
        $user = User::create([
            'name'                  => $siswa->nama,
            'username'              => $siswa->nisn,
            'email'                 => $siswa->email ?? $siswa->nisn.'@siswa.sch.id',
            'password'              => Hash::make('12345678'),
            'is_default_password'   => true,
            'default_password_hint' => '12345678',
        ]);
        $user->assignRole($role);
        $siswa->update(['user_id' => $user->id]);

        return back()->with('success', 'Akun '.$siswa->nama.' berhasil dibuat! Username: '.$siswa->nisn);
    }

    public function generateGuruMassal(Request $request)
    {
        $gurus   = Guru::where('is_archived', false)->whereNull('user_id')->get();
        $role    = Role::firstOrCreate(['name' => 'guru']);
        $created = 0;
        $skipped = 0;

        foreach ($gurus as $guru) {
            $username = $guru->nik ?? $guru->nip;
            if (!$username) { $skipped++; continue; }

            if (User::where('username', $username)->exists()) {
                $user = User::where('username', $username)->first();
                $guru->update(['user_id' => $user->id]);
                $skipped++;
                continue;
            }

            $user = User::create([
                'name'                  => $guru->nama,
                'username'              => $username,
                'email'                 => $guru->email ?? $username.'@guru.sch.id',
                'password'              => Hash::make('guruku@1234'),
                'is_default_password'   => true,
                'default_password_hint' => 'guruku@1234',
            ]);
            $user->assignRole($role);
            $guru->update(['user_id' => $user->id]);
            $created++;
        }

        return back()->with('success', "$created akun guru berhasil dibuat, $skipped dilewati.");
    }

    public function generateGuruSingle(Request $request)
    {
        $request->validate(['guru_id' => 'required|exists:gurus,id']);
        $guru     = Guru::findOrFail($request->guru_id);
        $username = $guru->nik ?? $guru->nip;

        if (!$username) return back()->with('error', 'Guru '.$guru->nama.' tidak memiliki NIK/NIP!');
        if ($guru->user_id) return back()->with('error', 'Guru '.$guru->nama.' sudah memiliki akun!');
        if (User::where('username', $username)->exists()) return back()->with('error', 'Username '.$username.' sudah digunakan!');

        $role = Role::firstOrCreate(['name' => 'guru']);
        $user = User::create([
            'name'                  => $guru->nama,
            'username'              => $username,
            'email'                 => $guru->email ?? $username.'@guru.sch.id',
            'password'              => Hash::make('guruku@1234'),
            'is_default_password'   => true,
            'default_password_hint' => 'guruku@1234',
        ]);
        $user->assignRole($role);
        $guru->update(['user_id' => $user->id]);

        return back()->with('success', 'Akun '.$guru->nama.' berhasil dibuat! Username: '.$username);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tipe'    => 'required|in:siswa,guru',
        ]);

        $user            = User::findOrFail($request->user_id);
        $defaultPassword = $request->tipe === 'siswa' ? '12345678' : 'guruku@1234';

        $user->update([
            'password'              => Hash::make($defaultPassword),
            'is_default_password'   => true,
            'default_password_hint' => $defaultPassword,
        ]);

        return back()->with('success', 'Password '.$user->name.' berhasil direset ke default!');
    }
}
