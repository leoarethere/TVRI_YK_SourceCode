# Diagram UML & ERD — Sistem Informasi TVRI Stasiun D.I. Yogyakarta

Kumpulan diagram yang menggambarkan sistem dari berbagai sudut pandang.
Semua diagram disimpan dalam **kode Mermaid** (file `.mmd`) dan dapat dirender
di GitHub, VS Code (plugin Mermaid), https://mermaid.live, atau `mmdc` (mermaid-cli).
Seluruh file telah diverifikasi ter-render dengan Mermaid 11.

## Daftar diagram

| No | Diagram | File | Fokus |
|----|---------|------|-------|
| 1 | Use Case Diagram | [`use-case.mmd`](./use-case.mmd) | Aktor (Pengunjung, Pegawai, Admin Secondary, Super Admin, Layanan Email) beserta relasi «include», «extend», dan «generalisasi» |
| 2 | Activity Diagram — Autentikasi | [`activity-01-authentication.mmd`](./activity-01-authentication.mmd) | Login dashboard umum + portal pegawai, verifikasi captcha, proteksi role |
| 2 | Activity Diagram — Komentar | [`activity-02-comment-moderation.mmd`](./activity-02-comment-moderation.mmd) | Pengiriman komentar publik, honeypot anti-spam, moderasi (setujui/tolak/hapus) |
| 2 | Activity Diagram — Publikasi Konten | [`activity-03-content-publication.mmd`](./activity-03-content-publication.mmd) | Alur CRUD semua modul dashboard (pola umum), unggah gambar, invalidation cache |
| 2 | Activity Diagram — Manajemen Pengguna | [`activity-04-user-management.mmd`](./activity-04-user-management.mmd) | CRUD pengguna & peran, proteksi admin terakhir, pencabutan sesi |
| 3 | Class Diagram | [`class.mmd`](./class.mmd) | Model domain Eloquent + trait (Concerns) + service, rule, middleware, controller utama |
| 4 | ERD (inti) | [`erd-core.mmd`](./erd-core.mmd) | Skema tabel inti berrelasi (users, sessions, konten, jadwal, komentar) — direkomendasikan untuk dibaca cepat |
| 4 | ERD (lengkap) | [`erd.mmd`](./erd.mmd) | Skema lengkap 27 tabel + foreign key + gaya hapus, diverifikasi dari file migrasi |

## Catatan akurasi

- **Use case**: Mermaid tidak memiliki tipe diagram use case stabil bawaan,
  sehingga diagram ditulis sebagai `flowchart` yang meniru notasi UML
  (aktor = lingkaran, use case = stadium, relasi `«include»` / `«extend»` /
  `«generalisasi»` sebagai panah putus-putus berlabel).
- **Activity**: sintaks `activityDiagram` Mermaid sudah dihapus pada
  Mermaid 11.17 (tidak akan dirender di GitHub), sehingga keempat alur proses
  ditulis ulang sebagai `flowchart` yang tetap memakai notasi UML activity:
  bulat sebagai awal/akhir, rounded rectangle sebagai aktivitas, dan
  diamond sebagai decision. Semua kondisi cabang mengikuti perilaku aktual
  kode (middleware `isAdmin`/`dashboardUser`, proteksi "admin terakhir",
  force-logout akun salah portal, honeypot komentar, dst).
  Diagram dipisah per proses bisnis (4 berkas) agar tetap terbaca.
- **Class**: hanya field & method penting yang ditulis agar diagram ringkas.
  Trait dipakai via `..|>` (realization), disit dari `app/Models/Concerns/`.
- **ERD**: dibangun dari `database/migrations/*.php` (termasuk ALTER dari
  migrasi lanjutan, mis. perubahan `CASCADE` → `RESTRICT` pada
  `broadcasts.user_id`). Tabel bawaan framework (`cache`, `jobs`) tidak
  digambarkan. Karena 16 tabel informasi berdiri sendiri tanpa FK,
  layout ERD lengkap menjadi sangat lebar — pasangan `erd-core.mmd`
  disediakan untuk versi yang nyaman dibaca.
