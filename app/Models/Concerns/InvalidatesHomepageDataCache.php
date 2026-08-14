<?php

namespace App\Models\Concerns;

use App\Support\HomepageCache;

trait InvalidatesHomepageDataCache
{
    public static function bootInvalidatesHomepageDataCache(): void
    {
        static::saved(fn () => HomepageCache::forgetData());
        static::deleted(fn () => HomepageCache::forgetData());
    }
}
