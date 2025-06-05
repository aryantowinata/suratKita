<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class SuratMasuk extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_surat',
        'no_agenda',
        'pengirim',
        'perihal',
        'tanggal_surat',
        'status',
        'file_surat',
        'id_role',
        'jenis_surat'
    ];

    // === Enkripsi Nomor Surat ===
    public function setNomorSuratAttribute($value)
    {
        $this->attributes['nomor_surat'] = Crypt::encryptString($value);
    }

    public function getNomorSuratAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    // === Enkripsi Pengirim ===
    public function setPengirimAttribute($value)
    {
        $this->attributes['pengirim'] = Crypt::encryptString($value);
    }

    public function getPengirimAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    // === Enkripsi Perihal ===
    public function setPerihalAttribute($value)
    {
        $this->attributes['perihal'] = Crypt::encryptString($value);
    }

    public function getPerihalAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    // === Enkripsi File Surat (jika berupa path) ===
    public function setFileSuratAttribute($value)
    {
        $this->attributes['file_surat'] = Crypt::encryptString($value);
    }

    public function getFileSuratAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function roles()
    {
        return $this->belongsToMany(User::class, 'surat_masuk_role', 'surat_masuk_id', 'user_id');
    }
}