# Deployment Laravel ke cPanel TVRI Yogyakarta

Panduan ini ditujukan untuk project Laravel 11 TVRI Yogyakarta. Targetnya adalah
deployment production yang dapat dipulihkan, tidak membocorkan `.env`, dan tetap
memisahkan source Laravel dari web root.

## 1. Struktur folder

```text
/home/yogz7392/
├── publik/
│   └── app/                    # APP_ROOT: project Laravel lengkap
│       ├── app/
│       ├── bootstrap/
│       ├── public/
│       ├── storage/
│       ├── vendor/
│       ├── .env
│       └── artisan
└── public_html/                # PUBLIC_ROOT: hanya berkas dari app/public
    ├── build/
    ├── storage -> ../publik/app/storage/app/public
    ├── index.php
    └── .htaccess
```

Folder `storage` Laravel tidak dipindahkan ke `public_html`. Data asli tetap di
`publik/app/storage`. `public_html/storage` hanya symbolic link menuju
`publik/app/storage/app/public`.

Arsip server sebelumnya memperlihatkan kemungkinan susunan `publik/publik`.
Sebelum deployment, cari lokasi sebenarnya dari `artisan`:

```bash
find /home/yogz7392/publik -maxdepth 3 -name artisan -type f
```

Jika hasilnya bukan `/home/yogz7392/publik/app/artisan`, sesuaikan `APP_ROOT` pada
semua perintah. Jangan menebak jalurnya.

## 2. Jalur dan versi PHP

Definisikan variabel berikut setiap membuka sesi Terminal cPanel:

```bash
APP_ROOT=/home/yogz7392/publik/app
PUBLIC_ROOT=/home/yogz7392/public_html
PHP_BIN=/opt/cpanel/ea-php82/root/usr/bin/php
"$PHP_BIN" -v
```

Laravel 11 memerlukan PHP 8.2+. Atur domain `yogyakarta.tvri.go.id` ke PHP 8.2
melalui **cPanel > MultiPHP Manager**. Jangan mempertahankan handler `ea-php74`
di `.htaccess`.

## 3. Persiapan sebelum upload

1. Jadwalkan maintenance window.
2. Backup database live melalui cPanel Backup/phpMyAdmin atau `mysqldump`.
3. Backup `.env`, app lama, dan seluruh `storage/app/public`.
4. Build aset dan tes di komputer lokal:

   ```powershell
   npm.cmd ci
   npm.cmd run build
   php artisan test
   ```

5. Pastikan `public/build/manifest.json` ada.
6. Jangan masukkan `.env`, `.git`, `node_modules`, dump SQL, backup ZIP, log, atau
   cache lokal ke paket release.
7. Dump hasil migrasi lokal siap pakai berada di
   `storage/app/backups/db_litebeam-after-legacy-import-20260811-085551.sql`.
   Upload dump ke folder private, bukan `public_html`.

## 4. Deployment melalui Terminal cPanel (disarankan)

### 4.1 Backup server

```bash
STAMP=$(date +%Y%m%d-%H%M%S)
mkdir -p /home/yogz7392/backups/$STAMP
cp -a "$APP_ROOT/.env" /home/yogz7392/backups/$STAMP/env.backup
cp -a "$APP_ROOT/storage/app/public" /home/yogz7392/backups/$STAMP/media
mysqldump -u NAMA_USER_DB -p NAMA_DATABASE > /home/yogz7392/backups/$STAMP/database.sql
```

Ganti nama user/database. Terminal meminta password sehingga tidak masuk riwayat
shell. Simpan semua backup di luar `public_html`.

### 4.2 Maintenance, upload, dan dependency

```bash
cd "$APP_ROOT"
"$PHP_BIN" artisan down --retry=60
```

Upload release ZIP ke `/home/yogz7392/publik/`, ekstrak, lalu pastikan `artisan`,
`composer.json`, `app`, `bootstrap`, `public`, `resources`, dan `routes` berada
langsung di `APP_ROOT`. Jangan sampai terbentuk `/publik/app/Yogyakarta/...` atau
`/publik/publik/...`.

