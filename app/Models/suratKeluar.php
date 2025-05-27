<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class SuratKeluar extends Model
{
    use HasFactory;
    protected $fillable = [
        'nomor_surat', 'tujuan', 'perihal', 'tanggal_surat', 'status', 'pengirim', 'file_surat','jenis_surat'
    ];

    public function setNomorSuratAttribute($value)
    {
        $this->attributes['nomor_surat'] = Crypt::encryptString($value);
    }

    public function getTujuanAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function setTujuanAttribute($value)
    {
        $this->attributes['tujuan'] = Crypt::encryptString($value);
    }

    public function getNomorSuratAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function setPerihalAttribute($value)
    {
        $this->attributes['perihal'] = Crypt::encryptString($value);
    }

    public function getPerihalAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function setPengirimAttribute($value)
    {
        $this->attributes['pengirim'] = Crypt::encryptString($value);
    }

    public function getPengirimAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function setFileSuratAttribute($value)
    {
        $this->attributes['file_surat'] = Crypt::encryptString($value);
    }

    public function getFileSuratAttribute($value)
    {
        return Crypt::decryptString($value);
    }

}