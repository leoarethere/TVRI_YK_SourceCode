<?php

declare(strict_types=1);

/**
 * Pemeriksa statis untuk deployment cPanel.
 *
 * Script ini sengaja tidak melakukan bootstrap Laravel sehingga aman dipakai
 * ketika proc_open dinonaktifkan oleh hosting.
 */
function fail(string $message): never
{
    fwrite(STDERR, $message.PHP_EOL);
    exit(1);
}

function canonicalPath(string $path): string
{
    $resolved = realpath($path);

    if ($resolved === false) {
        fail("Path tidak dapat di-resolve: {$path}");
    }

    return rtrim(str_replace('\\', '/', $resolved), '/');
}

function verifyIndex(string $indexPath, string $expectedAppRoot): void
{
    if (! is_file($indexPath) || ! is_readable($indexPath)) {
        fail("index.php tidak dapat dibaca: {$indexPath}");
    }

    $source = file_get_contents($indexPath);

    if ($source === false) {
        fail("Gagal membaca index.php: {$indexPath}");
    }

    $candidate = null;

    if (preg_match('/\$appRoot\s*=\s*([\'\"])(\/[^\'\"]+)\1\s*;/', $source, $matches) === 1) {
        $candidate = $matches[2];
    } elseif (preg_match('/\$appRoot\s*=\s*dirname\s*\(\s*__DIR__\s*\)\s*\.\s*([\'\"])([^\'\"]+)\1\s*;/', $source, $matches) === 1) {
        $candidate = dirname(dirname($indexPath)).$matches[2];
    } elseif (preg_match('/\$appRoot\s*=\s*__DIR__\s*\.\s*([\'\"])([^\'\"]+)\1\s*;/', $source, $matches) === 1) {
        $candidate = dirname($indexPath).$matches[2];
    }

    if ($candidate === null) {
        fail('Assignment $appRoot pada public_html/index.php tidak dikenali.');
    }

    $actualRoot = canonicalPath($candidate);
    $expectedRoot = canonicalPath($expectedAppRoot);

    if ($actualRoot !== $expectedRoot) {
        fail("public_html/index.php menunjuk {$actualRoot}, seharusnya {$expectedRoot}");
    }

    $requiredReferences = [
        '/\$appRoot\s*\.\s*([\'\"])\/vendor\/autoload\.php\1/',
        '/\$appRoot\s*\.\s*([\'\"])\/bootstrap\/app\.php\1/',
    ];

    foreach ($requiredReferences as $pattern) {
        if (preg_match($pattern, $source) !== 1) {
            fail('public_html/index.php tidak memakai $appRoot untuk autoload dan bootstrap Laravel.');
        }
    }

    echo $actualRoot.PHP_EOL;
}

