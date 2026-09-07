<?php

namespace Tests\Feature;

use App\Models\Kelompok;
use App\Models\PosKebutuhan;
use App\Models\ProfilDesa;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProgressTest extends TestCase
{
    use RefreshDatabase;

    protected function setupScenario()
    {
        Storage::fake('public');
        Storage::fake('local');

        $userDesa = User::factory()->create(['role' => 'perangkat_desa', 'is_verified' => true]);
        $profilDesa = ProfilDesa::create([
            'user_id' => $userDesa->id,
            'nama_desa' => 'Desa Sukamaju',
            'kecamatan' => 'Kecamatan A',
            'kabupaten' => 'Kabupaten B',
            'provinsi' => 'Jawa Timur',
            'latitude' => -7.2575,
            'longitude' => 112.7521,
            'sk_file_url' => 'sk/dummy.pdf',
            'verified_at' => now(),
        ]);

        $pos = PosKebutuhan::create([
            'desa_id' => $profilDesa->id,
            'judul' => 'Pengembangan Web Desa',
            'deskripsi' => 'Deskripsi pos kebutuhan',
            'kategori' => 'infrastruktur',
            'sdg_codes' => [9],
            'kuota_kelompok' => 1,
            'deadline' => now()->addDays(30),
            'jurusan_dibutuhkan' => ['Informatika' => 3],
            'status' => 'open',
        ]);

        $ketua = User::factory()->create(['role' => 'mahasiswa', 'is_verified' => true]);
        $anggota = User::factory()->create(['role' => 'mahasiswa', 'is_verified' => true]);
        $mahasiswaLain = User::factory()->create(['role' => 'mahasiswa', 'is_verified' => true]);

        $kelompok = Kelompok::create([
            'nama_kelompok' => 'Kelompok Alpha',
            'ketua_id' => $ketua->id,
            'status' => 'aktif',
        ]);
        $kelompok->anggota()->create([
            'user_id' => $anggota->id,
            'role_in_group' => 'anggota',
        ]);

        $proposal = Proposal::create([
            'kelompok_id' => $kelompok->id,
            'pos_kebutuhan_id' => $pos->id,
            'draf_proker' => 'Program kerja mingguan',
            'file_proposal_url' => 'proposals/dummy.pdf',
            'status' => 'diterima',
            'submitted_at' => now(),
        ]);

        return compact('userDesa', 'profilDesa', 'pos', 'ketua', 'anggota', 'mahasiswaLain', 'kelompok', 'proposal');
    }

    public function test_anggota_can_store_progress_mingguan()
    {
        $data = $this->setupScenario();

        $response = $this->actingAs($data['anggota'], 'sanctum')->postJson('/api/progress', [
            'proposal_id' => $data['proposal']->id,
            'minggu_ke' => 1,
            'persentase' => 25,
            'deskripsi' => 'Observasi lapangan dan koordinasi dengan kepala dusun.',
            'foto' => UploadedFile::fake()->image('progress_m1.jpg'),
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.minggu_ke', 1)
            ->assertJsonPath('data.persentase', 25)
            ->assertJsonPath('data.is_locked', true);

        $this->assertDatabaseHas('progress_mingguan', [
            'proposal_id' => $data['proposal']->id,
            'minggu_ke' => 1,
            'persentase' => 25,
            'is_locked' => 1,
        ]);
    }

    public function test_cannot_submit_duplicate_minggu_ke()
    {
        $data = $this->setupScenario();

        $this->actingAs($data['ketua'], 'sanctum')->postJson('/api/progress', [
            'proposal_id' => $data['proposal']->id,
            'minggu_ke' => 1,
            'persentase' => 20,
            'deskripsi' => 'Laporan minggu 1 awal.',
        ])->assertStatus(201);

        $response = $this->actingAs($data['ketua'], 'sanctum')->postJson('/api/progress', [
            'proposal_id' => $data['proposal']->id,
            'minggu_ke' => 1,
            'persentase' => 25,
            'deskripsi' => 'Coba duplikasi laporan minggu 1.',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['minggu_ke']);
    }

    public function test_mahasiswa_lain_cannot_submit_progress()
    {
        $data = $this->setupScenario();

        $response = $this->actingAs($data['mahasiswaLain'], 'sanctum')->postJson('/api/progress', [
            'proposal_id' => $data['proposal']->id,
            'minggu_ke' => 1,
            'persentase' => 20,
            'deskripsi' => 'Laporan bukan kelompoknya.',
        ]);

        $response->assertStatus(403);
    }

    public function test_authorized_users_can_view_proposal_progress_timeline()
    {
        $data = $this->setupScenario();

        // Submit minggu 1 dan minggu 2
        $this->actingAs($data['ketua'], 'sanctum')->postJson('/api/progress', [
            'proposal_id' => $data['proposal']->id,
            'minggu_ke' => 1,
            'persentase' => 20,
            'deskripsi' => 'Minggu 1',
        ]);
        $this->actingAs($data['ketua'], 'sanctum')->postJson('/api/progress', [
            'proposal_id' => $data['proposal']->id,
            'minggu_ke' => 2,
            'persentase' => 45,
            'deskripsi' => 'Minggu 2',
        ]);

        // Desa mitra views timeline
        $response = $this->actingAs($data['userDesa'], 'sanctum')
            ->getJson("/api/proposal/{$data['proposal']->id}/progress");

        $response->assertStatus(200)
            ->assertJsonCount(2);

        // Mahasiswa lain (unauthorized) attempts to view
        $unauthResponse = $this->actingAs($data['mahasiswaLain'], 'sanctum')
            ->getJson("/api/proposal/{$data['proposal']->id}/progress");

        $unauthResponse->assertStatus(403);
    }

    public function test_only_ketua_can_upload_surat_izin_ortu()
    {
        $data = $this->setupScenario();

        // Anggota coba upload -> 403
        $anggotaAttempt = $this->actingAs($data['anggota'], 'sanctum')
            ->postJson("/api/proposal/{$data['proposal']->id}/surat-izin-ortu", [
                'file_surat' => UploadedFile::fake()->create('surat_izin.pdf', 500, 'application/pdf'),
            ]);
        $anggotaAttempt->assertStatus(403);

        // Ketua upload -> 200
        $ketuaAttempt = $this->actingAs($data['ketua'], 'sanctum')
            ->postJson("/api/proposal/{$data['proposal']->id}/surat-izin-ortu", [
                'file_surat' => UploadedFile::fake()->create('surat_izin.pdf', 500, 'application/pdf'),
            ]);

        $ketuaAttempt->assertStatus(200)
            ->assertJsonPath('message', 'Surat izin orang tua berhasil diunggah');

        $this->assertDatabaseHas('surat_izin_ortu', [
            'proposal_id' => $data['proposal']->id,
        ]);
    }
}
