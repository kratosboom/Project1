<?php

namespace Tests\Feature\Livescore;

use App\Services\ApiSports\ApiSportsException;
use App\Services\ApiSports\FootballClient;
use App\Services\ApiSports\QuotaGuard;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FootballClientTest extends TestCase
{
    use FakesApiFootball;

    public function test_sends_api_key_header_and_records_quota(): void
    {
        $this->fakeApiFootball(remaining: 57);

        $rows = app(FootballClient::class)->fixturesByDate(now('Asia/Jakarta')->toDateString());

        $this->assertNotEmpty($rows);
        Http::assertSent(fn (Request $r) => $r->hasHeader('x-apisports-key', 'test-key')
            && str_starts_with($r->url(), 'https://v3.football.api-sports.io/fixtures')
            && str_contains($r->url(), 'timezone=Asia'));
        $this->assertSame(57, app(QuotaGuard::class)->remaining());
    }

    public function test_status_does_not_touch_quota(): void
    {
        $this->fakeApiFootball(remaining: 3);

        $s = app(FootballClient::class)->status();

        $this->assertSame('Free', $s['subscription']['plan']);
        $this->assertNull(app(QuotaGuard::class)->remaining());
    }

    public function test_api_errors_envelope_throws(): void
    {
        config(['livescore.api_key' => 'test-key']);
        Http::fake(['*' => Http::response(['errors' => ['plan' => 'Free plans do not have access to this season.'], 'response' => []])]);

        $this->expectException(ApiSportsException::class);
        $this->expectExceptionMessage('Free plans do not have access');

        app(FootballClient::class)->standings(39, 2026);
    }

    public function test_missing_key_throws_without_request(): void
    {
        config(['livescore.api_key' => null]);
        Http::fake();

        try {
            app(FootballClient::class)->fixturesByDate('2026-11-23');
            $this->fail('Expected exception');
        } catch (ApiSportsException $e) {
            $this->assertStringContainsString('API_SPORTS_KEY', $e->getMessage());
        }
        Http::assertNothingSent();
    }
}
