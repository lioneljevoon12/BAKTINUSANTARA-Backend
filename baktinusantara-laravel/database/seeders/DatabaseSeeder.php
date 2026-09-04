<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProfilDesa;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $userDesa = User::create([
        'name' => 'Perangkat Desa Sukamaju',
        'email' => 'desa@test.com',
        'password' => bcrypt('password'),
        'role' => 'perangkat_desa',
        'is_verified' => true,
    ]);

    ProfilDesa::create([
        'user_id' => $userDesa->id,
        'nama_desa' => 'Desa Sukamaju',
        'kecamatan' => 'Contoh',
        'kabupaten' => 'Contoh',
        'provinsi' => 'Jawa Timur',
        'latitude' => -7.257472,
        'longitude' => 112.752090,
        'verified_at' => now(),
    ]);
    }
}
