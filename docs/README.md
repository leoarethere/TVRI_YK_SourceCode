# 📚 Dokumentasi Website TVRI Stasiun D.I. Yogyakarta

**Versi:** 1.1 | **Tanggal:** 14 Agustus 2026 | **Laravel 12 · PHP 8.2 · MySQL**

---

## Daftar Dokumen

| # | Dokumen | Deskripsi | File |
|---|---------|-----------|------|
| 1 | **Manual Book** | Panduan penggunaan lengkap untuk admin dan pengguna | [manual-book.md](./manual-book.md) |
| 2 | **Dokumentasi Database** | ERD, LRS, dan Kamus Data | [database-diagram.md](./database-diagram.md) |
| 3 | **Class Diagram** | Diagram kelas Model, Controller, dan alur sistem | [class-diagram.md](./class-diagram.md) |
| 4 | **Deployment cPanel** | Upload, database, storage link, cache production, verifikasi, dan rollback | [deployment-cpanel.md](./deployment-cpanel.md) |

---

## Ringkasan Cepat

### Stack Teknologi
- **Backend:** Laravel 12 (PHP 8.2), MySQL 8
- **Frontend:** Blade, TailwindCSS, Alpine.js
- **Image:** Intervention Image v3
- **Security:** HTMLPurifier (mews/purifier), Bcrypt 12 rounds

### Statistik Proyek
- 📊 **26 Tabel** database
- 🎮 **48 Controller** (Frontend + Dashboard)
- 🧩 **26 Model** Eloquent
- 🛤️ **182 Route** terdaftar
- 📄 **31 Modul** view (10 frontend + 21 backend)

### Akses Sistem
- **URL Publik:** `/`
- **URL Admin:** `/login` → `/dashboard`
- **Proteksi:** Middleware `auth` + `dashboardUser`; konfigurasi sensitif dibatasi ke `admin`

### Fitur Unggulan
- ✅ Sistem komentar & rating polimorfik (Berita, Postingan, Program Siaran)
- ✅ Dark mode toggle
- ✅ SEO-ready (meta title, meta description, slug)
- ✅ Optimasi gambar otomatis (JPEG 70%, max 1200px)
- ✅ Rate limiting komentar (10/menit)
- ✅ Cache homepage (10 menit data, 1 jam slider)
- ✅ Cascade delete pada relasi konten-komentar

---

## Diagram Singkat Arsitektur

```
Browser
  │
  ├── [Publik] Frontend Routes ──► HomeController, PostController,
  │                                 NewsController, BroadcastController,
  │                                 JadwalController, StreamingController
  │                                 (+ 10 halaman statis instansi)
  │
  └── [Auth]  Dashboard Routes ──► DashboardController (+ 20 sub-controller)
                                    └── Middleware: auth + dashboardUser (+ isAdmin)
```

---

*Dokumen ini dibuat berdasarkan analisis lengkap codebase proyek.*
