<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Redirect berdasarkan peran pengguna
            $role = Auth::user()->role;

            switch ($role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'kadis':
                    return redirect()->route('pimpinan.dashboard');
                case 'sekretaris':
                    return redirect()->route('pimpinan.dashboard');
                case 'kabid':
                    return redirect()->route('pimpinan.dashboard');
                case 'pegawai': // Jika ada role user biasa
                    return redirect()->route('pegawai.dashboard');
            }
        }

        return $next($request);
    }
}