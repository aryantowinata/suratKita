<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'nama' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('11111111'),
            'role' => 'admin',
            'id_bidang' => null,
        ]);
    }
}
