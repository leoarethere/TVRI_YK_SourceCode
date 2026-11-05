# 📖 Manual Book — Website TVRI Stasiun D.I. Yogyakarta

**Versi Dokumen:** 1.1
**Tanggal:** 8 Agustus 2026
**Framework:** Laravel 12 · PHP 8.2 · MySQL · TailwindCSS

---

## Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Arsitektur Sistem](#2-arsitektur-sistem)
3. [Panduan Instalasi](#3-panduan-instalasi)
4. [Halaman Publik (Frontend)](#4-halaman-publik-frontend)
5. [Panel Admin (Dashboard)](#5-panel-admin-dashboard)
6. [Manajemen Konten](#6-manajemen-konten)
7. [Sistem Komentar & Rating](#7-sistem-komentar--rating)
8. [Manajemen Pengguna](#8-manajemen-pengguna)
9. [Pengaturan Website](#9-pengaturan-website)
10. [Keamanan](#10-keamanan)
11. [Deployment ke Hosting](#11-deployment-ke-hosting)

---

## 1. Pendahuluan

### 1.1 Tentang Aplikasi

Website resmi **TVRI Stasiun D.I. Yogyakarta** adalah sistem informasi berbasis web yang dirancang untuk menyajikan informasi penyiaran, berita, postingan, serta layanan publik secara digital. Aplikasi ini memiliki dua sisi utama:

- **Frontend (Publik):** Halaman yang dapat diakses oleh seluruh pengunjung tanpa perlu login.
- **Backend (Dashboard Admin):** Panel administrasi yang hanya dapat diakses oleh pengguna yang sudah terautentikasi.

### 1.2 Fitur Utama

| No | Fitur | Keterangan |
|----|-------|------------|
| 1 | Beranda Dinamis | Hero carousel, program unggulan, jadwal acara hari ini, postingan terbaru |
| 2 | Manajemen Berita | CRUD berita dengan kategori, gambar, SEO, dan status draft/publish |
| 3 | Manajemen Postingan | CRUD postingan/artikel dengan kategori dan fitur serupa berita |
| 4 | Program Penyiaran | Katalog program siaran TV dengan poster, sinopsis, dan link YouTube |
| 5 | Jadwal Acara | Jadwal harian acara TV, dikelompokkan per hari |
| 6 | Live Streaming | Halaman streaming langsung TVRI Yogyakarta (HLS) |
| 7 | Profil Instansi | Visi & Misi, Tugas & Fungsi, Sejarah, Prestasi, Himne & Mars |
| 8 | Layanan Publik | PPID, Reformasi Birokrasi, Info Magang, Info Kunjungan (dengan FAQ) |
| 9 | Komentar & Rating | Sistem komentar polimorfik untuk Berita, Postingan, dan Program Siaran |
| 10 | Banner Management | Carousel banner pada halaman beranda |
| 11 | Kontak & Sosial Media | Informasi kontak instansi dan tautan media sosial |
| 12 | Dark Mode | Toggle tema gelap/terang |

### 1.3 Teknologi yang Digunakan

- **Backend:** Laravel 12 (PHP 8.2)
- **Frontend:** Blade Templating, TailwindCSS, Alpine.js
- **Database:** MySQL 8
- **Rich Text Editor:** Trix Editor
- **Image Processing:** Intervention Image v3 (GD Driver)
- **Security:** HTMLPurifier (`mews/purifier`), CSRF Token, Bcrypt Hashing

---

## 2. Arsitektur Sistem

### 2.1 Pola Arsitektur

Aplikasi menggunakan pola **MVC (Model-View-Controller)** bawaan Laravel:

```
┌─────────────┐     ┌──────────────┐     ┌─────────────┐
│   Browser   │────▶│  Controller  │────▶│    Model     │
│  (Request)  │     │  (Logika)    │     │  (Database)  │
└─────────────┘     └──────┬───────┘     └─────────────┘
                           │
                    ┌──────▼───────┐
                    │    View      │
                    │  (Blade)     │
                    └──────────────┘
```

### 2.2 Struktur Direktori Utama

```
Yogyakarta/
├── app/
│   ├── Http/Controllers/     # 46 Controller (Frontend + Dashboard)
│   ├── Models/               # 26 Model Eloquent
│   └── Providers/            # Service Provider
├── database/
│   ├── migrations/           # 47 File Migrasi
│   └── seeders/              # Seeder (termasuk DummyDataSeeder)
├── resources/views/
│   ├── frontend/             # 10 Modul Halaman Publik
│   ├── backend/              # 22 Modul Dashboard Admin
│   └── components/           # Komponen Blade (Layout, Komentar, dll)
├── routes/
│   └── web.php               # 165 Route Terdaftar
└── public/
    ├── img/                  # Asset gambar statis
    └── storage/              # Symlink ke storage/app/public
```

---

## 3. Panduan Instalasi

### 3.1 Persyaratan Sistem

- PHP ≥ 8.2 dengan ekstensi: `gd`, `mbstring`, `openssl`, `pdo_mysql`, `fileinfo`
- Composer ≥ 2.x
- Node.js ≥ 20.19.x atau ≥ 22.12.x & NPM ≥ 10
- MySQL ≥ 8.0
- Web Server (Apache/Nginx/Laragon)

### 3.2 Langkah Instalasi (Lokal)

```bash
# 1. Clone repository
git clone <repository-url> Yogyakarta
cd Yogyakarta

# 2. Install dependensi PHP
composer install

# 3. Install dependensi frontend
npm ci

# 4. Salin file environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Konfigurasi database di file .env
# DB_DATABASE=yogyakarta_db
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Tentukan akun administrator awal di .env
# SEED_ADMIN_NAME="Administrator"
# SEED_ADMIN_USERNAME=admin
# SEED_ADMIN_EMAIL=admin@example.com
# SEED_ADMIN_PASSWORD="gunakan-password-kuat-minimal-12-karakter"

# 8. Jalankan migrasi & seeder
php artisan migrate --seed

# 9. Buat symbolic link storage
php artisan storage:link

# 10. Build asset frontend
npm run build

# 11. Jalankan development server
php artisan serve
```

### 3.3 Akun Administrator Awal

Seeder tidak menyimpan kredensial bawaan di source code. Nilai `SEED_ADMIN_USERNAME`,
`SEED_ADMIN_EMAIL`, dan `SEED_ADMIN_PASSWORD` wajib diisi sebelum menjalankan
`php artisan db:seed`; password harus terdiri dari sedikitnya 12 karakter.

---

## 4. Halaman Publik (Frontend)

### 4.1 Beranda (`/`)

Halaman utama menampilkan 7 seksi:
1. **Hero Carousel** — Banner slider dari tabel `banners`
2. **Program Unggulan** — Slider program siaran aktif
3. **Jadwal Acara Hari Ini** — Timeline rundown acara dengan indikator "ON AIR"
4. **Layanan & Informasi Publik** — Grid 4 kartu (PPID, RB, Magang, Kunjungan)
5. **Postingan Terbaru** — 3 artikel terbaru
6. **Visi & Misi** — Ringkasan visi-misi dalam card interaktif
7. **Hubungi Kami** — Informasi kontak + embed Google Maps

### 4.2 Berita (`/berita`)

- Daftar berita dengan filter: **Pencarian**, **Kategori**, **Sorting** (Terbaru/Terlama/Populer/A-Z/Z-A)
- Setiap berita memiliki halaman detail (`/berita/{slug}`) dengan view counter dan komponen komentar
- Pagination 9 item per halaman

### 4.3 Postingan (`/postingan`)

- Serupa dengan Berita, memiliki filter Kategori, Sorting, dan Pencarian
- Halaman detail (`/postingan/{slug}`) dengan fitur komentar & rating
- Pagination 9 item per halaman

### 4.4 Program Penyiaran (`/penyiaran`)

- Katalog program TV dalam layout grid poster (aspect ratio 3:4)
- Filter berdasarkan **Kategori Siaran** (pills navigasi) dan **Pencarian**
- Halaman detail (`/penyiaran/{slug}`) menampilkan sinopsis, trailer YouTube, dan komentar
- Pagination 12 item per halaman

### 4.5 Jadwal Acara (`/jadwal-acara`)

- Jadwal dikelompokkan per hari (Senin–Minggu)
- Filter per hari menggunakan pill navigation
- Menampilkan waktu tayang, nama program, dan kategori

### 4.6 Live Streaming (`/streaming`)

- Player video HLS untuk siaran langsung TVRI D.I. Yogyakarta
- URL stream: `https://ott-balancer.tvri.go.id/live/eds/Jogjakarta/hls/Jogjakarta.m3u8`

### 4.7 Profil Instansi

| Halaman | URL | Keterangan |
|---------|-----|------------|
| Visi & Misi | `/visi-misi` | Layout zig-zag dengan efek 3D card |
| Tugas & Fungsi | `/tugas-fungsi` | Dua seksi: Tugas & Fungsi |
| Sejarah | `/sejarah` | Timeline kronologis dengan layout alternating |
| Prestasi | `/prestasi` | Daftar penghargaan dengan badge tahun & kategori |
| Himne & Mars | `/himne-tvri` | Lirik + embed video YouTube/MP4 |

### 4.8 Layanan Publik

| Halaman | URL | Keterangan |
|---------|-----|------------|
| PPID | `/ppid` | Dokumen informasi publik dengan link sumber |
| Reformasi Birokrasi | `/info-rb` | Dokumen RB dengan link file |
| Info Magang | `/info-magang` | Informasi magang/PKL + seksi FAQ |
| Info Kunjungan | `/info-kunjungan` | Prosedur kunjungan industri + seksi FAQ |

---

## 5. Panel Admin (Dashboard)

### 5.1 Akses Dashboard

- **URL:** `/login`
- **Proteksi:** Middleware `auth` — hanya user terautentikasi yang bisa mengakses
- Setelah login, diarahkan ke `/dashboard`

### 5.2 Menu Dashboard

Dashboard menampilkan statistik ringkasan dan navigasi ke seluruh modul:

```
Dashboard
├── Konten
│   ├── Postingan (CRUD + Kategori)
│   ├── Berita (CRUD + Kategori)
│   ├── Program Siaran (CRUD + Kategori)
│   └── Jadwal Acara (CRUD + Kategori Hari)
├── Profil Instansi
│   ├── Visi & Misi
│   ├── Tugas & Fungsi
│   ├── Sejarah
│   ├── Prestasi
│   └── Himne & Mars TVRI
├── Layanan Publik
│   ├── PPID
│   ├── Reformasi Birokrasi
│   ├── Info Magang (+ FAQ)
│   └── Info Kunjungan (+ FAQ)
├── Interaksi
│   └── Manajemen Komentar
├── Pengaturan
│   ├── Banner / Slider
│   ├── Informasi Kontak
│   └── Media Sosial
└── Pengguna
    └── Manajemen User
```

---

## 6. Manajemen Konten

### 6.1 Membuat Postingan / Berita Baru

1. Buka menu **Postingan** → klik **Tambah Postingan**
2. Isi formulir:
   - **Judul** (wajib) — slug otomatis di-generate
   - **Kategori** (wajib) — pilih dari dropdown
   - **Gambar Unggulan** — upload gambar (otomatis dioptimasi: JPEG, 1200px, 70%)
   - **Ringkasan (Excerpt)** — deskripsi singkat untuk kartu preview
   - **Konten (Body)** — gunakan Trix Editor untuk format teks
   - **Status** — `Draft` atau `Published`
   - **Meta Title & Description** — untuk SEO (opsional)
3. Klik **Simpan**

> **Catatan:** Alur yang sama berlaku untuk **Berita** dan **Program Siaran**.

### 6.2 Status Konten

| Status | Keterangan |
|--------|------------|
| `Draft` | Tersimpan di database, **tidak tampil** di frontend |
| `Published` | Tampil di frontend untuk publik, kolom `published_at` otomatis terisi |

### 6.3 Manajemen Gambar

- Gambar diproses oleh **Intervention Image** sebelum disimpan
- Konversi otomatis ke format **JPEG** dengan kualitas **70%** dan lebar maksimal **1200px**
- Disimpan di `storage/app/public/` dan diakses melalui symbolic link

---

## 7. Sistem Komentar & Rating

### 7.1 Gambaran Umum

Sistem komentar menggunakan **Polymorphic Relationship** sehingga satu tabel `comments` dapat menampung komentar untuk:
- Program Siaran (`App\Models\Broadcast`)
- Berita (`App\Models\News`)
- Postingan (`App\Models\Post`)

### 7.2 Cara Kerja

1. Pengunjung mengisi **Nama**, **Email**, **Rating** (1-5 bintang), dan **Komentar**
2. Form dilindungi oleh **CSRF Token** dan **Rate Limiter** (maks 10 komentar/menit per IP)
3. Komentar masuk dengan status **pending** dan belum tampil ke publik
4. Admin/editor dapat menyetujui, menolak, atau menghapus komentar melalui menu **Manajemen Komentar**

### 7.3 Keamanan Komentar

- **Whitelist Model:** Hanya 3 model yang diizinkan (`Broadcast`, `News`, `Post`)
- **XSS Protection:** Output menggunakan `{{ }}` (escaped) di Blade
- **Throttle:** `throttle:10,1` pada route `POST /komentar`
- **Cascade Delete:** Menghapus konten induk otomatis menghapus komentar terkait

---

## 8. Manajemen Pengguna

### 8.1 Role Pengguna

| Role | Nilai `role` | Hak Akses |
|------|--------------|-----------|
| Administrator | `admin` | Akses penuh, termasuk pengguna dan pengaturan sensitif |
| Editor | `secondary` | Akses dashboard untuk pengelolaan konten |
| Pegawai | `pegawai` | Akses portal pegawai; tidak dapat membuka dashboard |

### 8.2 Proteksi Akun

- Admin **tidak bisa menghapus akun sendiri**
- Admin **tidak bisa menghapus akun administrator terakhir**
- Password baru minimal **12 karakter**
- Password di-hash menggunakan **Bcrypt** dengan **12 rounds**
- Fitur **Lupa Password** tersedia melalui email (SMTP)

---

## 9. Pengaturan Website

### 9.1 Banner / Slider

- Kelola banner carousel di beranda
- Setiap banner memiliki: Judul, Subtitle, Gambar, Link, Teks Tombol, Urutan, Status Aktif

### 9.2 Informasi Kontak

- Satu record kontak instansi: Telepon Admin, Telepon Kerjasama, Hotline WhatsApp, Alamat, Email, Embed Google Maps

### 9.3 Media Sosial

- Satu record untuk seluruh link sosial media: Instagram, Facebook, Twitter/X, TikTok, YouTube, Email

---

## 10. Keamanan

| Aspek | Implementasi |
|-------|-------------|
| Autentikasi | Laravel Auth dengan session driver `database` |
| CSRF | Token `@csrf` di semua form |
| XSS | Output escaped `{{ }}`, HTML dibersihkan dengan `clean()` (HTMLPurifier) |
| Password | Bcrypt 12 rounds |
| Route Protection | Middleware `auth` + `dashboardUser`; pengaturan sensitif juga memakai `isAdmin` |
| Rate Limiting | `throttle:10,1` pada endpoint komentar |
| File Upload | Validasi tipe & ukuran, konversi otomatis via Intervention Image |
| Foreign Key | `onDelete('restrict')` pada kategori, `onDelete('cascade')` pada user |

---

## 11. Deployment ke Hosting

### 11.1 Konfigurasi `.env` Produksi

Gunakan template private `deploy/cpanel/env.production.example`. Pertahankan
`APP_KEY` live, gunakan timezone `Asia/Jakarta`, cookie sesi HTTPS, filesystem
`public`, serta log `daily`. Jangan menyalin `.env` lokal ke server.

### 11.2 Perintah Deploy

Gunakan alur tunggal pada [deployment-cpanel.md](./deployment-cpanel.md). Jangan
menjalankan `key:generate` pada database live, jangan menjalankan
`artisan storage:link` untuk struktur public root terpisah, dan jangan membangun
dependency frontend di server. Script deployment memakai PHP CLI cPanel,
menjalankan migration/cache dengan urutan aman, serta mempublikasikan manifest
Vite paling akhir.

### 11.3 Perizinan File

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

**© 2026 TVRI Stasiun D.I. Yogyakarta — Dokumen ini dibuat secara otomatis berdasarkan analisis codebase.**
