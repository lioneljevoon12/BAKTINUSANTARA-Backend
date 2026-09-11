# 🇮🇩 BaktiNusantara — Backend RESTful API

**Platform Kolaborasi Berbasis Web untuk Menyelaraskan Kebutuhan Desa dengan Program Kerja KKN Mahasiswa Menuju Akselerasi SDGs Desa**

---

## 📚 Panduan Dokumentasi & Setup

Untuk memulai instalasi atau mengintegrasikan frontend dengan backend ini, silakan merujuk pada dokumen panduan berikut:

- 🛠️ **[SETUP.md](./SETUP.md)**: Panduan Instalasi Lokal, Konfigurasi Environment, Migrasi Database, Seeding Data, dan Eksekusi Automated Tests.
- 🚀 **[FRONTEND_INTEGRATION_GUIDE.md](./FRONTEND_INTEGRATION_GUIDE.md)**: Panduan Lengkap Kontrak API, Cheatsheet Akun Uji, dan 50 Endpoint API untuk Tim Frontend.

---

## 🌟 Fitur Utama Backend
- **Multi-Role RBAC & Token Auth (Sanctum):** Admin, Universitas, Dosen Pembimbing Lapangan (DPL), Perangkat Desa, dan Mahasiswa.
- **Aspirasi Warga & Kurasi Desa:** Saluran aspirasi publik masyarakat desa yang dapat dikurasi menjadi Pos Kebutuhan KKN resmi.
- **Direct Publish & Smart-Matching Engine:** Pos Kebutuhan multi-SDGs dengan pencocokan kompetensi jurusan mahasiswa (`matching_score`) dan kalkulasi radius geografis (`Haversine Formula`).
- **Progress Tracking Berkala & Data Lock:** Pelaporan progres mingguan bergambar yang immutable pasca-submit untuk integritas data pengabdian.
- **Luaran Akhir & E-Portofolio Publik:** Verifikasi berkas luaran oleh desa, penerbitan portofolio publik tanpa kebocoran data sensitif, dan penerbitan PDF E-Sertifikat asli.
- **Tata Kelola Universitas & Evaluasi DPL:** Verifikasi institusi, penugasan DPL se-almamater, dan pelaporan kinerja DPL dari pihak desa ke universitas.
- **Dashboard Metrik Dampak Nasional:** Agregasi real-time total desa terbantu, UMKM terdigitalisasi, jam pengabdian terakumulasi, dan sebaran pilar SDGs.
- **Pusat Notifikasi Multi-Role:** Notifikasi event-driven in-app otomatis pada seluruh transaksi antar-peran.
- **API Wilayah Indonesia (Proxy & Cache):** Data administratif berjenjang (Provinsi, Kabupaten, Kecamatan, Desa) dengan server-side caching 30 hari.

---

## ⚡ Quick Start
```bash
cd baktinusantara-laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Server backend akan berjalan di `http://127.0.0.1:8000/api`.

