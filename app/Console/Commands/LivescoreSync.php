<?php

namespace App\Console\Commands;

use App\Services\ApiSports\ApiSportsException;
use App\Services\ApiSports\QuotaGuard;
use App\Services\Livescore\FootballSync;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class LivescoreSync extends Command
{
    protected $signature = 'livescore:sync
        {task=today : today | around | details | standings | leagues | date | all}
        {--date= : Tanggal (Y-m-d) untuk task "date"}';

    protected $description = 'Sinkron data sepak bola dari API-Sports ke database (hemat kuota).';

    public function handle(FootballSync $sync, QuotaGuard $quota): int
    {
        $tz = config('livescore.timezone');
        $today = Carbon::now($tz);

        try {
            $result = match ($this->argument('task')) {
                'today' => ['hari ini' => $sync->syncDate($today->toDateString(), essential: true)],
                'around' => [
                    'kemarin' => $sync->syncDate($today->copy()->subDay()->toDateString()),
                    'besok' => $sync->syncDate($today->copy()->addDay()->toDateString()),
                ],
                'date' => [$this->option('date') => $sync->syncDate(Carbon::parse((string) $this->option('date'), $tz)->toDateString())],
                'details' => ['detail' => $sync->syncDetails()],
                'standings' => ['klasemen' => $sync->syncStandings()],
                'leagues' => ['liga' => $sync->syncLeagues()],
                'all' => [
                    'liga' => $sync->syncLeagues(),
                    'hari ini' => $sync->syncDate($today->toDateString(), essential: true),
                    'detail' => $sync->syncDetails(),
                    'klasemen' => $sync->syncStandings(),
                ],
                default => throw new \InvalidArgumentException('Task tidak dikenal.'),
            };
        } catch (ApiSportsException|\InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        foreach ($result as $label => $n) {
            $this->line(sprintf('  %-10s %s', $label, $n === null ? 'dilewati (kuota menipis)' : $n.' item'));
        }

        $left = $quota->remaining();
        $this->info('Sisa kuota hari ini: '.($left ?? 'belum diketahui'));

        return self::SUCCESS;
    }
}
