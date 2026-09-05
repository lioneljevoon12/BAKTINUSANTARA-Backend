<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ProfilDesa;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $userDesa = User::firstOrCreate(
            ['email' => 'desa@test.com'],
            [
                'name' => 'Perangkat Desa Sukamaju',
                'password' => bcrypt('password'),
                'role' => 'perangkat_desa',
                'is_verified' => true,
            ]
        );

        ProfilDesa::firstOrCreate(
            ['user_id' => $userDesa->id],
            [
                'nama_desa' => 'Desa Sukamaju',
                'kecamatan' => 'Contoh',
                'kabupaten' => 'Contoh',
                'provinsi' => 'Jawa Timur',
                'latitude' => -7.257472,
                'longitude' => 112.752090,
                'verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin Platform',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_verified' => true,
            ]
        );
    }
}