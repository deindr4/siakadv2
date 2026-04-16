<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    // ── FORM RESET ──
    public function showForm()
    {
        return view('auth.reset-password');
    }

    // ── STEP 1: Verifikasi username + tgl lahir + reset key ──
    public function verify(Request $request)
    {
        $request->validate([
            'username'      => 'required|string',
            'tanggal_lahir' => 'required|date',
            'reset_key'     => 'required|string',
        ], [
            'username.required'      => 'Username wajib diisi',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'reset_key.required'     => 'Kunci reset wajib diisi',
        ]);

        // Cek reset key dari config
        $validKey = config('siakad.reset_password_key');
        if ($request->reset_key !== $validKey) {
            return back()
                ->withInput($request->only('username','tanggal_lahir'))
                ->with('error', 'Kunci reset tidak valid. Hubungi administrator.');
        }

        // Cari user berdasarkan username
        $user = User::where('username', $request->username)
                    ->orWhere('email', $request->username)
                    ->first();

        if (!$user) {
            return back()
                ->withInput($request->only('username','tanggal_lahir'))
                ->with('error', 'Akun dengan username tersebut tidak ditemukan.');
        }

        // Cek tanggal lahir berdasarkan role
        $verified = false;
        $role = $user->getRoleNames()->first();

        if ($role === 'siswa') {
            $siswa = Siswa::where('user_id', $user->id)->first();
            if ($siswa && $siswa->tanggal_lahir) {
                $verified = \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('Y-m-d')
                            === \Carbon\Carbon::parse($request->tanggal_lahir)->format('Y-m-d');
            }
        } elseif (in_array($role, ['guru','admin','bk','tata_usaha','kepala_sekolah','wakil_kepala_sekolah'])) {
            $guru = Guru::where('user_id', $user->id)->first();
            if ($guru && $guru->tanggal_lahir) {
                $verified = \Carbon\Carbon::parse($guru->tanggal_lahir)->format('Y-m-d')
                            === \Carbon\Carbon::parse($request->tanggal_lahir)->format('Y-m-d');
            }
        }

        if (!$verified) {
            return back()
                ->withInput($request->only('username','tanggal_lahir'))
                ->with('error', 'Tanggal lahir tidak sesuai dengan data yang terdaftar.');
        }

        // Simpan token sementara di session
        $token = Str::random(40) . '|' . $user->id;
        session(['reset_token' => $token]);

        return back()->withInput($request->only('username'));
    }

    // ── STEP 2: Simpan password baru ──
    public function update(Request $request)
    {
        $request->validate([
            'reset_token'  => 'required',
            'password'     => 'required|min:8|confirmed',
        ], [
            'password.min'       => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Validasi token session
        $token = session('reset_token');
        if (!$token || $token !== $request->reset_token) {
            return redirect()->route('password.reset.form')
                ->with('error', 'Sesi verifikasi tidak valid atau sudah habis. Silakan ulangi.');
        }

        // Ambil user ID dari token
        $userId = explode('|', $token)[1] ?? null;
        $user   = $userId ? User::find($userId) : null;

        if (!$user) {
            return redirect()->route('password.reset.form')
                ->with('error', 'Akun tidak ditemukan. Silakan ulangi proses reset.');
        }

        // Update password
        $user->update([
            'password'              => Hash::make($request->password),
            'is_default_password'   => false,
            'default_password_hint' => null,
        ]);

        // Hapus token dari session
        session()->forget('reset_token');
        session(['reset_success' => true]);

        return redirect()->route('password.reset.form')
            ->with('reset_success', true);
    }
}
