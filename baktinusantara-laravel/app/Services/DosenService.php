<?php

namespace App\Services;

use App\Models\Kelompok;
use App\Models\ProfilDosen;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class DosenService
{
    public function listKelompokBinaan(User $userDosen)
    {
        $dosen = $userDosen->profilDosen;
        if (!$dosen) {
            abort(403, 'Profil dosen tidak ditemukan.');
        }

        return Kelompok::where('dosen_id', $dosen->id)
            ->with([
                'ketua',
                'anggota.user.profilMahasiswa',
                'proposal.posKebutuhan.desa',
                'proposal.progressMingguan',
                'proposal.luaranAkhir',
            ])
            ->get();
    }

    public function validasiKelayakanProposal(Proposal $proposal, User $userDosen, array $data): Proposal
    {
        $proposal->load('kelompok');

        $dosen = $userDosen->profilDosen;
        if (!$dosen || $proposal->kelompok->dosen_id !== $dosen->id) {
            abort(403, 'Anda bukan dosen pembimbing dari kelompok pemilik proposal ini.');
        }

        // Simpan ke kolom terstruktur khusus Dosen DPL
        $proposal->update([
            'status_kelayakan_dosen' => $data['status_kelayakan'],
            'catatan_dosen' => $data['catatan_dosen'],
            'dosen_reviewed_at' => now(),
        ]);

        return $proposal->load('kelompok', 'posKebutuhan');
    }

    public function listDosenPublic()
    {
        return ProfilDosen::with(['user:id,name,email', 'universitas:id,nama_universitas,kode_univ'])
            ->get();
    }

    public function setDosenPembimbing(Kelompok $kelompok, User $userKetua, int $dosenId): Kelompok
    {
        if ($kelompok->ketua_id !== $userKetua->id) {
            abort(403, 'Hanya ketua kelompok yang memiliki wewenang menetapkan dosen pembimbing.');
        }

        $dosen = ProfilDosen::with('universitas')->findOrFail($dosenId);

        // Validasi kesesuaian asal universitas mahasiswa dengan dosen
        $profilMahasiswa = $userKetua->profilMahasiswa;
        if ($profilMahasiswa && $dosen->universitas) {
            $mhsUniv = strtolower(trim($profilMahasiswa->universitas));
            $dosenUnivNama = strtolower(trim($dosen->universitas->nama_universitas));
            $dosenUnivKode = strtolower(trim($dosen->universitas->kode_univ ?? ''));

            $isMatch = ($mhsUniv === $dosenUnivNama)
                || ($dosenUnivKode && $mhsUniv === $dosenUnivKode)
                || str_contains($dosenUnivNama, $mhsUniv)
                || str_contains($mhsUniv, $dosenUnivNama);

            if (!$isMatch) {
                throw ValidationException::withMessages([
                    'dosen_id' => 'Dosen pembimbing harus berasal dari perguruan tinggi yang sama dengan mahasiswa (' . $dosen->universitas->nama_universitas . ').',
                ]);
            }
        }

        $kelompok->update([
            'dosen_id' => $dosen->id,
        ]);

        return $kelompok->load('dosen.user', 'dosen.universitas');
    }
}
