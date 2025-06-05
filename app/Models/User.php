<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'id_bidang',
        'foto_profile',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'id_bidang');
    }

    // Mutator untuk mengenkripsi data sebelum disimpan ke database
    public function setNamaAttribute($value)
    {
        $this->attributes['nama'] = Crypt::encryptString($value);
    }



    // Accessor untuk mendekripsi data saat diambil dari database
    public function getNamaAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function suratMasuk()
    {
        return $this->belongsToMany(SuratMasuk::class, 'role_surat_masuk', 'user_id', 'surat_masuk_id');
    }

    public function bidangs()
    {
        return $this->belongsTo(Bidang::class, 'id_bidang');
    }
}