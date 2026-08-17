<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    private const HOMEPAGE_CACHE_KEY = 'homepage.slides';

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'title',
        'subtitle',
        'link',
        'button_text',
        'image_path',
        'order',
        'is_active',
    ];

    /**
     * Tipe data asli untuk atribut.
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Model events untuk membersihkan cache secara otomatis.
     */
    protected static function booted(): void
    {
        static::saved(function (): void {
            Cache::forget(self::HOMEPAGE_CACHE_KEY);
        });

        static::deleted(function (Banner $banner): void {
            Cache::forget(self::HOMEPAGE_CACHE_KEY);

            if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
                Storage::disk('public')->delete($banner->image_path);
            }
        });
    }
}
