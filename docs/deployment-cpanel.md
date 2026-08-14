# Deployment Laravel ke cPanel TVRI Yogyakarta

Panduan ini ditujukan untuk project Laravel 12 TVRI Yogyakarta. Targetnya adalah
deployment production yang dapat dipulihkan, tidak membocorkan `.env`, dan tetap
memisahkan source Laravel dari web root.

## 1. Struktur folder

```text
/home/yogz7392/
├── publik/                     # APP_ROOT: project Laravel lengkap
│   ├── app/
│   ├── bootstrap/
│   ├── public/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   └── artisan
└── public_html/                # PUBLIC_ROOT: hanya berkas dari app/public
    ├── build/
    ├── storage -> ../publik/storage/app/public
    ├── index.php
    └── .htaccess
```

Folder `storage` Laravel tidak dipindahkan ke `public_html`. Data asli tetap di
`publik/storage`. `public_html/storage` hanya symbolic link menuju
`publik/storage/app/public`.

Arsip server sebelumnya memperlihatkan kemungkinan susunan `publik/publik`.
Sebelum deployment, cari lokasi sebenarnya dari `artisan`:

```bash
find /home/yogz7392/publik -maxdepth 3 -name artisan -type f
```

Hasil server live yang dipakai panduan ini harus
`/home/yogz7392/publik/artisan`. Jika berbeda, hentikan deployment dan pastikan
struktur release terlebih dahulu; jangan menambah folder `app` atau `publik`
berdasarkan tebakan.

## 2. Jalur dan versi PHP

Definisikan variabel berikut setiap membuka sesi Terminal cPanel:

```bash
APP_ROOT=/home/yogz7392/publik
PUBLIC_ROOT=/home/yogz7392/public_html
PHP_BIN=/opt/cpanel/ea-php82/root/usr/bin/php
"$PHP_BIN" -v
```

Laravel 12 memerlukan PHP 8.2+. Atur domain `yogyakarta.tvri.go.id` ke PHP 8.2
melalui **cPanel > MultiPHP Manager**. Jangan mempertahankan handler `ea-php74`
di `.htaccess`.

## 3. Persiapan sebelum upload

1. Jadwalkan maintenance window.
2. Backup database live melalui cPanel Backup/phpMyAdmin atau `mysqldump`.
3. Backup `.env`, app lama, dan seluruh `storage/app/public`.
4. Build aset dan tes di komputer lokal:

   ```powershell
   node --version
   npm.cmd ci
   npm.cmd run build
   php artisan test
   ```

   Vite 7 memerlukan Node.js minimal 20.19 atau 22.12. Gunakan `npm ci`, bukan
   `npm install`, agar versi dependency persis mengikuti `package-lock.json`.

5. Pastikan `composer.lock`, `package-lock.json`, dan
   `public/build/manifest.json` ada di paket release.
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
set -Ee -o pipefail
cd "$APP_ROOT"
MAINTENANCE_ACTIVE=0
release_maintenance() {
    STATUS=$?
    trap - EXIT HUP INT TERM
    if [ "$MAINTENANCE_ACTIVE" -eq 1 ]; then
        "$PHP_BIN" "$APP_ROOT/artisan" up || true
    fi
    exit "$STATUS"
}
trap release_maintenance EXIT
trap 'exit 129' HUP
trap 'exit 130' INT
trap 'exit 143' TERM
"$PHP_BIN" artisan down --retry=60
MAINTENANCE_ACTIVE=1
```

Biarkan trap tersebut aktif selama deployment. Jika perintah gagal atau sesi
terputus dengan signal, shell tetap mencoba menjalankan `artisan up`. Script
`optimize-laravel.sh` pada langkah 4.8 juga memiliki trap internal yang sama.

Upload release ZIP ke `/home/yogz7392/publik/`, ekstrak, lalu pastikan `artisan`,
`composer.json`, `app`, `bootstrap`, `public`, `resources`, dan `routes` berada
langsung di `APP_ROOT`. Lokasi akhirnya harus tepat
`/home/yogz7392/publik/artisan`; jangan sampai terbentuk
`/publik/Yogyakarta/...`, `/publik/app/...`, atau `/publik/publik/...`.

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
LOG_CHANNEL=daily
LOG_DAILY_DAYS=14
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
```

