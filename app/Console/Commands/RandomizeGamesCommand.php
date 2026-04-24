<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\GameRandomizerService;
use Illuminate\Console\Command;

class RandomizeGamesCommand extends Command
{
    protected $signature = 'games:randomize
        {--quiet-success : Suppress success output — useful when scheduled}';

    protected $description = 'Randomize RTP, Jam Gacor, Pola, and Maxwin values for all games. Does not touch is_hot / click_count.';

    public function handle(GameRandomizerService $randomizer): int
    {
        $count = $randomizer->randomizeAll();

        if (! $this->option('quiet-success')) {
            $this->info(sprintf('[%s] Randomized %d game(s).', now()->format('Y-m-d H:i:s'), $count));
        }

        return self::SUCCESS;
    }
}
