<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogAktivitasHelper;
use App\Models\User;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return response()
            ->view('auth.login')
            ->header("Cache-Control", "no-store, no-cache, must-revalidate, max-age=0")
            ->header("Pragma", "no-cache")
            ->header("Expires", "Sat, 01 Jan 2000 00:00:00 GMT");
    }

    public function postLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            LogAktivitasHelper::log('login', "User {$user->name} berhasil login sebagai {$user->role}");
            $request->session()->regenerate();

            return $this->redirectByRole($user);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function loginWithToken($token)
    {
        $user = User::where('login_token', $token)
            ->where('login_token_expires_at', '>=', Carbon::now())
            ->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Token tidak valid atau sudah kedaluwarsa.');
        }

        Auth::login($user);

        // Hapus token setelah digunakan
        $user->update([
            'login_token' => null,
            'login_token_expires_at' => null,
        ]);

        LogAktivitasHelper::log('login', "User {$user->name} login via token");

        return $this->redirectByRole($user);
    }

    private function redirectByRole($user)
    {
        if ($user->role === 'admin') {
            return redirect()->intended('admin/dashboard');
        } elseif (in_array($user->role, ['kadis', 'sekretaris', 'kabid'])) {
            return redirect()->intended('pimpinan/dashboard');
        } elseif ($user->role === 'pegawai') {
            return redirect()->intended('pegawai/dashboard');
        } else {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Akses tidak valid.',
            ]);
        }
    }
}