<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KabidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nama' => 'kabid',
            'email' => 'kabid@example.com',
            'password' => Hash::make('11111111'),
            'role' => 'kabid',
            'id_bidang' => null,
        ]);
    }
}
