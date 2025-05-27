<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SekreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nama' => 'sekretaris',
            'email' => 'sekretaris@example.com',
            'password' => Hash::make('11111111'),
            'role' => 'sekretaris',
            'id_bidang' => null,
        ]);
    }
}
