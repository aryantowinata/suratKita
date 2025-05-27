<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArsipSurat extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'nomor_surat',
        'pengirim',
        'penerima',
        'perihal',
        'tanggal_surat',
        'jenis_surat',
        'file_surat',
        'status'
    ];
}