function verifyManifest(string $manifestPath, array $requiredEntries = []): void
{
    if (! is_file($manifestPath) || ! is_readable($manifestPath)) {
        fail("Manifest tidak dapat dibaca: {$manifestPath}");
    }

    try {
        $manifest = json_decode(
            (string) file_get_contents($manifestPath),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    } catch (JsonException $exception) {
        fail("Manifest JSON tidak valid: {$exception->getMessage()}");
    }

    if (! is_array($manifest) || $manifest === []) {
        fail('Manifest kosong atau tidak berbentuk object JSON.');
    }

    foreach ($requiredEntries as $requiredEntry) {
        $entry = $manifest[$requiredEntry] ?? null;

        if (! is_array($entry) || ($entry['isEntry'] ?? false) !== true || ! isset($entry['file'])) {
            fail("Entry Vite wajib tidak tersedia atau bukan entry aktif: {$requiredEntry}");
        }
    }

    $buildRoot = canonicalPath(dirname($manifestPath));
    $references = [];

    foreach ($manifest as $entryName => $entry) {
        if (! is_array($entry)) {
            fail("Entry manifest tidak valid: {$entryName}");
        }

        if (isset($entry['file'])) {
            $references[] = $entry['file'];
        }

        foreach (['css', 'assets'] as $collection) {
            if (! isset($entry[$collection])) {
                continue;
            }

            if (! is_array($entry[$collection])) {
                fail("Field {$collection} pada {$entryName} harus berupa array.");
            }

            array_push($references, ...$entry[$collection]);
        }
    }

    $references = array_values(array_unique($references));

    if ($references === []) {
        fail('Manifest tidak mereferensikan berkas build apa pun.');
    }

    foreach ($references as $reference) {
        if (! is_string($reference) || $reference === '') {
            fail('Manifest memiliki referensi aset yang kosong atau bukan string.');
        }

        $normalized = str_replace('\\', '/', $reference);

        if (str_starts_with($normalized, '/') || preg_match('#(^|/)\.\.(/|$)#', $normalized) === 1) {
            fail("Referensi aset tidak aman: {$reference}");
        }

        $assetPath = realpath($buildRoot.'/'.ltrim($normalized, '/'));

        if ($assetPath === false || ! is_file($assetPath)) {
            fail("Aset yang direferensikan manifest tidak ditemukan: {$reference}");
        }

        $canonicalAsset = str_replace('\\', '/', $assetPath);

        if (! str_starts_with($canonicalAsset, $buildRoot.'/')) {
            fail("Aset keluar dari direktori build: {$reference}");
        }
    }

    echo count($references).PHP_EOL;
}

function configValue(array $config, string $path): mixed
{
    $value = $config;

    foreach (explode('.', $path) as $segment) {
        if (! is_array($value) || ! array_key_exists($segment, $value)) {
            return null;
        }

        $value = $value[$segment];
    }

    return $value;
}

function hasValidEncryptionKey(mixed $key): bool
{
    if (! is_string($key) || $key === '') {
        return false;
    }

    if (str_starts_with($key, 'base64:')) {
        $decoded = base64_decode(substr($key, 7), true);

        return $decoded !== false && strlen($decoded) === 32;
    }

    return strlen($key) === 32;
}

function verifyConfigCache(string $configPath): void
{
    if (! is_file($configPath) || ! is_readable($configPath)) {
        fail("Cache konfigurasi tidak dapat dibaca: {$configPath}");
    }

    $config = require $configPath;

    if (! is_array($config)) {
        fail('Cache konfigurasi Laravel tidak mengembalikan array.');
    }

    $checks = [
        'APP_ENV harus production' => configValue($config, 'app.env') === 'production',
        'APP_DEBUG harus false' => configValue($config, 'app.debug') === false,
        'APP_URL harus HTTPS' => str_starts_with((string) configValue($config, 'app.url'), 'https://'),
        'APP_TIMEZONE harus Asia/Jakarta' => configValue($config, 'app.timezone') === 'Asia/Jakarta',
        'APP_KEY harus valid untuk AES-256-CBC' => configValue($config, 'app.cipher') === 'AES-256-CBC'
            && hasValidEncryptionKey(configValue($config, 'app.key')),
        'DB_CONNECTION harus mysql atau mariadb' => in_array(configValue($config, 'database.default'), ['mysql', 'mariadb'], true),
        'FILESYSTEM_DISK harus public' => configValue($config, 'filesystems.default') === 'public',
        'SESSION_DRIVER harus database' => configValue($config, 'session.driver') === 'database',
        'SESSION_SECURE_COOKIE harus true' => configValue($config, 'session.secure') === true,
        'SESSION_HTTP_ONLY harus true' => configValue($config, 'session.http_only') === true,
        'SESSION_SAME_SITE harus lax atau strict' => in_array(configValue($config, 'session.same_site'), ['lax', 'strict'], true),
        'CACHE_STORE harus database' => configValue($config, 'cache.default') === 'database',
        'QUEUE_CONNECTION harus database atau sync' => in_array(configValue($config, 'queue.default'), ['database', 'sync'], true),
    ];

    $logChannel = configValue($config, 'logging.default');
    $stackChannels = configValue($config, 'logging.channels.stack.channels');
    $checks['Log production harus memakai rotasi daily'] = $logChannel === 'daily'
        || ($logChannel === 'stack' && is_array($stackChannels) && in_array('daily', $stackChannels, true));

    $mailDriver = configValue($config, 'mail.default');
    $checks['Mail production tidak boleh memakai log/array'] = is_string($mailDriver)
        && ! in_array($mailDriver, ['log', 'array'], true);

    foreach ($checks as $message => $passed) {
        if (! $passed) {
            fail($message);
        }
    }

    echo count($checks).PHP_EOL;
}

$command = $argv[1] ?? '';

switch ($command) {
    case 'index':
        verifyIndex(
            $argv[2] ?? fail('Path index.php wajib diberikan.'),
            $argv[3] ?? fail('APP_ROOT wajib diberikan.')
        );
        break;

    case 'manifest':
        verifyManifest(
            $argv[2] ?? fail('Path manifest.json wajib diberikan.'),
            array_slice($argv, 3)
        );
        break;

    case 'config':
        verifyConfigCache(
            $argv[2] ?? fail('Path bootstrap/cache/config.php wajib diberikan.')
        );
        break;

    default:
        fail('Pemakaian: verify-deployment.php index <index.php> <APP_ROOT> | manifest <manifest.json> [entry...] | config <config.php>');
}
