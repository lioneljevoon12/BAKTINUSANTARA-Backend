<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilMahasiswa extends Model
{
    protected $table = 'profil_mahasiswa';
    protected $fillable = ['user_id', 'nim', 'universitas', 'jurusan', 'semester', 'ktm_file_url', 'verified_at'];

    public function user() { return $this->belongsTo(User::class); }
}