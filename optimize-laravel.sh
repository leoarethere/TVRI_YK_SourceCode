#!/usr/bin/env bash

# Optimasi release Laravel untuk struktur cPanel TVRI Yogyakarta.
# Semua perintah Artisan menggunakan PHP CLI cPanel yang ditentukan eksplisit.

set -Eeuo pipefail

APP_ROOT="${APP_ROOT:-/home/yogz7392/publik}"
PUBLIC_ROOT="${PUBLIC_ROOT:-/home/yogz7392/public_html}"
PHP_BIN="${PHP_BIN:-/opt/cpanel/ea-php82/root/usr/bin/php}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-0}"
RESTART_QUEUE="${RESTART_QUEUE:-1}"
REPLACE_STORAGE_LINK="${REPLACE_STORAGE_LINK:-0}"
VERIFY_SCRIPT="$APP_ROOT/deploy/cpanel/verify-deployment.php"

maintenance_owned=0

log() {
    printf '[%s] %s\n' "$1" "$2"
}

fail() {
    log 'GAGAL' "$1" >&2
    exit 1
}

check_safe_path() {
    case "$1" in
        ''|/|/home|/home/)
            fail "$2 tidak aman atau terlalu luas: $1"
            ;;
        /*)
            ;;
        *)
            fail "$2 harus berupa absolute path: $1"
            ;;
    esac
}

artisan() {
    (cd "$APP_ROOT" && "$PHP_BIN" artisan "$@")
}

env_value() {
    key="$1"
    sed -n "s/^${key}=//p" "$APP_ROOT/.env" | tail -n 1 | tr -d '\r\"'
}

release_maintenance() {
    status=$?
    trap - EXIT HUP INT TERM

    if [ "$maintenance_owned" -eq 1 ]; then
        log 'INFO' 'Melepas maintenance mode...'

        if ! artisan up; then
            log 'GAGAL' 'Maintenance mode tidak dapat dilepas; jalankan PHP_BIN artisan up secara manual.' >&2

            if [ "$status" -eq 0 ]; then
                status=1
            fi
        fi
    fi

    exit "$status"
}

trap release_maintenance EXIT
trap 'exit 129' HUP
trap 'exit 130' INT
trap 'exit 143' TERM

check_safe_path "$APP_ROOT" 'APP_ROOT'
check_safe_path "$PUBLIC_ROOT" 'PUBLIC_ROOT'

case "$RUN_MIGRATIONS" in
    0|1) ;;
    *) fail 'RUN_MIGRATIONS harus 0 atau 1.' ;;
esac

case "$RESTART_QUEUE" in
    0|1) ;;
    *) fail 'RESTART_QUEUE harus 0 atau 1.' ;;
esac

case "$REPLACE_STORAGE_LINK" in
    0|1) ;;
    *) fail 'REPLACE_STORAGE_LINK harus 0 atau 1.' ;;
esac

if [ ! -x "$PHP_BIN" ]; then
    fail "PHP_BIN tidak executable: $PHP_BIN"
fi

php_version_id="$("$PHP_BIN" -r 'echo PHP_VERSION_ID;' 2>/dev/null || true)"

if [ -z "$php_version_id" ] || [ "$php_version_id" -lt 80200 ]; then
    fail "PHP CLI harus 8.2+; PHP_VERSION_ID=${php_version_id:-tidak diketahui}"
fi

for required in artisan composer.json composer.lock bootstrap/app.php vendor/autoload.php deploy/cpanel/verify-deployment.php .env; do
    if [ ! -f "$APP_ROOT/$required" ]; then
        fail "Berkas wajib tidak ditemukan: $APP_ROOT/$required"
    fi
done

if [ ! -d "$PUBLIC_ROOT" ] || [ ! -w "$PUBLIC_ROOT" ]; then
    fail "PUBLIC_ROOT tidak tersedia atau tidak writable: $PUBLIC_ROOT"
fi

app_key="$(env_value APP_KEY)"

if [ -z "$app_key" ]; then
    fail 'APP_KEY production kosong. Script tidak akan membuat key baru secara otomatis.'
fi

app_env="$(env_value APP_ENV)"
app_debug="$(env_value APP_DEBUG | tr '[:upper:]' '[:lower:]')"
app_url="$(env_value APP_URL)"
app_timezone="$(env_value APP_TIMEZONE)"
db_connection="$(env_value DB_CONNECTION)"
db_database="$(env_value DB_DATABASE)"
db_username="$(env_value DB_USERNAME)"
db_password="$(env_value DB_PASSWORD)"
filesystem_disk="$(env_value FILESYSTEM_DISK)"
session_secure="$(env_value SESSION_SECURE_COOKIE | tr '[:upper:]' '[:lower:]')"
log_channel="$(env_value LOG_CHANNEL)"
log_stack="$(env_value LOG_STACK)"
mail_mailer="$(env_value MAIL_MAILER)"
mail_host="$(env_value MAIL_HOST)"
mail_from="$(env_value MAIL_FROM_ADDRESS)"

[ "$app_env" = 'production' ] || fail 'APP_ENV harus production sebelum optimasi live.'
[ "$app_debug" = 'false' ] || fail 'APP_DEBUG harus false sebelum optimasi live.'
[ "$app_timezone" = 'Asia/Jakarta' ] || fail 'APP_TIMEZONE production harus Asia/Jakarta.'

case "$app_key" in
    BASE64_KEY_DARI_ENV_LIVE_LAMA|BASE64_KEY_*|CHANGE_ME*|changeme*)
        fail 'APP_KEY masih berupa placeholder; pertahankan key live yang valid.'
        ;;
esac

if ! printf '%s' "$app_key" | "$PHP_BIN" -r '$key = stream_get_contents(STDIN); if (str_starts_with($key, "base64:")) { $key = base64_decode(substr($key, 7), true); } exit(is_string($key) && strlen($key) === 32 ? 0 : 1);'; then
    fail 'APP_KEY tidak valid untuk cipher AES-256-CBC; jangan generate ulang key live.'
fi

case "$app_url" in
    https://*) ;;
    *) fail 'APP_URL production harus menggunakan HTTPS.' ;;
esac

case "$db_connection" in
    mysql|mariadb) ;;
    *) fail 'DB_CONNECTION production harus mysql atau mariadb.' ;;
esac

case "$db_database" in
    ''|NAMA_DATABASE*) fail 'DB_DATABASE belum diisi dengan database production.' ;;
esac

case "$db_username" in
    ''|USER_DATABASE*) fail 'DB_USERNAME belum diisi dengan user production.' ;;
esac

case "$db_password" in
    ''|PASSWORD_DATABASE*) fail 'DB_PASSWORD production kosong atau masih placeholder.' ;;
esac

[ "$filesystem_disk" = 'public' ] || fail 'FILESYSTEM_DISK production harus public.'
[ "$session_secure" = 'true' ] || fail 'SESSION_SECURE_COOKIE production harus true.'

if [ "$log_channel" != 'daily' ]; then
    case ",$log_stack," in
        *,daily,*) ;;
        *) fail 'Logging production harus memakai channel daily agar log berotasi.' ;;
    esac
fi

case "$mail_mailer" in
    log|array|'') fail 'MAIL_MAILER production harus memakai transport pengiriman nyata.' ;;
esac

if [ "$mail_mailer" = 'smtp' ]; then
    case "$mail_host" in
        ''|HOST_MAIL_PROVIDER*) fail 'MAIL_HOST belum diisi dengan mail provider production.' ;;
    esac
fi

case "$mail_from" in
    *@*.*)
        case "$mail_from" in
            *DOMAIN_YANG_TERVERIFIKASI*) fail 'MAIL_FROM_ADDRESS masih berupa placeholder.' ;;
        esac
        ;;
    *) fail 'MAIL_FROM_ADDRESS production tidak valid.' ;;
esac

extension_output="$("$PHP_BIN" -r '$required = ["gd", "pdo_mysql"]; $missing = array_values(array_filter($required, static fn ($extension) => ! extension_loaded($extension))); if ($missing !== []) { fwrite(STDERR, implode(", ", $missing)); exit(1); }' 2>&1)" || fail "Ekstensi PHP production belum lengkap: $extension_output"

log 'INFO' "APP_ROOT=$APP_ROOT"
log 'INFO' "PUBLIC_ROOT=$PUBLIC_ROOT"
log 'INFO' "PHP_BIN=$PHP_BIN"

log 'INFO' 'Mengaktifkan maintenance mode...'
artisan down --retry=60
maintenance_owned=1

umask 0002
mkdir -p \
    "$APP_ROOT/storage/app/public" \
    "$APP_ROOT/storage/framework/cache" \
    "$APP_ROOT/storage/framework/sessions" \
    "$APP_ROOT/storage/framework/views" \
    "$APP_ROOT/storage/logs" \
    "$APP_ROOT/bootstrap/cache"

# Runtime harus writable oleh user dan group web server; jangan gunakan 777.
chmod -R ug+rwX "$APP_ROOT/storage" "$APP_ROOT/bootstrap/cache"
log 'OK' 'Permission storage dan bootstrap/cache dibuat group-writable.'

storage_target="$APP_ROOT/storage/app/public"
storage_link="$PUBLIC_ROOT/storage"
resolved_target="$(readlink -f "$storage_target")"

if [ -L "$storage_link" ]; then
    resolved_link="$(readlink -f "$storage_link" 2>/dev/null || true)"

    if [ "$resolved_link" = "$resolved_target" ]; then
        log 'OK' "Storage link sudah benar: $storage_link -> $resolved_target"
    elif [ "$REPLACE_STORAGE_LINK" -eq 1 ]; then
        unlink "$storage_link"
        ln -s "$storage_target" "$storage_link"
        log 'OK' "Storage link yang salah diganti: $storage_link -> $storage_target"
    else
        fail "Storage link menuju ${resolved_link:-target tidak valid}. Periksa dahulu, lalu ulangi dengan REPLACE_STORAGE_LINK=1 jika aman."
    fi
elif [ -e "$storage_link" ]; then
    fail "$storage_link adalah file/folder biasa. Backup dan pindahkan secara manual sebelum membuat symlink."
else
    ln -s "$storage_target" "$storage_link"
    log 'OK' "Storage link dibuat: $storage_link -> $storage_target"
fi

log 'INFO' 'Membersihkan cache file release lama tanpa mengakses cache database...'
artisan config:clear
artisan route:clear
artisan view:clear
artisan event:clear
artisan clear-compiled

if [ "$RUN_MIGRATIONS" -eq 1 ]; then
    log 'INFO' 'Menjalankan migration production...'
    artisan migrate --force
    log 'INFO' 'Mengaudit relasi kategori penyiaran legacy...'
    artisan broadcasts:audit-categories
else
    log 'INFO' 'Migration dilewati. Gunakan RUN_MIGRATIONS=1 setelah backup database tersedia.'
fi

# cache:clear dijalankan setelah migration agar fresh install tidak gagal karena
# tabel cache database belum dibuat.
artisan cache:clear

log 'INFO' 'Membangun cache production...'
artisan optimize

if config_check="$("$PHP_BIN" "$VERIFY_SCRIPT" config "$APP_ROOT/bootstrap/cache/config.php" 2>&1)"; then
    log 'OK' "Cache konfigurasi lolos $config_check kebijakan production."
else
    fail "Cache konfigurasi production tidak aman: $config_check"
fi

if [ "$RESTART_QUEUE" -eq 1 ]; then
    artisan queue:restart
    log 'OK' 'Sinyal restart queue berhasil dikirim.'
fi

log 'INFO' 'Melepas maintenance mode...'
artisan up
maintenance_owned=0

log 'OK' 'Optimasi Laravel selesai. Jalankan deploy/cpanel/preflight.sh untuk verifikasi akhir.'
