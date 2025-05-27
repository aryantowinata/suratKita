<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KadisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nama' => 'kadis',
            'email' => 'kadis@example.com',
            'password' => Hash::make('11111111'),
            'role' => 'kadis',
            'id_bidang' => null,
        ]);
    }
}
