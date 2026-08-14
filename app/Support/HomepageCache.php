<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

final class HomepageCache
{
    public const DATA_KEY = 'homepage.data.v2';

    public static function scheduleKey(): string
    {
        return 'homepage.schedules.'.now()->toDateString();
    }

    public static function forgetData(): void
    {
        Cache::forget(self::DATA_KEY);
    }

    public static function forgetSchedule(): void
    {
        Cache::forget(self::scheduleKey());
    }
}
