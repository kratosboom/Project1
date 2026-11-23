<?php

namespace Database\Seeders\Samples;

use Illuminate\Support\Carbon;

/**
 * Data contoh dengan struktur identik respons API-Football v3.
 * Dipakai test (Http::fake) dan LivescoreDemoSeeder untuk membangun tampilan
 * sebelum API key tersedia. Waktu dihitung relatif terhadap "sekarang".
 */
class ApiFootballSamples
{
    private const LEAGUES = [
        39 => ['Premier League', 'England', 'https://media.api-sports.io/flags/gb-eng.svg', 2026],
        140 => ['La Liga', 'Spain', 'https://media.api-sports.io/flags/es.svg', 2026],
        135 => ['Serie A', 'Italy', 'https://media.api-sports.io/flags/it.svg', 2026],
        78 => ['Bundesliga', 'Germany', 'https://media.api-sports.io/flags/de.svg', 2026],
        61 => ['Ligue 1', 'France', 'https://media.api-sports.io/flags/fr.svg', 2026],
        274 => ['Liga 1', 'Indonesia', 'https://media.api-sports.io/flags/id.svg', 2026],
    ];

    private const TEAMS = [
        42 => 'Arsenal', 49 => 'Chelsea', 50 => 'Manchester City', 40 => 'Liverpool', 33 => 'Manchester United', 47 => 'Tottenham',
        541 => 'Real Madrid', 529 => 'Barcelona', 530 => 'Atletico Madrid', 536 => 'Sevilla',
        505 => 'Inter', 489 => 'AC Milan', 496 => 'Juventus', 492 => 'Napoli',
        157 => 'Bayern München', 165 => 'Borussia Dortmund', 168 => 'Bayer Leverkusen',
        85 => 'Paris Saint Germain', 81 => 'Marseille',
        9101 => 'Persija Jakarta', 9102 => 'Persib Bandung',
    ];

    /**
     * [id, league, home, away, dayOffset, minutesFromNow, status, elapsed, gh, ga, htH, htA]
     */
    private static function matches(): array
    {
        return [
            [1000001, 39, 42, 49, 0, -70, '2H', 67, 2, 1, 1, 0],
            [1000002, 39, 50, 40, 0, 180, 'NS', null, null, null, null, null],
            [1000003, 39, 33, 47, 0, -300, 'FT', 90, 1, 1, 0, 1],
            [1000004, 140, 541, 529, 0, -50, 'HT', 45, 0, 0, 0, 0],
            [1000005, 140, 530, 536, 0, 300, 'NS', null, null, null, null, null],
            [1000006, 135, 505, 489, 0, -240, 'FT', 90, 3, 2, 2, 1],
            [1000007, 78, 157, 165, 0, -200, 'FT', 90, 4, 0, 2, 0],
            [1000008, 274, 9101, 9102, 0, -25, '1H', 23, 0, 1, null, null],
            [1000009, 61, 85, 81, -1, 0, 'FT', 90, 2, 0, 1, 0],
            [1000010, 135, 496, 492, -1, 0, 'FT', 90, 1, 1, 0, 0],
            [1000011, 78, 168, 157, 1, 0, 'NS', null, null, null, null, null],
            [1000012, 39, 40, 42, 1, 0, 'NS', null, null, null, null, null],
        ];
    }

    /** Respons /fixtures?date=Y-m-d (tanpa detail). */
    public static function fixturesByDate(string $date, string $tz = 'Asia/Jakarta'): array
    {
        return array_values(array_filter(
            array_map(fn ($m) => self::fixture($m, $tz, false), self::matches()),
            fn ($f) => Carbon::createFromTimestampUTC($f['fixture']['timestamp'])->timezone($tz)->toDateString() === $date,
        ));
    }

    /** Respons /fixtures?ids=... (dengan events, lineups, statistics). */
    public static function fixturesByIds(array $ids, string $tz = 'Asia/Jakarta'): array
    {
        return array_values(array_map(
            fn ($m) => self::fixture($m, $tz, true),
            array_filter(self::matches(), fn ($m) => in_array($m[0], array_map('intval', $ids), true)),
        ));
    }

    public static function allFixtures(string $tz = 'Asia/Jakarta', bool $withDetail = true): array
    {
        return array_map(fn ($m) => self::fixture($m, $tz, $withDetail), self::matches());
    }

