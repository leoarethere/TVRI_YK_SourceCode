<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use RuntimeException;

final class PublicStorageWriter
{
    /**
     * Store public contents and fail explicitly when the filesystem returns false.
     */
    public static function put(string $path, string $contents): void
    {
        if (! Storage::disk('public')->put($path, $contents)) {
            throw new RuntimeException("Gagal menyimpan file ke public storage: {$path}");
        }
    }
}
