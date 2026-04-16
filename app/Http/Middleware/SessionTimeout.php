<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    // Timeout per role (menit)
    const TIMEOUT = [
        'admin'                => 120,
        'kepala_sekolah'       => 120,
        'wakil_kepala_sekolah' => 120,
        'guru'                 => 60,
        'bk'                   => 60,
        'tata_usaha'           => 60,
        'siswa'                => 30,
        'default'              => 60,
    ];

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) return $next($request);

        $lastActivity = session('last_activity');
        $role         = Auth::user()->getRoleNames()->first() ?? 'default';
        $timeout      = (self::TIMEOUT[$role] ?? self::TIMEOUT['default']) * 60;

        if ($lastActivity && (time() - $lastActivity) > $timeout) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'message'  => 'Session expired',
                    'redirect' => route('login'),
                ], 401);
            }

            return redirect()->route('login')
                ->with('error', 'Sesi Anda telah berakhir karena tidak aktif. Silakan login kembali.');
        }

        // Update last_activity — skip untuk AJAX agar tidak reset timer saat polling
        if (!$request->expectsJson()) {
            session(['last_activity' => time()]);
        }

        return $next($request);
    }
}
