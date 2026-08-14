<?php

namespace App\Models\Concerns;

use App\Support\HomepageCache;

trait InvalidatesHomepageScheduleCache
{
    public static function bootInvalidatesHomepageScheduleCache(): void
    {
        static::saved(fn () => HomepageCache::forgetSchedule());
        static::deleted(fn () => HomepageCache::forgetSchedule());
    }
}