    /** Respons /standings?league=&season= */
    public static function standings(int $league, int $season): array
    {
        $teams = match ($league) {
            39 => [42, 50, 40, 49, 47, 33],
            140 => [541, 529, 530, 536],
            135 => [505, 492, 489, 496],
            78 => [157, 168, 165],
            61 => [85, 81],
            274 => [9102, 9101],
            default => [],
        };
        if (! $teams) {
            return [];
        }

        $rows = [];
        foreach ($teams as $i => $id) {
            $played = 13;
            $win = max(0, 10 - $i * 2);
            $draw = min($played - $win, 2 + $i % 3);
            $lose = $played - $win - $draw;
            $gf = 30 - $i * 4;
            $ga = 10 + $i * 3;
            $rows[] = [
                'rank' => $i + 1,
                'team' => self::team($id),
                'points' => $win * 3 + $draw,
                'goalsDiff' => $gf - $ga,
                'group' => self::LEAGUES[$league][0],
                'form' => substr(str_repeat(['WWDWL', 'WDWWW', 'LWDWD', 'DLWLW', 'LLDWL', 'WLLDL'][$i % 6], 1), 0, 5),
                'status' => 'same',
                'description' => $i < 4 && in_array($league, [39, 140, 135, 78], true) ? 'Promotion - Champions League (League phase: )' : null,
                'all' => ['played' => $played, 'win' => $win, 'draw' => $draw, 'lose' => $lose, 'goals' => ['for' => $gf, 'against' => $ga]],
                'home' => ['played' => 7, 'win' => 0, 'draw' => 0, 'lose' => 0, 'goals' => ['for' => 0, 'against' => 0]],
                'away' => ['played' => 6, 'win' => 0, 'draw' => 0, 'lose' => 0, 'goals' => ['for' => 0, 'against' => 0]],
                'update' => now()->toIso8601String(),
            ];
        }

        return [[
            'league' => self::league($league, $season) + ['standings' => [$rows]],
        ]];
    }

    /** Respons /leagues?id= */
    public static function league(int $id, ?int $season = null): array
    {
        [$name, $country, $flag, $year] = self::LEAGUES[$id] ?? ['League '.$id, 'World', null, 2026];

        return [
            'id' => $id,
            'name' => $name,
            'country' => $country,
            'logo' => "https://media.api-sports.io/football/leagues/{$id}.png",
            'flag' => $flag,
            'season' => $season ?? $year,
        ];
    }

    public static function leagueInfo(int $id): array
    {
        $l = self::league($id);

        return [[
            'league' => ['id' => $id, 'name' => $l['name'], 'type' => 'League', 'logo' => $l['logo']],
            'country' => ['name' => $l['country'], 'code' => null, 'flag' => $l['flag']],
            'seasons' => [['year' => 2025, 'current' => false], ['year' => $l['season'], 'current' => true]],
        ]];
    }

    public static function status(): array
    {
        return [
            'account' => ['firstname' => 'Demo', 'lastname' => 'User'],
            'subscription' => ['plan' => 'Free', 'end' => now()->addYear()->toIso8601String(), 'active' => true],
            'requests' => ['current' => 12, 'limit_day' => 100],
        ];
    }

    private static function team(int $id): array
    {
        return [
            'id' => $id,
            'name' => self::TEAMS[$id],
            'logo' => $id > 9000 ? null : "https://media.api-sports.io/football/teams/{$id}.png",
        ];
    }

    private static function fixture(array $m, string $tz, bool $withDetail): array
    {
        [$id, $league, $home, $away, $dayOffset, $minutes, $status, $elapsed, $gh, $ga, $hth, $hta] = $m;

        $now = Carbon::now($tz);
        if ($dayOffset === 0) {
            $kick = $now->copy()->addMinutes($minutes);
            $kick = $kick->max($now->copy()->startOfDay()->addMinute())->min($now->copy()->endOfDay()->subMinutes(5));
        } else {
            $kick = $now->copy()->startOfDay()->addDays($dayOffset)->setTime(21 + ($id % 2), $id % 2 ? 0 : 30);
        }

        $long = [
            'NS' => 'Not Started', '1H' => 'First Half', 'HT' => 'Halftime', '2H' => 'Second Half', 'FT' => 'Match Finished',
        ][$status];

        $row = [
            'fixture' => [
                'id' => $id,
                'referee' => in_array($status, ['NS'], true) ? null : 'M. Oliver',
                'timezone' => $tz,
                'date' => $kick->toIso8601String(),
                'timestamp' => $kick->getTimestamp(),
                'venue' => ['id' => 500 + $home, 'name' => self::TEAMS[$home].' Stadium', 'city' => self::LEAGUES[$league][1]],
                'status' => ['long' => $long, 'short' => $status, 'elapsed' => $elapsed, 'extra' => null],
            ],
            'league' => self::league($league) + ['round' => 'Regular Season - 13'],
            'teams' => [
                'home' => self::team($home) + ['winner' => $gh === null || $gh === $ga ? null : $gh > $ga],
                'away' => self::team($away) + ['winner' => $gh === null || $gh === $ga ? null : $ga > $gh],
            ],
            'goals' => ['home' => $gh, 'away' => $ga],
            'score' => [
                'halftime' => ['home' => $hth, 'away' => $hta],
                'fulltime' => ['home' => $status === 'FT' ? $gh : null, 'away' => $status === 'FT' ? $ga : null],
                'extratime' => ['home' => null, 'away' => null],
                'penalty' => ['home' => null, 'away' => null],
            ],
        ];

        if ($withDetail) {
            $row['events'] = $status === 'NS' ? [] : self::events($home, $away, (int) $gh, (int) $ga, (int) $elapsed);
            $row['lineups'] = $status === 'NS' ? [] : [self::lineup($home, '4-3-3'), self::lineup($away, '4-2-3-1')];
            $row['statistics'] = $status === 'NS' ? [] : [self::stats($home, 56), self::stats($away, 44)];
        }

        return $row;
    }

