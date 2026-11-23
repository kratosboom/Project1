<?php

namespace Database\Seeders;

use App\Services\Livescore\FootballSync;
use Database\Seeders\Samples\ApiFootballSamples;
use Illuminate\Database\Seeder;

/**
 * Isi data pertandingan contoh (tanpa API). Hanya untuk development/preview:
 *   php artisan db:seed --class=LivescoreDemoSeeder
 */
class LivescoreDemoSeeder extends Seeder
{
    public function run(FootballSync $sync): void
    {
        $tz = config('livescore.timezone');

        foreach (ApiFootballSamples::allFixtures($tz) as $row) {
            $sync->upsertFixture($row);
        }

        foreach ([39, 140, 135, 78, 61, 274] as $league) {
            foreach (ApiFootballSamples::standings($league, 2026) as $r) {
                $model = \App\Models\League::query()->firstWhere('api_id', $league);
                \App\Models\Standing::query()->updateOrCreate(
                    ['league_id' => $model->id, 'season' => 2026],
                    ['groups' => $r['league']['standings'], 'synced_at' => now()],
                );
            }
        }
    }
}
