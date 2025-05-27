<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nama' => 'pegawai',
            'email' => 'pegawai@example.com',
            'password' => Hash::make('11111111'),
            'role' => 'pegawai',
            'id_bidang' => null,
        ]);
    }
}