Pertahankan `.env` dan media production; jangan timpa dengan versi lokal:

```bash
cp /home/yogz7392/backups/$STAMP/env.backup "$APP_ROOT/.env"
mkdir -p "$APP_ROOT/storage/app/public"
cp -a /home/yogz7392/backups/$STAMP/media/. "$APP_ROOT/storage/app/public/"
cd "$APP_ROOT"
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
composer check-platform-reqs --no-dev
```

Jika Composer tidak tersedia, gunakan executable Composer dari hosting atau
upload `vendor` yang dibuat dengan PHP/platform dependency yang sama. Jangan
menjalankan `composer update` di live.

### 4.3 `.env` production

Periksa nilai berikut tanpa membagikan secret:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yogyakarta.tvri.go.id
LOG_LEVEL=warning
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Simpan `APP_KEY`, database, dan kredensial mail hanya di `.env` server. Jangan
menjalankan `key:generate` jika app lama sudah memiliki `APP_KEY`; data
terenkripsi/cookie lama dapat menjadi tidak valid. Jika tidak ada queue worker,
gunakan `QUEUE_CONNECTION=sync`.

### 4.4 Database

Upload dump ke folder private, misalnya `/home/yogz7392/backups/import.sql`:

```bash
mysql -u NAMA_USER_DB -p NAMA_DATABASE < /home/yogz7392/backups/import.sql
cd "$APP_ROOT"
"$PHP_BIN" artisan migrate --force
```

Alternatifnya gunakan phpMyAdmin: backup/export database live, pilih database
tujuan secara teliti, kosongkan tabel testing yang akan diganti, lalu **Import**.

### 4.5 Permission runtime

```bash
cd "$APP_ROOT"
mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache
```

Gunakan `775`/group-writable sesuai hosting. Jangan gunakan `777`. Source lain
umumnya cukup `755` untuk direktori dan `644` untuk file.

### 4.6 Berkas public

Salin isi `APP_ROOT/public`, tetapi pertahankan `index.php`, `.htaccess`, dan
symlink `storage` khusus cPanel:

```bash
mv "$PUBLIC_ROOT/build" "/home/yogz7392/backups/$STAMP/build" 2>/dev/null || true
rsync -a --exclude=index.php --exclude=.htaccess --exclude=storage/ --exclude=build/ "$APP_ROOT/public/" "$PUBLIC_ROOT/"
cp -a "$APP_ROOT/public/build" "$PUBLIC_ROOT/build"
```

Gunakan `deploy/cpanel/public-index.php.example` sebagai `public_html/index.php`
dan sesuaikan hanya `$appRoot`. Gunakan `public-htaccess.example` untuk rewrite.
PHP domain tetap dipilih melalui MultiPHP Manager.

Template nilai production tanpa secret tersedia di
`deploy/cpanel/env.production.example`. Salin nilainya secara selektif ke `.env`
live; jangan mengganti `APP_KEY` dan kredensial live secara membabi buta.

### 4.7 Storage link

Karena public root dipisah dari `APP_ROOT/public`, buat link langsung ke
`public_html`:

```bash
TARGET="$APP_ROOT/storage/app/public"
LINK="$PUBLIC_ROOT/storage"
ls -ld "$LINK" 2>/dev/null || true
```

Jika `LINK` belum ada:

```bash
ln -s "$TARGET" "$LINK"
```

Jika `public_html/storage` adalah folder biasa, jangan langsung menghapusnya.
Bandingkan dan backup isinya, pindahkan ke backup, lalu buat symlink. Verifikasi:

```bash
ls -ld "$PUBLIC_ROOT/storage"
readlink -f "$PUBLIC_ROOT/storage"
```

Hasilnya harus `$APP_ROOT/storage/app/public`.

### 4.8 Cache production dan buka aplikasi

```bash
cd "$APP_ROOT"
"$PHP_BIN" artisan optimize:clear
"$PHP_BIN" artisan migrate:status
"$PHP_BIN" artisan optimize
"$PHP_BIN" artisan queue:restart
"$PHP_BIN" artisan up
```

