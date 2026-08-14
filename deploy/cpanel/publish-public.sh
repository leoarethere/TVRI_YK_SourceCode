#!/usr/bin/env bash

# Publikasikan public/ ke public_html tanpa menghapus hash build lama.
# Manifest diganti paling akhir dengan rename pada filesystem yang sama.

set -Eeuo pipefail

APP_ROOT="${APP_ROOT:-/home/yogz7392/publik}"
PUBLIC_ROOT="${PUBLIC_ROOT:-/home/yogz7392/public_html}"
PHP_BIN="${PHP_BIN:-/opt/cpanel/ea-php82/root/usr/bin/php}"
SCRIPT_DIR="$(CDPATH= cd -- "$(dirname -- "$0")" && pwd -P)"
VERIFY_SCRIPT="$SCRIPT_DIR/verify-deployment.php"
SOURCE_PUBLIC="$APP_ROOT/public"
SOURCE_BUILD="$SOURCE_PUBLIC/build"
LIVE_BUILD="$PUBLIC_ROOT/build"
SOURCE_MANIFEST="$SOURCE_BUILD/manifest.json"
TEMP_MANIFEST="$LIVE_BUILD/.manifest.$$.json"
BACKUP_DIR="${BACKUP_DIR:-$(dirname -- "$APP_ROOT")/backups/publish-$(date +%Y%m%d-%H%M%S)}"
PREVIOUS_MANIFEST="$BACKUP_DIR/build-manifest.json"
VITE_ENTRIES=(
    'resources/css/app.css'
    'resources/js/app.js'
    'resources/js/dashboard.js'
)
manifest_activated=0
publish_succeeded=0

cleanup() {
    status=$?
    rm -f -- "$TEMP_MANIFEST"

    if [ "$manifest_activated" -eq 1 ] && [ "$publish_succeeded" -eq 0 ] && [ -f "$PREVIOUS_MANIFEST" ]; then
        rollback_manifest="$LIVE_BUILD/.manifest.rollback.$$.json"

        if cp -- "$PREVIOUS_MANIFEST" "$rollback_manifest" && mv -f -- "$rollback_manifest" "$LIVE_BUILD/manifest.json"; then
            printf '[ROLLBACK] Manifest live lama dipulihkan dari %s.\n' "$PREVIOUS_MANIFEST" >&2
        else
            rm -f -- "$rollback_manifest"
            printf '[GAGAL] Manifest lama tidak dapat dipulihkan otomatis dari %s.\n' "$PREVIOUS_MANIFEST" >&2
        fi
    fi

    return "$status"
}

trap cleanup EXIT
trap 'exit 129' HUP
trap 'exit 130' INT
trap 'exit 143' TERM

