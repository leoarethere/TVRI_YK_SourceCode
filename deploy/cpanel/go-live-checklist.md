# Checklist Go-Live cPanel

Isi tanggal, pelaksana, dan tanda centang saat deployment. Jangan lanjut ke tahap
berikutnya jika pemeriksaan kritis gagal.

## Identitas

- Tanggal/jam: ____________________
- Pelaksana: ____________________
- `APP_ROOT`: `/home/yogz7392/publik`
- `PUBLIC_ROOT`: `/home/yogz7392/public_html`
- Domain: `https://yogyakarta.tvri.go.id`

## Sebelum perubahan

- [ ] `artisan` dikonfirmasi berada tepat di `/home/yogz7392/publik/artisan`.
- [ ] Domain dan PHP CLI menggunakan PHP 8.2+.
- [ ] Backup database live berhasil dan file dapat dibaca.
- [ ] Backup `.env` production berhasil.
- [ ] Backup `storage/app/public` berhasil (media lengkap).
- [ ] Backup app/build live berhasil.
- [ ] Paket release lokal tidak memuat `.env`, SQL, ZIP backup, atau Git.
- [ ] Node.js memenuhi Vite 7 (20.19+ atau 22.12+).
- [ ] `npm ci`, `npm run build`, dan test lokal berhasil.
- [ ] `composer.lock` serta `package-lock.json` tersedia di release.

## Instalasi

- [ ] Maintenance mode aktif.
- [ ] Struktur release tidak memiliki folder rangkap.
- [ ] `.env` production lama dipertahankan dan `APP_DEBUG=false`.
- [ ] `APP_TIMEZONE=Asia/Jakarta`, `FILESYSTEM_DISK=public`, dan cookie sesi HTTPS aktif.
- [ ] Log production memakai channel `daily` dengan retensi terbatas.
- [ ] `APP_KEY` lama dipertahankan.
- [ ] `composer install --no-dev --optimize-autoloader` berhasil.
- [ ] `composer check-platform-reqs --no-dev` berhasil.
- [ ] Database diimpor ke database yang benar.
- [ ] `php artisan migrate --force` berhasil.
- [ ] `php artisan broadcasts:audit-categories` selesai tanpa relasi legacy yang ambigu.
- [ ] `storage` dan `bootstrap/cache` writable tanpa permission `777`.
- [ ] `publish-public.sh` selesai; asset hash lama tidak dihapus.
- [ ] Manifest lama tersimpan di backup private untuk rollback otomatis.
- [ ] Manifest live sama dengan release dan seluruh asset referensinya tersedia.
- [ ] `public_html/index.php` menunjuk `APP_ROOT` yang benar.
- [ ] `.htaccess` tidak memuat handler `ea-php74`.
- [ ] `.htaccess` memberi cache immutable hanya pada `/build/assets/` ber-hash.
- [ ] `public_html/storage` adalah symlink ke `storage/app/public`.
- [ ] Tidak ada `.env`, `.sql`, `.zip`, atau backup di `public_html`.

## Cache dan verifikasi

- [ ] Cache file lama dibersihkan sebelum migration dan cache database sesudahnya.
- [ ] `php artisan optimize` berhasil.
- [ ] `optimize-laravel.sh` memakai PHP_BIN cPanel dan melepas maintenance mode.
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
