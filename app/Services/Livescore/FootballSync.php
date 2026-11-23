<?php

namespace App\Services\Livescore;

use App\Models\Fixture;
use App\Models\League;
use App\Models\Standing;
use App\Models\Team;
use App\Services\ApiSports\FootballClient;
use App\Services\ApiSports\QuotaGuard;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Menarik data dari API-Football dan menyimpannya ke database.
 * Halaman publik hanya membaca database, tidak pernah memanggil API.
 */
class FootballSync
{
    public const LAST_SYNC_KEY = 'livescore:last-sync';

    public function __construct(private FootballClient $api, private QuotaGuard $quota)
    {
    }

    /** @return array<int> */
    public function trackedLeagues(): array
    {
        return config('livescore.leagues', []);
    }

    /**
     * Ambil semua pertandingan pada tanggal (Y-m-d, zona livescore) lalu simpan yang
     * termasuk liga pilihan. Satu request.
     */
    public function syncDate(string $date, bool $essential = false): ?int
    {
        if (! $this->quota->allows($essential)) {
            return null;
        }

        $rows = $this->api->fixturesByDate($date);
        $tracked = $this->trackedLeagues();
        $count = 0;

        DB::transaction(function () use ($rows, $tracked, &$count) {
            foreach ($rows as $row) {
                if ($tracked && ! in_array((int) data_get($row, 'league.id'), $tracked, true)) {
                    continue;
                }
                $this->upsertFixture($row);
                $count++;
            }
        });

        $this->markSynced('fixtures:'.$date);

        return $count;
    }

    /**
     * Lengkapi events/lineups/statistik untuk pertandingan live & yang baru selesai.
     * Maks 20 id per request; dibatasi $maxRequests per jalan.
     */
    public function syncDetails(int $maxRequests = 2): ?int
    {
        $ids = Fixture::query()
            ->where(function ($q) {
                $q->whereIn('status_short', Fixture::LIVE)
                    ->orWhere(fn ($q) => $q->whereIn('status_short', Fixture::FINISHED)
                        ->where('kickoff_at', '>=', now()->subDays(3))
                        ->where(fn ($q) => $q->whereNull('detail_synced_at')->orWhereColumn('detail_synced_at', '<', 'updated_at')));
            })
            ->orderByDesc('kickoff_at')
            ->limit($maxRequests * (int) config('livescore.details_batch', 20))
            ->pluck('api_id');

        if ($ids->isEmpty()) {
            return 0;
        }

        $done = 0;
        foreach ($ids->chunk((int) config('livescore.details_batch', 20)) as $chunk) {
            if (! $this->quota->allows()) {
                break;
            }
            foreach ($this->api->fixturesByIds($chunk->values()->all()) as $row) {
                $this->upsertFixture($row);
                $done++;
            }
        }

        $this->markSynced('details');

        return $done;
    }

    /** Klasemen semua liga pilihan yang sudah diketahui musimnya. 1 request/liga. */
    public function syncStandings(): int
    {
        $done = 0;

        foreach (League::query()->whereIn('api_id', $this->trackedLeagues())->whereNotNull('season')->get() as $league) {
            if (! $this->quota->allows()) {
                break;
            }

            $response = $this->api->standings($league->api_id, $league->season);
            $groups = data_get($response, '0.league.standings', []);

            if (! empty($groups)) {
                Standing::query()->updateOrCreate(
                    ['league_id' => $league->id, 'season' => $league->season],
                    ['groups' => $groups, 'synced_at' => now()],
                );
                $done++;
            }
        }

        $this->markSynced('standings');

        return $done;
    }

    /** Info liga + musim berjalan untuk liga pilihan. 1 request/liga, cukup seminggu sekali. */
    public function syncLeagues(): int
    {
        $done = 0;

        foreach ($this->trackedLeagues() as $apiId) {
            if (! $this->quota->allows()) {
                break;
            }

            $row = $this->api->league($apiId)[0] ?? null;
            if (! $row) {
                continue;
            }

            $current = collect($row['seasons'] ?? [])->firstWhere('current', true)
                ?? collect($row['seasons'] ?? [])->sortByDesc('year')->first();

            $this->upsertLeague([
                'id' => data_get($row, 'league.id'),
                'name' => data_get($row, 'league.name'),
                'type' => data_get($row, 'league.type'),
                'logo' => data_get($row, 'league.logo'),
                'country' => data_get($row, 'country.name'),
                'flag' => data_get($row, 'country.flag'),
                'season' => config('livescore.season') ?? ($current['year'] ?? null),
            ]);
            $done++;
        }

        $this->markSynced('leagues');

        return $done;
    }

