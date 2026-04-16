<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckDefaultPassword
{
    public function handle(Request $request, Closure $next)
    {
        if (
            auth()->check() &&
            auth()->user()->is_default_password
        ) {
            $allowed = [
                'password.change',
                'password.change.update',
                'logout',
            ];

            if (!$request->routeIs(...$allowed)) {
                return redirect()->route('password.change')
                    ->with('warning', 'Anda menggunakan password default. Silakan ganti password terlebih dahulu!');
            }
        }

        return $next($request);
    }
}
