#!/usr/bin/env bash

# Pemeriksaan read-only sebelum aplikasi dibuka untuk publik.
# APP_ROOT=/home/yogz7392/publik/app \
# PUBLIC_ROOT=/home/yogz7392/public_html \
# PHP_BIN=/opt/cpanel/ea-php82/root/usr/bin/php \
# bash deploy/cpanel/preflight.sh

set -u

APP_ROOT="${APP_ROOT:-/home/yogz7392/publik/app}"
PUBLIC_ROOT="${PUBLIC_ROOT:-/home/yogz7392/public_html}"
PHP_BIN="${PHP_BIN:-/opt/cpanel/ea-php82/root/usr/bin/php}"

failures=0
warnings=0

pass() {
    printf '[OK] %s\n' "$1"
}

warn() {
    printf '[PERINGATAN] %s\n' "$1" >&2
    warnings=$((warnings + 1))
}

fail() {
    printf '[GAGAL] %s\n' "$1" >&2
    failures=$((failures + 1))
}

check_safe_path() {
    case "$1" in
        ''|/|/home|/home/)
            fail "$2 tidak aman atau terlalu luas: $1"
            return 1
            ;;
    esac

    return 0
}

check_safe_path "$APP_ROOT" 'APP_ROOT'
check_safe_path "$PUBLIC_ROOT" 'PUBLIC_ROOT'

if [ -x "$PHP_BIN" ]; then
    php_version="$($PHP_BIN -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null || true)"
    php_major="${php_version%%.*}"
    php_minor="${php_version#*.}"

    if [ -n "$php_version" ] && { [ "$php_major" -gt 8 ] || { [ "$php_major" -eq 8 ] && [ "$php_minor" -ge 2 ]; }; }; then
        pass "PHP CLI $php_version memenuhi kebutuhan Laravel 11"
    else
        fail "PHP CLI harus 8.2+; terdeteksi: ${php_version:-tidak diketahui}"
    fi
else
    fail "PHP_BIN tidak executable: $PHP_BIN"
fi

for required in artisan composer.json bootstrap/app.php public/build/manifest.json; do
    if [ -f "$APP_ROOT/$required" ]; then
        pass "Ditemukan $APP_ROOT/$required"
    else
        fail "Tidak ditemukan $APP_ROOT/$required"
    fi
done

if [ -f "$APP_ROOT/vendor/autoload.php" ]; then
    pass 'Dependency Composer/vendor tersedia'
else
    fail 'vendor/autoload.php tidak tersedia; jalankan composer install production'
fi

if [ -f "$APP_ROOT/.env" ]; then
    pass '.env production tersedia di APP_ROOT'

    app_env="$(sed -n 's/^APP_ENV=//p' "$APP_ROOT/.env" | tail -n 1 | tr -d '\r\"')"
    app_debug="$(sed -n 's/^APP_DEBUG=//p' "$APP_ROOT/.env" | tail -n 1 | tr -d '\r\"' | tr '[:upper:]' '[:lower:]')"

    if [ "$app_env" = 'production' ]; then
        pass 'APP_ENV=production'
    else
        warn "APP_ENV bukan production: ${app_env:-tidak diatur}"
    fi

    if [ "$app_debug" = 'false' ]; then
        pass 'APP_DEBUG=false'
    else
        fail 'APP_DEBUG harus false di live server'
    fi
else
    fail '.env tidak ditemukan di APP_ROOT'
fi

for runtime_path in storage bootstrap/cache; do
    if [ -d "$APP_ROOT/$runtime_path" ] && [ -w "$APP_ROOT/$runtime_path" ]; then
        pass "$runtime_path tersedia dan writable"
    elif [ -d "$APP_ROOT/$runtime_path" ]; then
        fail "$runtime_path ada tetapi tidak writable oleh user saat ini"
    else
        fail "$runtime_path tidak ditemukan"
    fi
done

if [ -f "$PUBLIC_ROOT/index.php" ]; then
    pass 'public_html/index.php tersedia'
else
    fail 'public_html/index.php tidak ditemukan'
fi

if [ -f "$PUBLIC_ROOT/.htaccess" ]; then
    pass 'public_html/.htaccess tersedia'

    if grep -q 'ea-php74' "$PUBLIC_ROOT/.htaccess"; then
        fail '.htaccess masih memuat ea-php74; pilih PHP 8.2 di MultiPHP Manager'
    fi
else
    fail 'public_html/.htaccess tidak ditemukan'
fi

if [ -f "$PUBLIC_ROOT/build/manifest.json" ]; then
    pass 'Build frontend tersedia di public_html'
else
    fail 'public_html/build/manifest.json tidak ditemukan'
fi

expected_storage="$APP_ROOT/storage/app/public"
public_storage="$PUBLIC_ROOT/storage"

if [ -L "$public_storage" ]; then
    resolved_storage="$(readlink -f "$public_storage" 2>/dev/null || true)"
    resolved_expected="$(readlink -f "$expected_storage" 2>/dev/null || true)"

    if [ -n "$resolved_storage" ] && [ "$resolved_storage" = "$resolved_expected" ]; then
        pass "Storage link menuju $expected_storage"
    else
        fail "Storage link menuju target salah: ${resolved_storage:-tidak dapat di-resolve}"
    fi
elif [ -e "$public_storage" ]; then
    warn 'public_html/storage adalah file/folder biasa, bukan symbolic link'
else
    fail 'public_html/storage belum tersedia'
fi

for exposed_backup in "$PUBLIC_ROOT"/*.sql "$PUBLIC_ROOT"/*.zip "$PUBLIC_ROOT"/*.tar "$PUBLIC_ROOT"/*.gz; do
    if [ -e "$exposed_backup" ]; then
        fail "Backup berpotensi dapat diunduh publik: $exposed_backup"
    fi
done

if [ -x "$PHP_BIN" ] && [ -f "$APP_ROOT/artisan" ]; then
    if (cd "$APP_ROOT" && "$PHP_BIN" artisan about --only=environment >/dev/null 2>&1); then
        pass 'Laravel dapat melakukan bootstrap'
    else
        fail 'Laravel gagal bootstrap; periksa log tanpa membagikan secret'
    fi
fi

printf '\nRingkasan: %d gagal, %d peringatan.\n' "$failures" "$warnings"

if [ "$failures" -gt 0 ]; then
    exit 1
fi

exit 0
