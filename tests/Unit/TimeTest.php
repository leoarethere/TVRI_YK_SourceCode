<?php

namespace Tests\Unit;

use App\Support\Time;
use Carbon\Carbon;
use Tests\TestCase;

class TimeTest extends TestCase
{
    public function test_legacy_imported_timestamp_is_already_wib(): void
    {
        $legacy = Carbon::parse('2026-08-09 19:11:52', 'Asia/Jakarta');

        $wib = Time::toWib($legacy);

        $this->assertSame('2026-08-09 19:11:52', $wib->format('Y-m-d H:i:s'));
        $this->assertSame('Asia/Jakarta', $wib->getTimezone()->getName());
    }

    public function test_utc_era_timestamp_is_converted_to_wib(): void
    {
        $legacy = Carbon::parse('2026-08-14 08:11:24', 'Asia/Jakarta');

        $wib = Time::toWib($legacy);

        $this->assertSame('2026-08-14 15:11:24', $wib->format('Y-m-d H:i:s'));
        $this->assertSame('Asia/Jakarta', $wib->getTimezone()->getName());
    }

    public function test_timestamp_after_cutoff_is_not_shifted(): void
    {
        $recent = Carbon::parse('2026-08-18 14:35:06', 'Asia/Jakarta');

        $wib = Time::toWib($recent);

        $this->assertSame('2026-08-18 14:35:06', $wib->format('Y-m-d H:i:s'));
        $this->assertSame('Asia/Jakarta', $wib->getTimezone()->getName());
    }

    public function test_null_date_returns_null(): void
    {
        $this->assertNull(Time::toWib(null));
    }

    public function test_format_wib_uses_24_hour_format(): void
    {
        $legacy = Carbon::parse('2026-08-14 08:11:24', 'Asia/Jakarta');

        $this->assertSame('14 Agustus 2026, 15:11', Time::formatWib($legacy));
    }
}
