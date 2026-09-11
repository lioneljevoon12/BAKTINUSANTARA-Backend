# 🛠️ BaktiNusantara Backend — Complete Setup & Installation Guide

Panduan resmi instalasi, konfigurasi environment, migrasi database, seeding data uji, dan pengujian otomatis untuk Backend **BaktiNusantara** (Laravel 12.x / PHP 8.2+ / MySQL / Sanctum).

---

## 📋 1. Prasyarat Sistem (System Requirements)

Sebelum memulai instalasi, pastikan lingkungan lokal Anda memenuhi spesifikasi berikut:

| Perangkat Lunak | Versi Minimal | Keterangan |
|---|---|---|
| **PHP** | `^8.2` atau `^8.3` | Ekstensi wajib: `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `curl`, `fileinfo`, `gd`, `zip`, `tokenizer`, `xml` |
| **Composer** | `^2.5.0` | Dependency Manager PHP |
| **MySQL / MariaDB** | MySQL `^8.0` / MariaDB `^10.4` | Database Relasional Utama |
| **Git** | `^2.30.0` | Version Control System |

Cek versi PHP dan Composer Anda di terminal:
```bash
php -v
composer -V
```

---

## 🚀 2. Langkah-Langkah Instalasi (Step-by-Step Setup)

### Langkah 1: Clone Repository
Clone project dari Git repository ke direktori lokal Anda:
```bash
git clone https://github.com/lioneljevoon12/BAKTINUSANTARA-Backend.git
cd BAKTINUSANTARA-Backend/baktinusantara-laravel
```

---

### Langkah 2: Install Dependensi PHP via Composer
Jalankan Composer untuk mengunduh seluruh vendor package (Laravel Framework, Sanctum, Dompdf, dll):
```bash
composer install
```

---

### Langkah 3: Konfigurasi File Environment (`.env`)
Salin file `.env.example` menjadi `.env`:

**Windows (PowerShell / CMD):**
```powershell
copy .env.example .env
```

**Linux / macOS:**
```bash
cp .env.example .env
```

Buka file `.env` yang baru dibuat dan sesuaikan konfigurasi koneksi database MySQL Anda:
```env
APP_NAME=BaktiNusantara
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=baktinusantaradb
DB_USERNAME=root
DB_PASSWORD=
```
> 💡 *Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan kredensial MySQL lokal Anda (misal XAMPP/Laragon default user `root` tanpa password).*

---

### Langkah 4: Buat Database MySQL
Pastikan MySQL service sudah berjalan, lalu buat database dengan nama `baktinusantaradb`:

**Melalui MySQL CLI:**
```sql
CREATE DATABASE baktinusantaradb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
*Atau buat database `baktinusantaradb` melalui phpMyAdmin / TablePlus / DBeaver.*

---

### Langkah 5: Generate Application Encryption Key
Generate unique application key untuk enkripsi sesi dan token Sanctum:
```bash
php artisan key:generate
```

---

### Langkah 6: Jalankan Migrasi Database & Seeder Lengkap
Eksekusi migrasi skema tabel beserta dataset simulasi lengkap multi-role:
```bash
php artisan migrate:fresh --seed
```

Perintah di atas akan secara otomatis menyiapkan:
- 21 skema tabel relasional platform.
- 19 akun pengguna siap pakai (Admin, Universitas, Dosen DPL, Desa, Mahasiswa).
- Aspirasi warga, Pos Kebutuhan multi-SDGs, Kelompok KKN, Proposal, Progress berkala, Luaran akhir terverifikasi, E-Portofolio publik, PDF Sertifikat asli, Laporan DPL, dan Notifikasi real-time.

---

### Langkah 7: Hubungkan Storage Publik (Storage Symlink)
Buat symlink dari folder `storage/app/public` ke `public/storage` agar file publik (E-Sertifikat PDF, foto progress, berkas portofolio) dapat diakses via URL browser:
```bash
php artisan storage:link
```

---