Simpan `APP_KEY`, database, dan kredensial mail hanya di `.env` server. Jangan
menjalankan `key:generate` jika app lama sudah memiliki `APP_KEY`; data
terenkripsi/cookie lama dapat menjadi tidak valid. Jika tidak ada queue worker,
gunakan `QUEUE_CONNECTION=sync`.

`optimize-laravel.sh` berhenti sebelum maintenance jika `APP_KEY`, database,
mail, timezone, cookie HTTPS, filesystem, atau rotasi log masih kosong/tidak aman.
Nilai placeholder dari template tidak pernah dianggap sebagai konfigurasi live.

### 4.4 Database

Upload dump ke folder private, misalnya `/home/yogz7392/backups/import.sql`:

```bash
mysql -u NAMA_USER_DB -p NAMA_DATABASE < /home/yogz7392/backups/import.sql
cd "$APP_ROOT"
"$PHP_BIN" artisan migrate --force
"$PHP_BIN" artisan broadcasts:audit-categories
```

Command audit tersebut bersifat read-only dan wajib dijalankan setelah migration.
Jika menemukan relasi kategori legacy yang hilang atau ambigu, hentikan go-live
dan petakan datanya secara manual sebelum aplikasi dibuka kembali.

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

Publikasikan isi `APP_ROOT/public` dengan script project. Script ini
mempertahankan `index.php`, `.htaccess`, dan symlink `storage` khusus cPanel,
memvalidasi seluruh file yang dirujuk manifest, serta tidak menghapus asset hash
lama:

```bash
cd "$APP_ROOT"
APP_ROOT="$APP_ROOT" PUBLIC_ROOT="$PUBLIC_ROOT" PHP_BIN="$PHP_BIN" \
    BACKUP_DIR="/home/yogz7392/backups/$STAMP" \
    bash deploy/cpanel/publish-public.sh
```

Jangan memindahkan atau menghapus folder `public_html/build` sebelum publish.
`publish-public.sh` menyalin asset baru dengan mode merge tanpa `--delete`, lalu
mengganti `manifest.json` paling akhir menggunakan rename atomik. Dengan begitu,
HTML atau cache browser yang masih meminta hash lama tetap dapat dilayani dan
manifest baru tidak pernah aktif sebelum seluruh asset barunya tersedia.
Manifest live sebelumnya juga disimpan sebagai
`/home/yogz7392/backups/$STAMP/build-manifest.json`. Jika publish terputus setelah
manifest baru aktif, script memulihkan manifest lama secara otomatis.

Jika harus memakai File Manager, terapkan urutan yang sama: upload seluruh file
`build/assets` baru ke folder live tanpa menghapus isi lama, verifikasi nama dan
ukuran file, lalu upload `manifest.json` terakhir. Jangan mengekstrak arsip build
lama setelah langkah ini karena manifest lama akan mengaktifkan kembali UI lama.

Gunakan `deploy/cpanel/public-index.php.example` sebagai `public_html/index.php`
dan sesuaikan hanya `$appRoot`. Gunakan `public-htaccess.example` untuk rewrite.
Template `.htaccess` juga mengaktifkan kompresi bila modul Apache tersedia dan
cache immutable hanya untuk asset Vite ber-hash; file statis non-hash tidak
dipaksa immutable. PHP domain tetap dipilih melalui MultiPHP Manager.

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
APP_ROOT="$APP_ROOT" PUBLIC_ROOT="$PUBLIC_ROOT" PHP_BIN="$PHP_BIN" \
    RUN_MIGRATIONS=0 bash optimize-laravel.sh
OPTIMIZE_STATUS=$?
if [ "$OPTIMIZE_STATUS" -ne 0 ]; then
    exit "$OPTIMIZE_STATUS"