    private static function events(int $home, int $away, int $gh, int $ga, int $elapsed): array
    {
        $ev = [];
        $minute = 8;
        $step = max(4, intdiv(max($elapsed, 10), max(1, $gh + $ga + 2)));
        for ($i = 0; $i < $gh + $ga; $i++) {
            $isHome = $i < $gh;
            $team = $isHome ? $home : $away;
            $ev[] = ['time' => ['elapsed' => $minute, 'extra' => null], 'team' => self::team($team),
                'player' => ['id' => $team * 100 + 9, 'name' => self::player($team, 9)],
                'assist' => ['id' => $team * 100 + 10, 'name' => self::player($team, 10)],
                'type' => 'Goal', 'detail' => 'Normal Goal', 'comments' => null];
            $minute += $step;
        }
        if ($elapsed >= 30) {
            $ev[] = ['time' => ['elapsed' => 31, 'extra' => null], 'team' => self::team($away),
                'player' => ['id' => $away * 100 + 4, 'name' => self::player($away, 4)], 'assist' => ['id' => null, 'name' => null],
                'type' => 'Card', 'detail' => 'Yellow Card', 'comments' => 'Foul'];
        }
        if ($elapsed >= 60) {
            $ev[] = ['time' => ['elapsed' => 61, 'extra' => null], 'team' => self::team($home),
                'player' => ['id' => $home * 100 + 7, 'name' => self::player($home, 7)], 'assist' => ['id' => $home * 100 + 14, 'name' => self::player($home, 14)],
                'type' => 'subst', 'detail' => 'Substitution 1', 'comments' => null];
        }
        usort($ev, fn ($a, $b) => $a['time']['elapsed'] <=> $b['time']['elapsed']);

        return $ev;
    }

    private static function player(int $team, int $no): string
    {
        $first = ['A.', 'B.', 'C.', 'D.', 'E.', 'F.', 'G.', 'H.', 'J.', 'K.', 'L.', 'M.', 'N.', 'R.', 'S.', 'T.'];
        $last = ['Santos', 'Müller', 'Rossi', 'García', 'Smith', 'Silva', 'Martin', 'Pratama', 'Kovač', 'Dubois', 'Jensen', 'Okafor', 'Nugroho', 'Lopez', 'Bianchi', 'Wright'];

        return $first[($team + $no) % 16].' '.$last[($team * 3 + $no) % 16];
    }

    private static function lineup(int $team, string $formation): array
    {
        $pos = ['G', 'D', 'D', 'D', 'D', 'M', 'M', 'M', 'F', 'F', 'F'];
        $start = [];
        foreach (range(1, 11) as $i) {
            $start[] = ['player' => ['id' => $team * 100 + $i, 'name' => self::player($team, $i), 'number' => $i, 'pos' => $pos[$i - 1], 'grid' => null]];
        }

        return [
            'team' => self::team($team) + ['colors' => null],
            'coach' => ['id' => $team, 'name' => self::player($team, 99), 'photo' => null],
            'formation' => $formation,
            'startXI' => $start,
            'substitutes' => [['player' => ['id' => $team * 100 + 14, 'name' => self::player($team, 14), 'number' => 14, 'pos' => 'M', 'grid' => null]]],
        ];
    }

    private static function stats(int $team, int $possession): array
    {
        $s = $possession > 50;

        return [
            'team' => self::team($team),
            'statistics' => [
                ['type' => 'Shots on Goal', 'value' => $s ? 7 : 3],
                ['type' => 'Total Shots', 'value' => $s ? 15 : 8],
                ['type' => 'Ball Possession', 'value' => $possession.'%'],
                ['type' => 'Corner Kicks', 'value' => $s ? 6 : 2],
                ['type' => 'Fouls', 'value' => $s ? 9 : 13],
                ['type' => 'Yellow Cards', 'value' => $s ? 1 : 2],
                ['type' => 'Offsides', 'value' => $s ? 2 : 1],
                ['type' => 'Passes %', 'value' => $s ? '88%' : '81%'],
            ],
        ];
    }
}
