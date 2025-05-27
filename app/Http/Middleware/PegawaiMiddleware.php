<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class PegawaiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah pengguna sudah login
        if (Auth::check()) {
            if (Auth::user()->role === 'pegawai') {
                // Hanya blokir halaman login jika sudah login sebagai admin
                if ($request->is('auth/login')) {
                    return redirect()->route('pegawai.dashboard');
                }
                return $next($request);
            }

            return redirect('/auth/login')->withErrors(['access' => 'You do not have access to this page.']);
        }



        return redirect('/auth/login')->withErrors(['access' => 'Please login first to access this page.']);
    }
}
