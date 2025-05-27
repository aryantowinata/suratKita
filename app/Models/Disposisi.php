<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Disposisi extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_surat',
        'id_pengirim',
        'id_penerima',
        'id_bidang',
        'jenis_surat',
        'instruksi',
    ];

    // Relasi ke Surat Masuk
    public function suratMasuk()
    {
        return $this->belongsTo(SuratMasuk::class, 'id_surat');
    }

    public function suratKeluar()
    {
        return $this->belongsTo(SuratKeluar::class, 'id_surat');
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'id_bidang');
    }

    public function instruksis()
    {
        return $this->belongsToMany(Instruksi::class, 'disposisi_instruksi', 'disposisi_id', 'instruksi_id');
    }
}