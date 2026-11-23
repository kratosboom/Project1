<?php

namespace Tests\Feature\Livescore;

use App\Models\Fixture;
use App\Models\League;
use App\Models\Standing;
use App\Services\ApiSports\QuotaGuard;
use App\Services\Livescore\FootballSync;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FootballSyncTest extends TestCase
{
    use FakesApiFootball, RefreshDatabase;

    private function today(): string
    {
        return now('Asia/Jakarta')->toDateString();
    }

    public function test_sync_date_stores_only_tracked_leagues_and_is_idempotent(): void
    {
        $this->fakeApiFootball();
        config(['livescore.leagues' => [39, 140]]);
        $sync = app(FootballSync::class);

        $count = $sync->syncDate($this->today());

        $this->assertSame(Fixture::count(), $count);
        $this->assertSame(['39', '140'], League::pluck('api_id')->map(fn ($v) => (string) $v)->sort()->values()->all());

        $slug = Fixture::firstWhere('api_id', 1000001)->slug;
        $sync->syncDate($this->today());

        $this->assertSame($count, Fixture::count(), 'tidak boleh duplikat');
        $this->assertSame($slug, Fixture::firstWhere('api_id', 1000001)->slug, 'slug harus stabil');
        Http::assertSentCount(2);
    }

    public function test_fixture_fields_are_mapped(): void
    {
        $this->fakeApiFootball();
        app(FootballSync::class)->syncDate($this->today());

        $f = Fixture::with(['home', 'away', 'league'])->firstWhere('api_id', 1000001);

        $this->assertSame('Arsenal', $f->home->name);
        $this->assertSame('Chelsea', $f->away->name);
        $this->assertSame('2H', $f->status_short);
        $this->assertTrue($f->isLive());
        $this->assertSame("67'", $f->statusLabel());
        $this->assertSame([2, 1], [$f->goals_home, $f->goals_away]);
        $this->assertSame('Liga Primer Inggris', $f->league->displayName());
        $this->assertSame('liga-primer-inggris', $f->league->slug);
        $this->assertNull($f->events, 'endpoint per tanggal tidak membawa detail');
    }

    public function test_details_sync_fills_events_lineups_and_statistics_in_one_batch(): void
    {
        $this->fakeApiFootball();
        $sync = app(FootballSync::class);
        $sync->syncDate($this->today());

        $sync->syncDetails();

        $f = Fixture::firstWhere('api_id', 1000006); // FT 3-2
        $this->assertCount(2, $f->lineups);
        $this->assertNotEmpty($f->statistics);
        $this->assertSame(5, collect($f->events)->where('type', 'Goal')->count());
        $this->assertNotNull($f->detail_synced_at);
        Http::assertSentCount(2); // 1x tanggal + 1x detail (semua id dalam satu request)
    }

    public function test_standings_and_leagues_sync(): void
    {
        $this->fakeApiFootball();
        $sync = app(FootballSync::class);

        $this->assertSame(6, $sync->syncLeagues());
        $this->assertSame(2026, League::firstWhere('api_id', 39)->season);

        $this->assertSame(6, $sync->syncStandings());
        $this->assertSame('Arsenal', Standing::first()->groups[0][0]['team']['name']);
    }

    public function test_non_essential_sync_skipped_when_quota_low(): void
    {
        $this->fakeApiFootball();
        config(['livescore.quota.reserve' => 10]);
        app(QuotaGuard::class)->record(5, 100);

        $this->assertNull(app(FootballSync::class)->syncDate($this->today()));
        $this->assertIsInt(app(FootballSync::class)->syncDate($this->today(), essential: true));
        Http::assertSentCount(1);
    }

    public function test_sync_command(): void
    {
        $this->fakeApiFootball(remaining: 42);

        $this->artisan('livescore:sync', ['task' => 'today'])
            ->expectsOutputToContain('Sisa kuota hari ini: 42')
            ->assertSuccessful();

        $this->assertGreaterThan(0, Fixture::count());
    }

    public function test_sync_command_fails_cleanly_without_key(): void
    {
        config(['livescore.api_key' => null]);

        $this->artisan('livescore:sync', ['task' => 'today'])
            ->expectsOutputToContain('API_SPORTS_KEY')
            ->assertFailed();
    }
}
