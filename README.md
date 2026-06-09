# Pesantren CMS - Sistem Manajemen Presensi & Kegiatan Pondok Pesantren

[![Demo](https://img.shields.io/badge/Demo-Live-brightgreen?style=for-the-badge)](https://absensippmam-production.up.railway.app)
[![Laravel Version](https://img.shields.io/badge/Laravel-11-red?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue?style=for-the-badge&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)

**Pesantren CMS** adalah platform sistem manajemen berbasis web yang dirancang khusus untuk mempermudah pengelolaan data santri, pencatatan kehadiran (presensi harian & Qiyamullail), pengelolaan jadwal pengajian, serta audit log aktivitas pengguna di lingkungan pondok pesantren.

Aplikasi ini menggunakan framework **Laravel 11** untuk backend, dengan antarmuka pengguna **Stisla Admin Theme** (Bootstrap 5, jQuery, Sass) di frontend, serta dilengkapi dengan otentikasi **JWT (JSON Web Token)** untuk integrasi aplikasi mobile.

**Demo Aplikasi:** [absensippmam-production.up.railway.app](https://absensippmam-production.up.railway.app)

---

## 🌟 Fitur Utama

### 1. Manajemen Presensi Terintegrasi
* **Presensi Harian (Subuh & Isya):**
  * Siklus mingguan: Ahad hingga Sabtu.
  * Pencatatan kehadiran dapat dilakukan secara manual via checklist atau melalui **QR Code Scanner** terintegrasi.
  * Halaman pembuatan QR Code unik untuk masing-masing santri.
  * Rekapan presensi mingguan yang dapat diexport ke format **PDF (DomPDF)** dan **Excel (.xls)**.
* **Presensi Qiyamullail (Shalat Malam):**
  * Siklus mingguan khusus: Sabtu hingga Kamis.
  * Dibatasi oleh waktu pengisian presensi melalui Middleware khusus (`qiyam.time`).
  * Rekapan presensi Qiyamullail mingguan yang dapat diexport ke format **PDF** dan **Excel**.

### 2. Manajemen Data Utama
* **Data Santri:** CRUD (Create, Read, Update, Delete) data santri lengkap dengan NIS (Nomor Induk Santri). Sistem secara otomatis akan membuatkan akun pengguna (`User`) santri saat data santri baru disimpan.
* **Jadwal Pengajian:** Manajemen jadwal pelajaran/pengajian harian dengan routing berbasis UUID.
* **Manajemen Pengguna:** Pengelolaan akun pengguna sistem (SuperAdmin, Administrator, Pengurus, dan Santri).

### 3. Keamanan & RBAC (Role-Based Access Control) Dinamis
* **4 Role Pengguna bawaan:**
  1. `SuperAdmin`: Akses penuh terhadap seluruh sistem, data, dan audit trail log. Memiliki hak visibilitas khusus (santri dari SuperAdmin disembunyikan dari role di bawahnya).
  2. `Administrator`: Mengelola data santri, jadwal, presensi, serta manajemen pengguna non-SuperAdmin.
  3. `Pengurus`: Mengelola operasional harian (presensi santri, jadwal) tanpa akses manajemen data pengguna sistem.
  4. `Santri`: Mengakses profil pribadi, melihat jadwal, mengunduh QR Code pribadi, serta melihat histori presensi mingguan miliknya sendiri.
* **RBAC Fleksibel:** Pengaturan izin akses didefinisikan secara default di `config/rbac.php`, namun dapat diubah secara dinamis dan real-time di database melalui tabel `rbac_permissions` tanpa menyentuh kode program.

### 4. Audit Trail (Log Aktivitas)
* Melacak setiap aksi perubahan data penting yang dilakukan oleh Administrator dan Pengurus di dalam CMS demi menjaga transparansi dan akuntabilitas sistem.

### 5. API Backend (JWT Auth)
* Endpoint API versi 1 (`/api/v1`) aman dengan **JWT Authentication** (`tymon/jwt-auth`):
  * Login & Logout Santri
  * Lihat & Edit Profil Santri
  * Ubah Password Santri
  * Cari Data Santri via ID untuk kebutuhan Scan QR (`GET /api/santri/{id}`)

---

## 🛠️ Spesifikasi Teknologi

* **Framework Backend:** Laravel 11.x (PHP 8.2+)
* **Tema Frontend:** Stisla Admin Theme (Bootstrap 5, jQuery, Sass)
* **Kompilasi Aset:** Laravel Mix (Webpack)
* **Basis Data:** MySQL (Production) & SQLite `:memory:` (Unit/Feature Testing)
* **Library Utama:**
  * `tymon/jwt-auth` (Otentikasi API)
  * `barryvdh/laravel-dompdf` (Export PDF)
  * `html5-qrcode` (Scan QR Code via browser/kamera)

---

## 🚀 Panduan Instalasi Lokal

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek ini di komputer lokal Anda:

### Prasyarat
* PHP >= 8.2 (dengan ekstensi PDO MySQL aktif)
* Composer
* Node.js & NPM
* MySQL Database Server

### Langkah-langkah

1. **Kloning Repositori:**
   ```bash
   git clone https://github.com/Egazaky/pesantren-cms.git
   cd pesantren-cms
   ```

2. **Instal Dependensi PHP:**
   ```bash
   composer install
   ```

3. **Instal Dependensi Frontend:**
   ```bash
   npm install
   ```

4. **Konfigurasi Environment:**
   Salin berkas `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   Buka berkas `.env` lalu sesuaikan konfigurasi basis data Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_anda
   DB_USERNAME=username_mysql_anda
   DB_PASSWORD=password_mysql_anda
   ```

5. **Generate Kunci Enkripsi Aplikasi & JWT Secret:**
   ```bash
   php artisan key:generate
   php artisan jwt:secret
   ```

6. **Migrasi dan Seed Database:**
   Jalankan migrasi tabel beserta pengisian data awal (seeder):
   ```bash
   php artisan migrate
   ```
   ```bash
   php artisan db:seed
   ```

7. **Buat Tautan Simbolik Direktori Storage:**
   ```bash
   php artisan storage:link
   ```

8. **Kompilasi Aset Frontend (CSS/JS):**
   * Untuk mode pengembangan (development):
     ```bash
     npm run dev
     ```
   * Untuk memantau perubahan file secara otomatis (watch):
     ```bash
     npm run watch
     ```
   * Untuk build production:
     ```bash
     npm run prod
     ```

9. **Jalankan Development Server:**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui browser Anda di `http://127.0.0.1:8000`.

---

## 🔑 Kredensial Akun Bawaan (Seeder)

Setelah Anda menjalankan `php artisan db:seed`, akun-akun berikut akan tersedia di database untuk uji coba:

| Role | Email | Password | Keterangan / Deskripsi |
|---|---|---|---|
| **SuperAdmin** | `superadmin@ppm.am` | `password` | Pemegang kendali penuh seluruh sistem. |
| **Administrator** | `admin@ponpes.com` | `password` | Admin pengelola data & pengguna lain. |
| **Pengurus** | `pengurus@ponpes.com` | `password` | Pengurus harian (presensi & jadwal). |
| **Santri** | `santri@ponpes.com` | `password` | Contoh akun santri (hanya lihat data sendiri). |

---

## 🧪 Menjalankan Pengujian (Testing)

Proyek ini telah dilengkapi dengan pengujian otomatis (*unit* & *feature tests*) menggunakan PHPUnit. Basis data testing menggunakan SQLite `:memory:` sehingga tidak akan mengganggu basis data utama Anda.

Untuk menjalankan pengujian, ketik perintah:
```bash
vendor/bin/phpunit
```

Untuk memformat gaya penulisan kode sesuai standar Laravel Pint:
```bash
./vendor/bin/pint
```

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah lisensi [MIT](https://opensource.org/licenses/MIT).
