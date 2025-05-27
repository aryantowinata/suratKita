<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instruksi extends Model
{
    protected $fillable = ['nama_instruksi'];

    public function disposisis()
    {
        return $this->belongsToMany(Disposisi::class, 'disposisi_instruksi', 'instruksi_id', 'disposisi_id');
    }
}