<?php

namespace App\Helpers;

use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class LogAktivitasHelper
{
    public static function log($aksi, $deskripsi = null)
    {
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aksi' => $aksi,
            'deskripsi' => $deskripsi,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent')
        ]);
    }
}
