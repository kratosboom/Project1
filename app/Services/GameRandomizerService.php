<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Game;
use Illuminate\Support\Facades\DB;

class GameRandomizerService
{
    /**
     * Randomize RTP, Jam Gacor, Pola, and Maxwin attributes for all games.
     * Hot status (is_hot) and click_count are NEVER touched — Hot is derived from clicks.
     */
    public function randomizeAll(): int
    {
        $count = 0;
        DB::transaction(function () use (&$count) {
            Game::query()->chunkById(100, function ($games) use (&$count) {
                foreach ($games as $game) {
                    $payload = $this->generateRandomizedAttributes($game);
                    $game->forceFill($payload)->save();
                    $count++;
                }
            });
        });

        return $count;
    }

    /**
     * Spec:
     *  - RTP 65.00–98.00% (step 0.01)
     *  - Jam gacor: start 00:00–23:45 (step 15m), durasi 3 jam (wrap di 24h)
     *  - Turbo Spin: kelipatan 5, max 100
     *  - Auto Spin: kelipatan 10, max 100
     *  - Manual Spin: kelipatan 1, max 100
     *  - maxwin_multiplier: 50–300x
     *  - Difficulty: tinggi kalau multiplier tinggi (1–5 bintang, bucket 50x per bintang)
     *
     * @return array<string,mixed>
     */
    public function generateRandomizedAttributes(Game $game): array
    {
        $rtp = round(random_int(6500, 9800) / 100, 2);

        $quarterHours = [0, 15, 30, 45];
        $startHour = random_int(0, 23);
        $startMin = $quarterHours[array_rand($quarterHours)];
        $endHour = ($startHour + 3) % 24;
        $jamGacor = sprintf('%02d.%02d - %02d.%02d', $startHour, $startMin, $endHour, $startMin);

        $turbo = random_int(1, 20) * 5;
        $auto = random_int(1, 10) * 10;
        $manual = random_int(1, 100);

        $multiplier = random_int(50, 300);
        $difficulty = match (true) {
            $multiplier < 100 => 1,
            $multiplier < 150 => 2,
            $multiplier < 200 => 3,
            $multiplier < 250 => 4,
            default => 5,
        };

        $modalData = is_array($game->modal_data) ? $game->modal_data : [];
        $modalData['maxwin_multiplier'] = $multiplier;

        return [
            'rtp' => $rtp,
            'jam_gacor' => $jamGacor,
            'pola' => [
                'turbo' => $turbo.'X',
                'auto' => $auto.'X',
                'manual' => $manual.'X',
            ],
            'maxwin_multiplier' => $multiplier,
            'maxwin_difficulty_min' => $difficulty,
            'maxwin_difficulty_max' => $difficulty,
            'modal_data' => $modalData,
        ];
    }
}