check_safe_path() {
    case "$1" in
        ''|/|/home|/home/)
            printf '[GAGAL] %s tidak aman atau terlalu luas: %s\n' "$2" "$1" >&2
            exit 1
            ;;
        /*)
            ;;
        *)
            printf '[GAGAL] %s harus berupa absolute path: %s\n' "$2" "$1" >&2
            exit 1
            ;;
    esac
}

check_safe_path "$APP_ROOT" 'APP_ROOT'
check_safe_path "$PUBLIC_ROOT" 'PUBLIC_ROOT'
check_safe_path "$BACKUP_DIR" 'BACKUP_DIR'

for command_name in cp cmp; do
    if ! command -v "$command_name" >/dev/null 2>&1; then
        printf '[GAGAL] Perintah %s tidak tersedia di server.\n' "$command_name" >&2
        exit 1
    fi
done

if [ ! -x "$PHP_BIN" ]; then
    printf '[GAGAL] PHP_BIN tidak executable: %s\n' "$PHP_BIN" >&2
    exit 1
fi

if [ ! -f "$VERIFY_SCRIPT" ]; then
    printf '[GAGAL] Helper verifikasi tidak ditemukan: %s\n' "$VERIFY_SCRIPT" >&2
    exit 1
fi

if [ ! -d "$SOURCE_PUBLIC" ] || [ ! -f "$SOURCE_MANIFEST" ]; then
    printf '[GAGAL] public/ atau manifest release tidak ditemukan di %s.\n' "$APP_ROOT" >&2
    exit 1
fi

if [ ! -d "$PUBLIC_ROOT" ] || [ ! -w "$PUBLIC_ROOT" ]; then
    printf '[GAGAL] PUBLIC_ROOT tidak tersedia atau tidak writable: %s\n' "$PUBLIC_ROOT" >&2
    exit 1
fi

resolved_app_root="$(CDPATH= cd -- "$APP_ROOT" && pwd -P)"
resolved_public_root="$(CDPATH= cd -- "$PUBLIC_ROOT" && pwd -P)"

case "$resolved_public_root/" in
    "$resolved_app_root/"|"$resolved_app_root/"*)
        printf '[GAGAL] PUBLIC_ROOT tidak boleh sama dengan atau berada di dalam APP_ROOT.\n' >&2
        exit 1
        ;;
esac

case "$resolved_app_root/" in
    "$resolved_public_root/"*)
        printf '[GAGAL] APP_ROOT tidak boleh berada di dalam PUBLIC_ROOT.\n' >&2
        exit 1
        ;;
esac

asset_count="$("$PHP_BIN" "$VERIFY_SCRIPT" manifest "$SOURCE_MANIFEST" "${VITE_ENTRIES[@]}")"
printf '[OK] Manifest release valid; %s aset ditemukan.\n' "$asset_count"

mkdir -p -- "$LIVE_BUILD"
mkdir -p -- "$BACKUP_DIR"

resolved_backup_dir="$(CDPATH= cd -- "$BACKUP_DIR" && pwd -P)"

case "$resolved_backup_dir/" in
    "$resolved_public_root/"*)
        printf '[GAGAL] BACKUP_DIR tidak boleh berada di dalam public_html.\n' >&2
        exit 1
        ;;
esac

if [ -f "$LIVE_BUILD/manifest.json" ]; then
    if "$PHP_BIN" "$VERIFY_SCRIPT" manifest "$LIVE_BUILD/manifest.json" "${VITE_ENTRIES[@]}" >/dev/null 2>&1; then
        cp -- "$LIVE_BUILD/manifest.json" "$PREVIOUS_MANIFEST"
        chmod 0600 "$PREVIOUS_MANIFEST"
        printf '[OK] Manifest live lama dibackup ke %s.\n' "$PREVIOUS_MANIFEST"
    else
        printf '[PERINGATAN] Manifest live lama tidak valid dan tidak dipakai sebagai rollback.\n' >&2
    fi
fi

# Sinkronkan public non-build tanpa menyentuh entrypoint dan storage cPanel.
if command -v rsync >/dev/null 2>&1; then
    rsync -a \
        --exclude='index.php' \
        --exclude='.htaccess' \
        --exclude='.env' \
        --exclude='storage/' \
        --exclude='build/' \
        --exclude='*.sql' \
        --exclude='*.zip' \
        --exclude='*.tar' \
        --exclude='*.gz' \
        "$SOURCE_PUBLIC/" "$PUBLIC_ROOT/"

    # Tanpa --delete: asset hash lama dipertahankan untuk HTML/cache yang belum segar.
    # Manifest dikecualikan agar tidak aktif sebelum seluruh aset selesai disalin.
    rsync -a --exclude='manifest.json' "$SOURCE_BUILD/" "$LIVE_BUILD/"
else
    printf '[PERINGATAN] rsync tidak tersedia; memakai fallback cp -a tanpa delete.\n' >&2

    for source_item in "$SOURCE_PUBLIC"/* "$SOURCE_PUBLIC"/.[!.]* "$SOURCE_PUBLIC"/..?*; do
        [ -e "$source_item" ] || [ -L "$source_item" ] || continue
        source_name="$(basename -- "$source_item")"

        case "$source_name" in
            index.php|.htaccess|.env|storage|build|*.sql|*.zip|*.tar|*.gz)
                continue
                ;;
        esac

        cp -a -- "$source_item" "$PUBLIC_ROOT/"
    done

    for build_item in "$SOURCE_BUILD"/* "$SOURCE_BUILD"/.[!.]* "$SOURCE_BUILD"/..?*; do
        [ -e "$build_item" ] || [ -L "$build_item" ] || continue
        build_name="$(basename -- "$build_item")"

        if [ "$build_name" = 'manifest.json' ]; then
            continue
        fi

        cp -a -- "$build_item" "$LIVE_BUILD/"
    done
fi

cp -- "$SOURCE_MANIFEST" "$TEMP_MANIFEST"
chmod 0644 "$TEMP_MANIFEST"

# Validasi terhadap folder live sebelum manifest diaktifkan.
live_asset_count="$("$PHP_BIN" "$VERIFY_SCRIPT" manifest "$TEMP_MANIFEST" "${VITE_ENTRIES[@]}")"

# Rename dalam direktori yang sama bersifat atomik pada filesystem server.
mv -f -- "$TEMP_MANIFEST" "$LIVE_BUILD/manifest.json"
manifest_activated=1

if ! cmp -s "$SOURCE_MANIFEST" "$LIVE_BUILD/manifest.json"; then
    printf '[GAGAL] Manifest live berbeda setelah publish.\n' >&2
    exit 1
fi

publish_succeeded=1

printf '[OK] Public terpublikasi; manifest diganti terakhir dan %s aset live tervalidasi.\n' "$live_asset_count"
printf '[INFO] Asset hash lama sengaja dipertahankan; hapus hanya setelah masa cache berakhir.\n'