`artisan optimize` membuat cache konfigurasi, event, route, dan view di
`bootstrap/cache`/`storage`. Tidak ada perintah bernama `bootstrap:cache`.

## 5. Deployment manual melalui File Manager

1. Aktifkan **Show Hidden Files**.
2. Buat `/home/yogz7392/backups/TANGGAL-JAM`.
3. Salin `.env`, `storage/app/public`, dan app lama ke backup.
4. Upload release ZIP ke `/home/yogz7392/publik`, bukan `public_html`.
5. Extract dan pastikan project langsung di `/home/yogz7392/publik/app`.
6. Salin `.env` production dan media backup ke app baru.
7. Jika server tidak punya Composer, paket harus memuat folder `vendor` hasil
   `composer install --no-dev --optimize-autoloader`.
8. Salin isi `public` ke `public_html`; gunakan template `index.php` cPanel dan
   jangan menimpa `storage` tanpa backup.
9. Dengan **Change Permissions**, buat `storage` dan `bootstrap/cache` writable
   (`775` jika didukung), termasuk subfolder. Jangan gunakan `777`.
10. Import SQL lewat phpMyAdmin, lalu jalankan migration/cache lewat Terminal.

File Manager biasanya tidak dapat membuat symlink secara andal. Gunakan satu
perintah Terminal `ln -s`, pertahankan symlink lama yang benar, atau minta support
hosting. Folder media hasil copy bukan pengganti permanen: upload baru ditulis ke
`APP_ROOT/storage/app/public` dan tidak otomatis tersalin ke `public_html`.

File Manager juga tidak menggantikan Artisan. Jalankan:

```bash
cd /home/yogz7392/publik/app
/opt/cpanel/ea-php82/root/usr/bin/php artisan optimize:clear
/opt/cpanel/ea-php82/root/usr/bin/php artisan migrate --force
/opt/cpanel/ea-php82/root/usr/bin/php artisan optimize
```

Jika Terminal tidak tersedia, minta hosting menjalankannya. Jangan membuat script
PHP publik sekali pakai untuk menjalankan Artisan karena berisiko keamanan.

## 6. Verifikasi

Jalankan pemeriksa read-only dari project:

```bash
cd "$APP_ROOT"
APP_ROOT="$APP_ROOT" PUBLIC_ROOT="$PUBLIC_ROOT" PHP_BIN="$PHP_BIN" bash deploy/cpanel/preflight.sh
```

Lakukan smoke test:

- Beranda, detail, `/login`, lupa password, dan dashboard tidak error 500.
- Statistik serta data lama tampil benar.
- Gambar lama tampil; upload baru dapat dibuka dari browser.
- Form create/edit/delete penting bekerja.
- Tidak ada asset 404/mixed content di Developer Tools.
- `storage/logs/laravel.log` tidak memiliki error baru.
- `.env`, SQL, ZIP, dan backup tidak dapat diakses melalui URL.
- `APP_DEBUG=false` dan halaman error tidak membocorkan konfigurasi.

## 7. Cron queue (jika memakai database queue)

```cron
* * * * * cd /home/yogz7392/publik/app && /opt/cpanel/ea-php82/root/usr/bin/php artisan queue:work --stop-when-empty --tries=3 --timeout=60 >> /dev/null 2>&1
```

Tanpa konfigurasi mail provider, reset password tidak dapat dikirim walaupun queue
aktif. Simpan kredensial SMTP/API hanya di `.env`, bukan Git atau `public_html`.

## 8. Rollback

1. Jalankan `artisan down` pada release bermasalah.
2. Kembalikan app dan build lama dari backup.
3. Kembalikan `.env` serta storage link ke target lama.
4. Restore database hanya jika import/migration telah mengubahnya.
5. Dari app lama jalankan `optimize:clear`, `optimize`, lalu `up`.
6. Pertahankan log dan backup sampai release baru stabil.

Checklist operasional yang dapat dicentang tersedia di
`deploy/cpanel/go-live-checklist.md`.
