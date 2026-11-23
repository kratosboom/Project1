<?php

namespace Tests\Feature\Livescore;

use App\Services\ApiSports\QuotaGuard;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class QuotaGuardTest extends TestCase
{
    public function test_allows_when_quota_unknown(): void
    {
        $this->assertTrue(app(QuotaGuard::class)->allows());
    }

    public function test_reserve_is_kept_for_essential_syncs(): void
    {
        config(['livescore.quota.reserve' => 10]);
        $guard = app(QuotaGuard::class);

        $guard->record(10, 100);
        $this->assertFalse($guard->allows());
        $this->assertTrue($guard->allows(essential: true));

        $guard->record(0, 100);
        $this->assertFalse($guard->allows(essential: true));

        $guard->record(11, 100);
        $this->assertTrue($guard->allows());
    }

    public function test_snapshot_from_previous_utc_day_is_ignored(): void
    {
        Cache::forever('livescore:quota', ['remaining' => 0, 'limit' => 100, 'day' => now('UTC')->subDay()->toDateString(), 'at' => now()->toIso8601String()]);

        $this->assertNull(app(QuotaGuard::class)->remaining());
        $this->assertTrue(app(QuotaGuard::class)->allows());
    }
}
