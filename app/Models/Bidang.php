<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    use HasFactory;

    protected $fillable = ['nama_bidang'];

    public function users()
    {
        return $this->hasMany(User::class, 'id_bidang');
    }

    public function disposisis()
    {
        return $this->belongsToMany(Disposisi::class, 'bidang_disposisi');
    }
}
