#!/usr/bin/env bash

# Pemeriksaan read-only sebelum aplikasi dibuka untuk publik.
# APP_ROOT=/home/yogz7392/publik \
# PUBLIC_ROOT=/home/yogz7392/public_html \
# PHP_BIN=/opt/cpanel/ea-php82/root/usr/bin/php \
# bash deploy/cpanel/preflight.sh

set -u

APP_ROOT="${APP_ROOT:-/home/yogz7392/publik}"
PUBLIC_ROOT="${PUBLIC_ROOT:-/home/yogz7392/public_html}"
PHP_BIN="${PHP_BIN:-/opt/cpanel/ea-php82/root/usr/bin/php}"
SCRIPT_DIR="$(CDPATH= cd -- "$(dirname -- "$0")" && pwd -P)"
VERIFY_SCRIPT="$SCRIPT_DIR/verify-deployment.php"
VITE_ENTRIES=(
    'resources/css/app.css'
    'resources/js/app.js'
    'resources/js/dashboard.js'
)
REQUIRED_PHP_EXTENSIONS=(
    ctype
    dom
    fileinfo
    filter
    gd
    mbstring
    openssl
    pdo
    pdo_mysql
    session
    tokenizer
    xml
)

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
        /*)
            return 0
            ;;
        *)
            fail "$2 harus berupa absolute path: $1"
            return 1
            ;;
    esac
}

validate_manifest() {
    label="$1"
    manifest="$2"

    if [ ! -x "$PHP_BIN" ] || [ ! -f "$VERIFY_SCRIPT" ]; then
        return
    fi

    validation_output="$("$PHP_BIN" "$VERIFY_SCRIPT" manifest "$manifest" "${VITE_ENTRIES[@]}" 2>&1)"
    validation_status=$?

    if [ "$validation_status" -eq 0 ]; then
        pass "$label valid dan seluruh ${validation_output:-0} aset tersedia"
    else
        fail "$label tidak valid: $validation_output"
    fi
}

check_safe_path "$APP_ROOT" 'APP_ROOT'
check_safe_path "$PUBLIC_ROOT" 'PUBLIC_ROOT'

if [ -x "$PHP_BIN" ]; then
    php_version="$("$PHP_BIN" -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null || true)"
    php_major="${php_version%%.*}"
    php_minor="${php_version#*.}"

    if [ -n "$php_version" ] && { [ "$php_major" -gt 8 ] || { [ "$php_major" -eq 8 ] && [ "$php_minor" -ge 2 ]; }; }; then
        pass "PHP CLI $php_version memenuhi kebutuhan aplikasi"
    else
        fail "PHP CLI harus 8.2+; terdeteksi: ${php_version:-tidak diketahui}"
    fi

    extension_output="$("$PHP_BIN" -r '$missing = array_values(array_filter(array_slice($argv, 1), static fn ($extension) => ! extension_loaded($extension))); if ($missing !== []) { fwrite(STDERR, implode(", ", $missing)); exit(1); } echo count(array_slice($argv, 1));' -- "${REQUIRED_PHP_EXTENSIONS[@]}" 2>&1)"
    extension_status=$?

    if [ "$extension_status" -eq 0 ]; then
        pass "Seluruh $extension_output ekstensi PHP produksi tersedia"
    else
        fail "Ekstensi PHP produksi belum lengkap: $extension_output"
    fi
else
    fail "PHP_BIN tidak executable: $PHP_BIN"
fi

for required_command in cmp find; do
    if command -v "$required_command" >/dev/null 2>&1; then
        pass "Command server tersedia: $required_command"
    else
        fail "Command server tidak tersedia: $required_command"
    fi
done

if [ -f "$VERIFY_SCRIPT" ]; then
    pass 'Helper verifikasi deployment tersedia'
else
    fail "Helper verifikasi tidak ditemukan: $VERIFY_SCRIPT"
fi

for required in artisan composer.json composer.lock package-lock.json bootstrap/app.php public/build/manifest.json; do
    if [ -f "$APP_ROOT/$required" ]; then
        pass "Ditemukan $APP_ROOT/$required"
    else
        fail "Tidak ditemukan $APP_ROOT/$required"
    fi
done

config_cache="$APP_ROOT/bootstrap/cache/config.php"

if [ -f "$config_cache" ]; then
    if [ -x "$PHP_BIN" ] && [ -f "$VERIFY_SCRIPT" ]; then
        config_output="$("$PHP_BIN" "$VERIFY_SCRIPT" config "$config_cache" 2>&1)"
        config_status=$?

        if [ "$config_status" -eq 0 ]; then
            pass "Cache konfigurasi mematuhi seluruh $config_output kebijakan production"
        else
            fail "Cache konfigurasi production tidak aman: $config_output"
        fi
    fi

    if [ "$APP_ROOT/.env" -nt "$config_cache" ]; then
        fail '.env lebih baru dari cache konfigurasi; jalankan optimize ulang'
    fi

    newer_config="$(find "$APP_ROOT/config" -type f -name '*.php' -newer "$config_cache" -print -quit 2>/dev/null || true)"

    if [ -n "$newer_config" ]; then
        fail "Source konfigurasi lebih baru dari config cache: $newer_config"
    else
        pass 'Cache konfigurasi lebih baru dari .env dan seluruh source config'
    fi
else
    fail 'bootstrap/cache/config.php tidak tersedia; artisan optimize belum selesai'
fi

route_cache=''

for candidate in "$APP_ROOT"/bootstrap/cache/routes-*.php; do
    if [ -f "$candidate" ]; then
        route_cache="$candidate"
        break
    fi
done

if [ -n "$route_cache" ]; then
    stale_route=''

    for route_source in "$APP_ROOT"/routes/*.php "$APP_ROOT/bootstrap/app.php"; do
        if [ -f "$route_source" ] && [ "$route_source" -nt "$route_cache" ]; then
            stale_route="$route_source"
            break
        fi
    done

    if [ -n "$stale_route" ]; then
        fail "Source route lebih baru dari route cache: $stale_route"
    else
        pass 'Route cache tersedia dan tidak stale'
    fi
else
    fail 'Route cache tidak tersedia; artisan optimize belum selesai'
fi

compiled_view_count="$(find "$APP_ROOT/storage/framework/views" -maxdepth 1 -type f -name '*.php' 2>/dev/null | wc -l | tr -d '[:space:]')"

if [ "${compiled_view_count:-0}" -gt 0 ]; then
    pass "View cache tersedia ($compiled_view_count berkas compiled)"
else
    fail 'View cache tidak tersedia; artisan optimize belum selesai'
fi

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
        fail "APP_ENV bukan production: ${app_env:-tidak diatur}"
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

    if [ -x "$PHP_BIN" ] && [ -f "$VERIFY_SCRIPT" ]; then
        index_output="$("$PHP_BIN" "$VERIFY_SCRIPT" index "$PUBLIC_ROOT/index.php" "$APP_ROOT" 2>&1)"
        index_status=$?

        if [ "$index_status" -eq 0 ]; then
            pass "public_html/index.php menunjuk APP_ROOT yang benar: $index_output"
        else
            fail "$index_output"
        fi
    fi
else
    fail 'public_html/index.php tidak ditemukan'
fi

if [ -f "$PUBLIC_ROOT/.htaccess" ]; then
    pass 'public_html/.htaccess tersedia'

    if grep -q 'ea-php74' "$PUBLIC_ROOT/.htaccess"; then
        fail '.htaccess masih memuat ea-php74; pilih PHP 8.2 di MultiPHP Manager'
    fi

    if grep -q 'vite_immutable_asset' "$PUBLIC_ROOT/.htaccess" && grep -q 'max-age=31536000' "$PUBLIC_ROOT/.htaccess"; then
        pass 'Cache immutable dibatasi pada asset Vite ber-hash'
    else
        warn '.htaccess belum mengaktifkan cache immutable khusus /build/assets/'
    fi
else
    fail 'public_html/.htaccess tidak ditemukan'
fi

release_manifest="$APP_ROOT/public/build/manifest.json"
live_manifest="$PUBLIC_ROOT/build/manifest.json"

if [ -f "$release_manifest" ]; then
    validate_manifest 'Manifest release' "$release_manifest"
fi

if [ -f "$live_manifest" ]; then
    pass 'Build frontend tersedia di public_html'
    validate_manifest 'Manifest live' "$live_manifest"
else
    fail 'public_html/build/manifest.json tidak ditemukan'
fi

if [ -f "$release_manifest" ] && [ -f "$live_manifest" ]; then
    if cmp -s "$release_manifest" "$live_manifest"; then
        pass 'Manifest release dan public_html identik'
    else
        fail 'Manifest public_html berbeda dari release; publish aset belum selesai atau manifest stale'
    fi
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
    fail 'public_html/storage adalah file/folder biasa, bukan symbolic link'
else
    fail 'public_html/storage belum tersedia'
fi

for exposed_backup in "$PUBLIC_ROOT"/*.sql "$PUBLIC_ROOT"/*.zip "$PUBLIC_ROOT"/*.tar "$PUBLIC_ROOT"/*.gz; do
    if [ -e "$exposed_backup" ]; then
        fail "Backup berpotensi dapat diunduh publik: $exposed_backup"
    fi
done

# Bootstrap minimal tanpa Artisan/Symfony Process. Ini tidak memanggil proc_open.
if [ -x "$PHP_BIN" ] && [ -f "$APP_ROOT/vendor/autoload.php" ] && [ -f "$APP_ROOT/bootstrap/app.php" ]; then
    bootstrap_output="$("$PHP_BIN" -r '$root = $argv[1]; require $root."/vendor/autoload.php"; $app = require $root."/bootstrap/app.php"; echo get_class($app);' "$APP_ROOT" 2>&1)"
    bootstrap_status=$?

    if [ "$bootstrap_status" -eq 0 ] && [ "$bootstrap_output" = 'Illuminate\Foundation\Application' ]; then
        pass 'Laravel dapat melakukan bootstrap minimal tanpa proc_open'
    else
        fail "Laravel gagal bootstrap minimal: ${bootstrap_output:-tidak ada keluaran}"
    fi
fi

printf '\nRingkasan: %d gagal, %d peringatan.\n' "$failures" "$warnings"

if [ "$failures" -gt 0 ]; then
    exit 1
fi

exit 0
