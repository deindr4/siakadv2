<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        // Generate CAPTCHA math soal baru setiap buka halaman login
        $a      = rand(1, 9);
        $b      = rand(1, 9);
        $answer = $a + $b;

        session(['captcha_answer' => $answer]);

        return view('auth.login', compact('a', 'b'));
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $ip  = $request->ip();
        $key = 'login_attempts_' . $ip;

        // Cek apakah IP sedang lockout
        if (Cache::has('login_lockout_' . $ip)) {
            $remaining = Cache::get('login_lockout_' . $ip) - now()->timestamp;
            $menit     = ceil($remaining / 60);
            return back()->withErrors([
                'login' => "Terlalu banyak percobaan gagal. Coba lagi dalam {$menit} menit.",
            ])->withInput($request->only('login'));
        }

        // Validasi CAPTCHA
        $captchaInput  = (int) $request->input('captcha');
        $captchaAnswer = (int) session('captcha_answer');

        if ($captchaInput !== $captchaAnswer) {
            // Hitung attempt
            $attempts = Cache::get($key, 0) + 1;
            Cache::put($key, $attempts, now()->addMinutes(15));

            // Lockout setelah 5 gagal
            if ($attempts >= 5) {
                Cache::put('login_lockout_' . $ip, now()->addMinutes(15)->timestamp, now()->addMinutes(15));
                Cache::forget($key);
                return back()->withErrors([
                    'login' => 'Terlalu banyak percobaan gagal. Akun dikunci selama 15 menit.',
                ])->withInput($request->only('login'));
            }

            $sisa = 5 - $attempts;
            return back()->withErrors([
                'captcha' => "Jawaban CAPTCHA salah. Sisa percobaan: {$sisa}.",
            ])->withInput($request->only('login'));
        }

        // CAPTCHA benar — proses login
        try {
            $request->authenticate();
        } catch (\Exception $e) {
            // Hitung attempt gagal login
            $attempts = Cache::get($key, 0) + 1;
            Cache::put($key, $attempts, now()->addMinutes(15));

            if ($attempts >= 5) {
                Cache::put('login_lockout_' . $ip, now()->addMinutes(15)->timestamp, now()->addMinutes(15));
                Cache::forget($key);
                return back()->withErrors([
                    'login' => 'Terlalu banyak percobaan gagal. Akun dikunci selama 15 menit.',
                ])->withInput($request->only('login'));
            }

            $sisa = 5 - $attempts;
            return back()->withErrors([
                'login' => "Username atau password salah. Sisa percobaan: {$sisa}.",
            ])->withInput($request->only('login'));
        }

        // Login berhasil — reset attempts
        Cache::forget($key);
        $request->session()->regenerate();

        $user = auth()->user();

        if ($user->hasRole('admin'))                   return redirect('/dashboard/admin');
        elseif ($user->hasRole('kepala_sekolah'))      return redirect('/dashboard/kepala-sekolah');
        elseif ($user->hasRole('wakil_kepala_sekolah'))return redirect('/dashboard/wakil-kepala');
        elseif ($user->hasRole('guru'))                return redirect('/dashboard/guru');
        elseif ($user->hasRole('bk'))                  return redirect('/dashboard/bk');
        elseif ($user->hasRole('tata_usaha'))          return redirect('/dashboard/tata-usaha');
        elseif ($user->hasRole('siswa'))               return redirect('/dashboard/siswa');

        return redirect('/dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