    /** Simpan satu item "response" dari endpoint /fixtures. */
    public function upsertFixture(array $row): Fixture
    {
        $league = $this->upsertLeague($row['league'] ?? []);
        $home = $this->upsertTeam(data_get($row, 'teams.home', []));
        $away = $this->upsertTeam(data_get($row, 'teams.away', []));

        $apiId = (int) data_get($row, 'fixture.id');
        $kickoff = Carbon::createFromTimestampUTC((int) data_get($row, 'fixture.timestamp'));

        $data = [
            'league_id' => $league->id,
            'season' => data_get($row, 'league.season'),
            'round' => data_get($row, 'league.round'),
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'kickoff_at' => $kickoff,
            'status_short' => (string) data_get($row, 'fixture.status.short', 'NS'),
            'status_long' => data_get($row, 'fixture.status.long'),
            'elapsed' => data_get($row, 'fixture.status.elapsed'),
            'elapsed_extra' => data_get($row, 'fixture.status.extra'),
            'goals_home' => data_get($row, 'goals.home'),
            'goals_away' => data_get($row, 'goals.away'),
            'score' => $row['score'] ?? null,
            'venue_name' => data_get($row, 'fixture.venue.name'),
            'venue_city' => data_get($row, 'fixture.venue.city'),
            'referee' => data_get($row, 'fixture.referee'),
        ];

        // Respons /fixtures?ids= menyertakan detail; /fixtures?date= tidak.
        if (array_key_exists('events', $row) || array_key_exists('lineups', $row)) {
            $data['events'] = $row['events'] ?? [];
            $data['lineups'] = $row['lineups'] ?? [];
            $data['statistics'] = $row['statistics'] ?? [];
            $data['detail_synced_at'] = now()->addSecond();
        }

        $fixture = Fixture::query()->firstWhere('api_id', $apiId);

        if ($fixture) {
            $fixture->update($data);

            return $fixture;
        }

        $data['api_id'] = $apiId;
        // Slug dibuat sekali & tidak berubah (stabil untuk SEO meski jadwal digeser).
        $data['slug'] = Str::slug($home->name.' vs '.$away->name.' '.$kickoff->copy()->timezone(config('livescore.timezone'))->toDateString()).'-'.$apiId;

        return Fixture::query()->create($data);
    }

    private function upsertLeague(array $l): League
    {
        $apiId = (int) ($l['id'] ?? 0);
        $tracked = $this->trackedLeagues();
        $order = array_search($apiId, $tracked, true);

        $attrs = array_filter([
            'name' => $l['name'] ?? null,
            'type' => $l['type'] ?? null,
            'country' => $l['country'] ?? null,
            'country_flag' => $l['flag'] ?? null,
            'logo' => $l['logo'] ?? null,
            'season' => isset($l['season']) ? (int) $l['season'] : null,
        ], fn ($v) => $v !== null);
        $attrs['sort_order'] = $order === false ? 999 : $order;

        $league = League::query()->firstWhere('api_id', $apiId);
        if ($league) {
            $league->update($attrs);

            return $league;
        }

        $name = config('livescore.league_names.'.$apiId, $attrs['name'] ?? 'liga-'.$apiId);

        return League::query()->create($attrs + [
            'api_id' => $apiId,
            'name' => $attrs['name'] ?? $name,
            'slug' => $this->uniqueSlug(League::class, Str::slug($name), $apiId),
        ]);
    }

    private function upsertTeam(array $t): Team
    {
        $apiId = (int) ($t['id'] ?? 0);
        $team = Team::query()->firstWhere('api_id', $apiId);
        $attrs = array_filter(['name' => $t['name'] ?? null, 'logo' => $t['logo'] ?? null]);

        if ($team) {
            $team->update($attrs);

            return $team;
        }

        $name = $attrs['name'] ?? 'tim-'.$apiId;

        return Team::query()->create($attrs + [
            'api_id' => $apiId,
            'name' => $name,
            'slug' => $this->uniqueSlug(Team::class, Str::slug($name), $apiId),
        ]);
    }

    private function uniqueSlug(string $model, string $slug, int $apiId): string
    {
        $slug = $slug !== '' ? $slug : (string) $apiId;

        return $model::query()->where('slug', $slug)->exists() ? $slug.'-'.$apiId : $slug;
    }

    private function markSynced(string $what): void
    {
        $all = Cache::get(self::LAST_SYNC_KEY, []);
        $all[$what] = now()->toIso8601String();
        $all['_any'] = $all[$what];
        Cache::forever(self::LAST_SYNC_KEY, $all);
    }

    public static function lastSyncedAt(?string $what = '_any'): ?Carbon
    {
        $v = Cache::get(self::LAST_SYNC_KEY, [])[$what] ?? null;

        return $v ? Carbon::parse($v) : null;
    }
}
