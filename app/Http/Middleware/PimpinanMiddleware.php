<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PimpinanMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah pengguna sudah login
        if (!Auth::check()) {
            return redirect('/auth/login')->withErrors(['access' => 'Silakan login terlebih dahulu.']);
        }

        // Role yang diizinkan
        $allowedRoles = ['kadis', 'sekretaris', 'kabid'];

        if (in_array(Auth::user()->role, $allowedRoles)) {
            // Cegah pengguna yang sudah login mengakses halaman login lagi
            if ($request->is('auth/login')) {
                return redirect()->route('pimpinan.dashboard');
            }

            return $next($request);
        }

        // Redirect ke halaman lain jika role tidak sesuai
        return redirect('/')->withErrors(['access' => 'Anda tidak memiliki izin untuk mengakses halaman ini.']);
    }
}