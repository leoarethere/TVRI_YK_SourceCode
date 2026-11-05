<?php

namespace App\Support;

use Carbon\Carbon;
use DateTimeInterface;

final class Time
{
    /**
     * Konversi tanggal ke WIB (Asia/Jakarta).
     *
     * Data hasil impor database lama sudah tersimpan dalam WIB. Sementara itu,
     * data yang dibuat aplikasi setelah impor hingga perbaikan timezone masih
     * tersimpan sebagai wall-clock UTC. Keduanya dibedakan lewat cutoff.
     */
    public static function toWib(DateTimeInterface|string|null $date): ?Carbon
    {
        if ($date === null || $date === '') {
            return null;
        }

        $carbon = $date instanceof DateTimeInterface
            ? Carbon::instance($date)
            : Carbon::parse($date);

        $wallClock = $carbon->format('Y-m-d H:i:s');

        $legacyWibUntil = config('app.legacy_wib_until');
        $legacyUtcUntil = config('app.legacy_utc_until');

        $legacyWibUntil = $legacyWibUntil
            ? Carbon::parse($legacyWibUntil, 'Asia/Jakarta')->format('Y-m-d H:i:s')
            : null;
        $legacyUtcUntil = $legacyUtcUntil
            ? Carbon::parse($legacyUtcUntil, 'Asia/Jakarta')->format('Y-m-d H:i:s')
            : null;

        // Data impor lama: wall-clock sudah WIB, jangan digeser.
        if ($legacyWibUntil && $wallClock < $legacyWibUntil) {
            return Carbon::parse($wallClock, 'Asia/Jakarta');
        }

        // Data baru sebelum perbaikan timezone: wall-clock masih UTC.
        if ($legacyUtcUntil && $wallClock < $legacyUtcUntil) {
            return Carbon::parse($wallClock, 'UTC')->timezone('Asia/Jakarta');
        }

        return Carbon::parse($wallClock, 'Asia/Jakarta');
    }

    /**
     * Format tanggal dalam WIB dengan format 24 jam.
     */
    public static function formatWib(DateTimeInterface|string|null $date, string $format = 'd F Y, H:i'): string
    {
        return static::toWib($date)?->translatedFormat($format) ?? '-';
    }
}
