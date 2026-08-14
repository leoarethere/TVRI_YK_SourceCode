# Checklist Go-Live cPanel

Isi tanggal, pelaksana, dan tanda centang saat deployment. Jangan lanjut ke tahap
berikutnya jika pemeriksaan kritis gagal.

## Identitas

- Tanggal/jam: ____________________
- Pelaksana: ____________________
- `APP_ROOT`: ____________________
- `PUBLIC_ROOT`: `/home/yogz7392/public_html`
- Domain: `https://yogyakarta.tvri.go.id`

## Sebelum perubahan

- [ ] Lokasi `artisan` dikonfirmasi dengan `find`, bukan diasumsikan.
- [ ] Domain dan PHP CLI menggunakan PHP 8.2+.
- [ ] Backup database live berhasil dan file dapat dibaca.
- [ ] Backup `.env` production berhasil.
- [ ] Backup `storage/app/public` berhasil (media lengkap).
- [ ] Backup app/build live berhasil.
- [ ] Paket release lokal tidak memuat `.env`, SQL, ZIP backup, atau Git.
- [ ] `npm run build` dan test lokal berhasil.

## Instalasi

- [ ] Maintenance mode aktif.
- [ ] Struktur release tidak memiliki folder rangkap.
- [ ] `.env` production lama dipertahankan dan `APP_DEBUG=false`.
- [ ] `APP_KEY` lama dipertahankan.
- [ ] `composer install --no-dev --optimize-autoloader` berhasil.
- [ ] `composer check-platform-reqs --no-dev` berhasil.
- [ ] Database diimpor ke database yang benar.
- [ ] `php artisan migrate --force` berhasil.
- [ ] `storage` dan `bootstrap/cache` writable tanpa permission `777`.
- [ ] Isi `public` dan Vite build terbaru tersedia di `public_html`.
- [ ] `public_html/index.php` menunjuk `APP_ROOT` yang benar.
- [ ] `.htaccess` tidak memuat handler `ea-php74`.
- [ ] `public_html/storage` adalah symlink ke `storage/app/public`.
- [ ] Tidak ada `.env`, `.sql`, `.zip`, atau backup di `public_html`.

## Cache dan verifikasi

- [ ] `php artisan optimize:clear` berhasil.
- [ ] `php artisan optimize` berhasil.
- [ ] `deploy/cpanel/preflight.sh` selesai dengan 0 kegagalan.
- [ ] Maintenance mode dinonaktifkan dengan `php artisan up`.
- [ ] Beranda, detail konten, login, lupa password, dan dashboard dapat dibuka.
- [ ] Statistik dan data lama sesuai.
- [ ] Gambar lama tampil dan upload gambar baru berhasil.
- [ ] Aksi create/edit/delete penting berhasil.
- [ ] Tidak ada asset 404, mixed content, atau error 500.
- [ ] `storage/logs/laravel.log` tidak memiliki error baru.
- [ ] Queue cron aktif, atau `QUEUE_CONNECTION=sync` dipilih.
- [ ] Pengiriman reset password diuji bila mail provider sudah tersedia.

## Keputusan

- [ ] Go-live diterima.
- [ ] Rollback dilakukan.
- Catatan: ________________________________________________________________

