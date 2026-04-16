<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect ke halaman login Google
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login')
                ->withErrors(['email' => 'Login Google gagal. Silakan coba lagi.']);
        }

        // Cari user berdasarkan email
        $user = User::where('email', $googleUser->getEmail())->first();

        // Jika email tidak terdaftar → tolak
        if (!$user) {
            return redirect('/login')->withErrors([
                'email' => 'Email ' . $googleUser->getEmail() . ' tidak terdaftar di sistem. Hubungi administrator.',
            ]);
        }

        // Jika akun dinonaktifkan (opsional, jika ada kolom is_active)
        // if (!$user->is_active) {
        //     return redirect('/login')->withErrors([
        //         'email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
        //     ]);
        // }

        // Update google_id jika belum tersimpan
        if (!$user->google_id) {
            $user->update(['google_id' => $googleUser->getId()]);
        }

        // Login user
        Auth::login($user, remember: true);

        // Cek default password (ikuti flow yang sudah ada)
        if ($user->is_default_password) {
            return redirect()->route('password.change')
                ->with('warning', 'Silakan ganti password default Anda.');
        }

        // Redirect sesuai role
        return redirect($this->redirectByRole($user));
    }

    /**
     * Redirect berdasarkan role
     */
    private function redirectByRole(User $user): string
    {
        return match (true) {
            $user->hasRole('admin')                   => '/dashboard/admin',
            $user->hasRole('wakil_kepala_sekolah')    => '/dashboard/wakil-kepala',
            $user->hasRole('kepala_sekolah')          => '/dashboard/kepala-sekolah',
            $user->hasRole('guru')                    => '/dashboard/guru',
            $user->hasRole('bk')                      => '/dashboard/bk',
            $user->hasRole('tata_usaha')              => '/dashboard/tata-usaha',
            $user->hasRole('siswa')                   => '/dashboard/siswa',
            default                                   => '/dashboard/admin',
        };
    }
}
