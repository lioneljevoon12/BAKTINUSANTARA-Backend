<?php

namespace App\Services;

use App\Models\LaporanDosen;
use App\Models\User;

class LaporanDosenService
{
    public function storeByDesa(User $userDesa, array $data): LaporanDosen
    {
        $desa = $userDesa->profilDesa;
        if (!$desa) {
            abort(403, 'Profil desa tidak ditemukan.');
        }

        return LaporanDosen::create([
            'dosen_id' => $data['dosen_id'],
            'desa_id' => $desa->id,
            'proposal_id' => $data['proposal_id'] ?? null,
            'status' => 'menunggu',
            'isi' => $data['isi'],
        ])->load('dosen.user', 'desa', 'proposal');
    }
}