### Langkah 8: Jalankan Server Development
Nyalakan local development server Laravel:
```bash
php artisan serve
```

Server backend Anda sekarang aktif dan siap menerima request di:
```
http://127.0.0.1:8000
API Base URL: http://127.0.0.1:8000/api
```

---

## 🔑 3. Kredensial Akun Default (Siap Uji / Demo)

Semua akun hasil seeding menggunakan password default: **`password`**

| Role | Email | Password | Nama Akun | Skenario Pengujian |
|---|---|---|---|---|
| **Admin** | `admin@baktinusantara.id` | `password` | Super Admin BaktiNusantara | Verifikasi institusi desa, universitas, & mahasiswa pending. |
| **Universitas** | `unesa@unesa.ac.id` | `password` | LPM UNESA | Tambah Dosen DPL, monitor kelompok binaan, tinjau laporan evaluasi. |
| **Universitas** | `its@its.ac.id` | `password` | DRPM ITS | Kelola Dosen & kelompok KKN ITS. |
| **Dosen DPL** | `dosen.budi@unesa.ac.id` | `password` | Dr. Budi Santoso, M.Kom. | Tinjau kelayakan proposal, pantau timeline progress mingguan. |
| **Perangkat Desa** | `desa.sukamaju@desa.id` | `password` | Pemdes Sukamaju (Jombang) | Kurasi aspirasi, approve proposal, verifikasi luaran akhir. |
| **Perangkat Desa** | `desa.berkahmakmur@desa.id` | `password` | Pemdes Berkah Makmur (Pasuruan) | Kelola pos lingkungan/biogas, kirim evaluasi DPL ke kampus. |
| **Mahasiswa (Ketua 1)** | `ketua.ahmad@mhs.unesa.ac.id` | `password` | Ahmad Fauzi (KKN UNESA 01) | Siklus penuh: Proposal diterima, progress 100%, e-sertifikat terbit. |
| **Mahasiswa (Ketua 2)** | `ketua.dimas@mhs.its.ac.id` | `password` | Dimas Pratama (KKN ITS) | Skenario jarak jauh (> 1.000 km) & upload Surat Izin Orang Tua. |
| **Mahasiswa (Ketua 3)** | `ketua.bayu@mhs.unesa.ac.id` | `password` | Bayu Setiawan (KKN UNESA 02) | Skenario proposal baru berstatus menunggu review. |
| **Mahasiswa (Solo)** | `mhs.solo@mhs.unesa.ac.id` | `password` | Dewi Lestari | Mahasiswa aktif belum berkelompok (uji buat/gabung kelompok). |

---

## 🧪 4. Menjalankan Pengujian Otomatis (Automated Tests)

Backend BaktiNusantara dilengkapi dengan pengujian menyeluruh (PHPUnit / Feature Tests) mencakup seluruh modul transaksi, RBAC, validasi geografis Haversine, locking progress mingguan, agregasi dashboard, hingga API wilayah:

### Menjalankan Seluruh Test Suite
```bash
php artisan test
```
*Ekspektasi: 30 passed (169 assertions) 100% hijau.*

### Menjalankan Test Spesifik per Modul
```bash
# Pengujian API Wilayah Indonesia & Caching (BAB 6.1)
php artisan test --filter=WilayahTest

# Pengujian Metrik Dashboard Nasional & Notifikasi Multi-Role (v0.2.8)
php artisan test --filter=DashboardNotificationTest

# Pengujian Tata Kelola Universitas & Dosen Pembimbing (v0.2.7)
php artisan test --filter=UniversitasDosenTest

# Pengujian Luaran Akhir, Portofolio Publik & E-Sertifikat PDF (v0.2.6)
php artisan test --filter=LuaranPortofolioTest

# Pengujian Progress Mingguan & Surat Izin Orang Tua (v0.2.5)
php artisan test --filter=ProgressTest

# Pengujian Registrasi Mahasiswa & Relasi Universitas (v0.2.2)
php artisan test --filter=MahasiswaRegistrationTest
```

---

