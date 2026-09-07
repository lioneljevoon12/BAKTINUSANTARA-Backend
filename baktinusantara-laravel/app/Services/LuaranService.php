<?php

namespace App\Services;

use App\Models\LuaranAkhir;
use App\Models\PortofolioPublik;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LuaranService
{
    protected function assertAnggotaKelompok(Proposal $proposal, User $user): void
    {
        $isMember = $proposal->kelompok->anggota()->where('user_id', $user->id)->exists()
            || $proposal->kelompok->ketua_id === $user->id;

        if (!$isMember) {
            abort(403, 'Anda bukan anggota dari kelompok pemilik proposal ini.');
        }
    }

    public function store(User $user, array $data, $file): LuaranAkhir
    {
        $proposal = Proposal::with('kelompok')->findOrFail($data['proposal_id']);

        $this->assertAnggotaKelompok($proposal, $user);

        if ($proposal->status !== 'diterima') {
            throw ValidationException::withMessages([
                'proposal' => 'Luaran akhir hanya dapat diunggah jika proposal berstatus diterima.',
            ]);
        }

        $existing = LuaranAkhir::where('proposal_id', $proposal->id)->first();
        if ($existing && $existing->status_verifikasi === 'verified') {
            throw ValidationException::withMessages([
                'luaran' => 'Luaran akhir untuk proposal ini sudah diverifikasi dan tidak dapat diubah.',
            ]);
        }

        $path = $file->store('luaran-deliverables', 'public');
        $fileUrl = Storage::url($path);

        if ($existing) {
            $existing->update([
                'file_deliverable_url' => $fileUrl,
                'deskripsi' => $data['deskripsi'],
                'status_verifikasi' => 'menunggu',
            ]);
            return $existing;
        }

        return LuaranAkhir::create([
            'proposal_id' => $proposal->id,
            'file_deliverable_url' => $fileUrl,
            'deskripsi' => $data['deskripsi'],
            'status_verifikasi' => 'menunggu',
        ]);
    }

    public function verifyByDesa(LuaranAkhir $luaran, User $user, array $data): PortofolioPublik
    {
        $luaran->load('proposal.posKebutuhan', 'proposal.kelompok');

        if (!$user->profilDesa || $luaran->proposal->posKebutuhan->desa_id !== $user->profilDesa->id) {
            abort(403, 'Anda tidak memiliki wewenang untuk memverifikasi luaran ini.');
        }

        $luaran->update([
            'status_verifikasi' => 'verified',
            'disahkan_oleh' => $user->id,
            'disahkan_at' => now(),
        ]);

        $baseSlug = Str::slug($luaran->proposal->posKebutuhan->judul ?? 'portofolio-kkn');
        $slug = $baseSlug . '-' . Str::lower(Str::random(6));

        while (PortofolioPublik::where('slug_public', $slug)->exists()) {
            $slug = $baseSlug . '-' . Str::lower(Str::random(6));
        }

        $portofolio = PortofolioPublik::updateOrCreate(
            ['luaran_id' => $luaran->id],
            [
                'slug_public' => $slug,
                'ringkasan_dampak' => $data['ringkasan_dampak'],
                'testimoni_desa' => $data['testimoni_desa'],
                'sertifikat_pdf_url' => 'certificates/' . $slug . '.pdf',
                'published_at' => now(),
            ]
        );

        return $portofolio->load('luaran.proposal.kelompok', 'luaran.proposal.posKebutuhan.desa');
    }

    public function listByDesa(User $user)
    {
        $desaId = $user->profilDesa?->id;
        if (!$desaId) {
            abort(403, 'Profil desa tidak ditemukan.');
        }

        return LuaranAkhir::whereHas('proposal.posKebutuhan', fn($q) => $q->where('desa_id', $desaId))
            ->with('proposal.kelompok', 'proposal.posKebutuhan', 'portofolio')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByProposal(Proposal $proposal, User $user): ?LuaranAkhir
    {
        $proposal->load('kelompok', 'posKebutuhan');

        $isMember = $proposal->kelompok->anggota()->where('user_id', $user->id)->exists()
            || $proposal->kelompok->ketua_id === $user->id;
        $isDesa = $user->profilDesa && $proposal->posKebutuhan->desa_id === $user->profilDesa->id;
        $isDosen = $user->profilDosen && $proposal->kelompok->dosen_id === $user->profilDosen->id;
        $isAdmin = $user->role === 'admin';

        if (!$isMember && !$isDesa && !$isDosen && !$isAdmin) {
            abort(403, 'Anda tidak memiliki wewenang untuk melihat luaran proposal ini.');
        }

        return $proposal->luaranAkhir()->with('portofolio', 'disahkanOleh')->first();
    }

    public function getPublicPortfolio(string $slug): PortofolioPublik
    {
        return PortofolioPublik::where('slug_public', $slug)
            ->with([
                'luaran.disahkanOleh.profilDesa',
                'luaran.proposal.posKebutuhan.desa',
                'luaran.proposal.kelompok.ketua',
                'luaran.proposal.kelompok.anggota.user.profilMahasiswa',
            ])
            ->firstOrFail();
    }
}
