<?php

namespace App\Services\ApiSports;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Klien tipis untuk API-Football v3 (api-sports.io).
 * Dokumentasi: https://www.api-football.com/documentation-v3
 */
class FootballClient
{
    public function __construct(private QuotaGuard $quota)
    {
    }

    public function isConfigured(): bool
    {
        return filled(config('livescore.api_key'));
    }

    /** Status akun & pemakaian. Tidak memotong kuota harian. */
    public function status(): array
    {
        return $this->get('status', [], countsQuota: false);
    }

    /** @return array<int, array> */
    public function fixturesByDate(string $date): array
    {
        return $this->get('fixtures', ['date' => $date, 'timezone' => config('livescore.timezone')]);
    }

    /**
     * Detail lengkap (events, lineups, statistics, players) untuk maks 20 fixture.
     *
     * @param  array<int>  $ids
     * @return array<int, array>
     */
    public function fixturesByIds(array $ids): array
    {
        return $this->get('fixtures', ['ids' => implode('-', $ids), 'timezone' => config('livescore.timezone')]);
    }

    /** @return array<int, array> */
    public function standings(int $league, int $season): array
    {
        return $this->get('standings', ['league' => $league, 'season' => $season]);
    }

    /** @return array<int, array> */
    public function league(int $id): array
    {
        return $this->get('leagues', ['id' => $id]);
    }

    /**
     * @return array<mixed>  isi "response" dari envelope API
     */
    public function get(string $endpoint, array $query = [], bool $countsQuota = true): array
    {
        if (! $this->isConfigured()) {
            throw new ApiSportsException('API_SPORTS_KEY belum diisi di .env.');
        }

        try {
            $res = $this->http()->get($endpoint, $query);
        } catch (ConnectionException $e) {
            throw new ApiSportsException('Tidak bisa terhubung ke API-Sports: '.$e->getMessage(), 0, $e);
        }

        if ($countsQuota) {
            $this->recordQuota($res);
        }

        if ($res->failed()) {
            throw new ApiSportsException("API-Sports HTTP {$res->status()} untuk /{$endpoint}");
        }

        $body = $res->json() ?? [];
        $errors = $body['errors'] ?? [];

        // "errors" berupa [] bila sukses, atau objek {"field":"pesan"} bila gagal.
        if (! empty($errors)) {
            $msg = is_array($errors) ? implode('; ', array_map('strval', $errors)) : (string) $errors;
            Log::warning('API-Sports error', ['endpoint' => $endpoint, 'query' => $query, 'errors' => $errors]);
            throw new ApiSportsException("API-Sports menolak /{$endpoint}: {$msg}");
        }

        return $body['response'] ?? [];
    }

    private function http(): PendingRequest
    {
        return Http::baseUrl(rtrim(config('livescore.base_url'), '/'))
            ->withHeaders(['x-apisports-key' => config('livescore.api_key')])
            ->acceptJson()
            ->timeout((int) config('livescore.timeout'))
            ->retry(2, 500, throw: false);
    }

    private function recordQuota(Response $res): void
    {
        $remaining = $res->header('x-ratelimit-requests-remaining');
        $limit = $res->header('x-ratelimit-requests-limit');

        $this->quota->record(
            $remaining !== '' ? (int) $remaining : null,
            $limit !== '' ? (int) $limit : null,
        );
    }
}