## 🏗️ 5. Struktur Arsitektur Direktori Backend

Kode sumber backend dirancang dengan pola arsitektur *Service-Repository Pattern* yang rapi dan modular:

```
baktinusantara-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/         # Controller tipis (Thin Controllers)
│   │   │   ├── AspirasiController.php
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── DesaController.php
│   │   │   ├── DosenController.php
│   │   │   ├── KelompokController.php
│   │   │   ├── LuaranController.php
│   │   │   ├── MahasiswaController.php
│   │   │   ├── NotificationController.php
│   │   │   ├── PortofolioController.php
│   │   │   ├── PosKebutuhanController.php
│   │   │   ├── ProgressController.php
│   │   │   ├── ProposalController.php
│   │   │   ├── UniversitasController.php
│   │   │   └── WilayahController.php
│   │   ├── Middleware/          # Middleware RBAC & Force JSON
│   │   └── Requests/            # FormRequest validasi ketat & sanitize input
│   ├── Models/                  # Eloquent Models & Entity Relationships
│   └── Services/                # Core Business Logic Layer (Fat Services)
│       ├── AspirasiService.php
│       ├── CertificateService.php   # Pure PDF Generator (E-Sertifikat)
│       ├── DashboardService.php     # Real-time Metrics Aggregator
│       ├── DesaService.php
│       ├── DosenService.php
│       ├── KelompokService.php
│       ├── LaporanDosenService.php
│       ├── LuaranService.php
│       ├── MahasiswaService.php
│       ├── NotificationService.php  # Event-Driven In-App Notifications
│       ├── PosKebutuhanService.php  # Haversine Distance Calculation
│       ├── ProgressService.php
│       ├── ProposalService.php      # Smart-Matching Scoring Engine
│       ├── UniversitasService.php
│       └── WilayahService.php       # Proxy & Caching Layer (BAB 6.1)
├── database/
│   ├── migrations/              # 21 Skema Migrasi Database Relasional
│   └── seeders/
│       └── DatabaseSeeder.php   # Master Seeder Lengkap Multi-Role
├── routes/
│   └── api.php                  # 50 Endpoint API RESTful Terdaftar
├── storage/
│   └── app/
│       ├── private/             # Berkas proposal & luaran pre-verified
│       └── public/              # Berkas portofolio publik & PDF sertifikat
├── tests/
│   └── Feature/                 # Automated Integration & Feature Tests
├── FRONTEND_INTEGRATION_GUIDE.md# Panduan Lengkap Kontrak API untuk Frontend
└── SETUP.md                     # Panduan Setup ini
```

---

## ❓ 6. Pemecahan Masalah Umum (Troubleshooting & FAQ)

### 1. Error: `SQLSTATE[HY000] [1049] Unknown database 'baktinusantaradb'`
**Penyebab:** Database belum dibuat di server MySQL lokal Anda.  
**Solusi:** Buat database `baktinusantaradb` terlebih dahulu melalui MySQL terminal atau phpMyAdmin:
```sql
CREATE DATABASE baktinusantaradb;
```

---

### 2. Error: `403 Forbidden` atau File E-Sertifikat / Foto 404
**Penyebab:** Symlink storage publik belum terpasang.  
**Solusi:** Jalankan:
```bash
php artisan storage:link
```

---

### 3. Ingin Mereset Database ke Kondisi Bersih Semula
**Solusi:** Jalankan perintah `migrate:fresh` bersama seeding:
```bash
php artisan migrate:fresh --seed
```

---

### 4. Cache Konfigurasi / Route Macet Setelah Perubahan
**Solusi:** Bersihkan cache aplikasi:
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

---

## 📖 7. Panduan Integrasi Frontend

Untuk rekan-rekan Frontend Developer yang ingin melihat daftar lengkap **50 endpoint API**, format payload request JSON/form-data, dan contoh response sukses, silakan buka:

👉 [`FRONTEND_INTEGRATION_GUIDE.md`](./FRONTEND_INTEGRATION_GUIDE.md)