fi
MAINTENANCE_ACTIVE=0
trap - EXIT HUP INT TERM
```

Gunakan `RUN_MIGRATIONS=1` hanya jika migration belum dijalankan pada langkah
4.4 dan backup database sudah terverifikasi. Dalam mode tersebut, audit kategori
penyiaran dijalankan tepat setelah migration. Script menggunakan `PHP_BIN` cPanel
untuk semua Artisan, membuat runtime group-writable, memastikan storage link
berada di `public_html`, lalu membangun cache. Maintenance mode selalu dilepas
oleh trap, termasuk saat migration, audit, atau cache gagal. Jika symlink lama salah,
periksa targetnya dahulu; gunakan `REPLACE_STORAGE_LINK=1` hanya setelah yakin
symlink tersebut aman diganti. Folder biasa tidak pernah dihapus otomatis.

Script membersihkan cache file lebih dahulu, menjalankan migration, lalu baru
membersihkan cache database. Urutan ini mencegah fresh install gagal hanya karena
tabel `cache` belum dibuat. `artisan optimize` kemudian membuat cache konfigurasi,
event, route, dan view di
`bootstrap/cache`/`storage`. Tidak ada perintah bernama `bootstrap:cache`.

## 5. Deployment manual melalui File Manager

1. Aktifkan **Show Hidden Files**.
2. Buat `/home/yogz7392/backups/TANGGAL-JAM`.
3. Salin `.env`, `storage/app/public`, dan app lama ke backup.
4. Upload release ZIP ke `/home/yogz7392/publik`, bukan `public_html`.
5. Extract dan pastikan `artisan` langsung berada di
   `/home/yogz7392/publik/artisan`.
6. Salin `.env` production dan media backup ke app baru.
7. Jika server tidak punya Composer, paket harus memuat folder `vendor` hasil
   `composer install --no-dev --optimize-autoloader`.
8. Salin isi `public` ke `public_html`; gunakan template `index.php` cPanel dan
   jangan menimpa `storage` tanpa backup. Untuk build, upload semua asset hash
   baru tanpa menghapus hash lama, lalu ganti `manifest.json` paling akhir.
9. Dengan **Change Permissions**, buat `storage` dan `bootstrap/cache` writable
   (`775` jika didukung), termasuk subfolder. Jangan gunakan `777`.
10. Import SQL lewat phpMyAdmin, lalu jalankan migration/cache lewat Terminal.

File Manager biasanya tidak dapat membuat symlink secara andal. Gunakan satu
perintah Terminal `ln -s`, pertahankan symlink lama yang benar, atau minta support
hosting. Folder media hasil copy bukan pengganti permanen: upload baru ditulis ke
`APP_ROOT/storage/app/public` dan tidak otomatis tersalin ke `public_html`.

File Manager juga tidak menggantikan Artisan. Jalankan:

```bash
cd /home/yogz7392/publik
APP_ROOT=/home/yogz7392/publik \
PUBLIC_ROOT=/home/yogz7392/public_html \
PHP_BIN=/opt/cpanel/ea-php82/root/usr/bin/php \
RUN_MIGRATIONS=1 bash optimize-laravel.sh
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
- Preflight memastikan cache config/route/view tidak stale dan seluruh entry Vite tersedia.
- Cookie sesi hanya terkirim melalui HTTPS dan log memakai rotasi harian.
- `storage/logs/laravel.log` tidak memiliki error baru.
- `.env`, SQL, ZIP, dan backup tidak dapat diakses melalui URL.
- `APP_DEBUG=false` dan halaman error tidak membocorkan konfigurasi.

## 7. Cron queue (jika memakai database queue)

```cron
* * * * * cd /home/yogz7392/publik && /opt/cpanel/ea-php82/root/usr/bin/php artisan queue:work --stop-when-empty --tries=3 --timeout=60 >> /dev/null 2>&1
```

Tanpa konfigurasi mail provider, reset password tidak dapat dikirim walaupun queue
aktif. Simpan kredensial SMTP/API hanya di `.env`, bukan Git atau `public_html`.

## 8. Rollback

1. Jalankan `artisan down` pada release bermasalah.
2. Kembalikan app lama dan `build-manifest.json` dari backup; asset hash lama
   tetap tersedia karena proses publish tidak menghapusnya.
3. Kembalikan `.env` serta storage link ke target lama.
4. Restore database hanya jika import/migration telah mengubahnya.
5. Dari app lama jalankan `optimize:clear`, `optimize`, lalu `up`.
6. Pertahankan log dan backup sampai release baru stabil.

Checklist operasional yang dapat dicentang tersedia di
`deploy/cpanel/go-live-checklist.md`.
