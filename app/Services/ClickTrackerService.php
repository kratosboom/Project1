<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Game;
use Illuminate\Support\Facades\DB;

class ClickTrackerService
{
    /**
     * How many top-clicked games are flagged as Hot.
     */
    public const HOT_LIMIT = 10;

    /**
     * Minimum click_count required to be eligible as Hot.
     * Prevents games with 0 clicks from filling the top-N.
     */
    public const MIN_CLICKS_FOR_HOT = 1;

    /**
     * Record a single click for the given game and re-sync the hot flags.
     */
    public function record(Game $game): void
    {
        DB::transaction(function () use ($game) {
            $game->increment('click_count');
            $this->recomputeHotFlags();
        });
    }

    /**
     * Recompute which games are currently Hot based on top-N click counts.
     * Only games that meet MIN_CLICKS_FOR_HOT are considered.
     */
    public function recomputeHotFlags(): void
    {
        $hotIds = Game::query()
            ->where('click_count', '>=', self::MIN_CLICKS_FOR_HOT)
            ->orderByDesc('click_count')
            ->orderBy('id')
            ->limit(self::HOT_LIMIT)
            ->pluck('id')
            ->all();

        Game::query()->where('is_hot', true)
            ->when($hotIds !== [], fn ($q) => $q->whereNotIn('id', $hotIds))
            ->update(['is_hot' => false]);

        if ($hotIds !== []) {
            Game::query()->whereIn('id', $hotIds)->update(['is_hot' => true]);
        }
    }

    /**
     * Zero all click counts and hot flags — admin reset.
     */
    public function resetAll(): int
    {
        return DB::transaction(function (): int {
            $count = Game::query()->count();
            Game::query()->update(['click_count' => 0, 'is_hot' => false]);

            return $count;
        });
    }
}
