<?php

namespace Tests\Feature\Livescore;

use Database\Seeders\Samples\ApiFootballSamples;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

trait FakesApiFootball
{
    /** Palsukan API-Football memakai data contoh. $remaining = nilai header sisa kuota. */
    protected function fakeApiFootball(int $remaining = 80): void
    {
        config(['livescore.api_key' => 'test-key', 'livescore.leagues' => [39, 140, 135, 78, 61, 274]]);

        Http::fake(function (Request $request) use ($remaining) {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $q);
            $path = trim((string) parse_url($request->url(), PHP_URL_PATH), '/');
            $tz = config('livescore.timezone');

            $response = match (true) {
                $path === 'status' => ApiFootballSamples::status(),
                $path === 'fixtures' && isset($q['ids']) => ApiFootballSamples::fixturesByIds(explode('-', $q['ids']), $tz),
                $path === 'fixtures' && isset($q['date']) => ApiFootballSamples::fixturesByDate($q['date'], $tz),
                $path === 'standings' => ApiFootballSamples::standings((int) $q['league'], (int) $q['season']),
                $path === 'leagues' => ApiFootballSamples::leagueInfo((int) $q['id']),
                default => [],
            };

            return Http::response(
                ['get' => $path, 'parameters' => $q, 'errors' => [], 'results' => count($response), 'paging' => ['current' => 1, 'total' => 1], 'response' => $response],
                200,
                ['x-ratelimit-requests-limit' => '100', 'x-ratelimit-requests-remaining' => (string) $remaining],
            );
        });
    }
}
