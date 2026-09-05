<?php

namespace App\Services;

use App\Models\User;
use App\Models\ProfilMahasiswa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MahasiswaService
{
    public function register(array $data, $ktmFile): ProfilMahasiswa
    {
        return DB::transaction(function () use ($data, $ktmFile) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone_wa' => $data['phone_wa'] ?? null,
                'role' => 'mahasiswa',
                'is_verified' => false,
            ]);

            $path = $ktmFile->store('ktm-mahasiswa', 'local');

            return ProfilMahasiswa::create([
                'user_id' => $user->id,
                'nim' => $data['nim'],
                'universitas' => $data['universitas'],
                'jurusan' => $data['jurusan'],
                'semester' => $data['semester'] ?? null,
                'ktm_file_url' => $path,
            ]);
        });
    }

    public function verify(ProfilMahasiswa $mhs): ProfilMahasiswa
    {
        $mhs->update(['verified_at' => now()]);
        $mhs->user()->update(['is_verified' => true]);
        return $mhs;
    }
}