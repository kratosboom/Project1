<?php

namespace App\Support;

use App\Models\GameProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Menyatukan penulisan provider (pgsoft vs pg-soft, spadegaming vs spade-gaming)
 * berdasarkan normalisasi alfanumerik, lalu memilih baris yang punya game terbanyak.
 */
final class GameProviderBrandMatcher
{
    public static function normalize(string $value): string
    {
        $s = preg_replace('/[^a-z0-9]/', '', strtolower($value));

        return is_string($s) ? $s : '';
    }

    /**
     * Kunci pengelompokan untuk provider non–HOT GAMES (slug saja).
     */
    public static function groupKey(GameProvider $p): string
    {
        if ($p->is_hot_games) {
            return 'hot:'.$p->id;
        }

        return self::normalize((string) $p->slug);
    }

    /**
     * Provider yang cocok dengan ?provider=... (slug URL atau bentuk mirip nama).
     */
    public static function matchingProviders(Collection $providers, string $urlSlug): Collection
    {
        $n = self::normalize($urlSlug);

        return $providers->filter(function (GameProvider $p) use ($n) {
            if ($p->is_hot_games) {
                return self::normalize((string) $p->slug) === $n;
            }

            return self::normalize((string) $p->slug) === $n
                || self::normalize(Str::slug((string) $p->name)) === $n;
        });
    }

    /**
     * Pilih satu provider kanonik: terbanyak game, lalu sort_order, lalu id.
     */
    public static function chooseCanonical(Collection $matches): ?GameProvider
    {
        if ($matches->isEmpty()) {
            return null;
        }

        return $matches->values()->sort(function (GameProvider $a, GameProvider $b) {
            $c = ($b->games_count ?? 0) <=> ($a->games_count ?? 0);

            if ($c !== 0) {
                return $c;
            }

            $c = $a->sort_order <=> $b->sort_order;

            if ($c !== 0) {
                return $c;
            }

            return $a->id <=> $b->id;
        })->first();
    }

    public static function resolveFromUrlSlug(Collection $providers, string $urlSlug): ?GameProvider
    {
        return self::chooseCanonical(self::matchingProviders($providers, $urlSlug));
    }

    /**
     * Satu tab per merek: slug yang menormalisasi sama digabung; pemenang punya data terbanyak.
     *
     * @param  Collection<int, GameProvider>  $providers
     * @return Collection<int, GameProvider>
     */
    public static function dedupeForNavigation(Collection $providers): Collection
    {
        /** @var array<string, Collection<int, GameProvider>> $buckets */
        $buckets = [];

        foreach ($providers as $p) {
            $key = self::groupKey($p);
            if (! isset($buckets[$key])) {
                $buckets[$key] = new Collection;
            }
            $buckets[$key]->push($p);
        }

        $winners = new Collection;

        foreach ($buckets as $group) {
            $winner = self::chooseCanonical($group);
            if ($winner !== null) {
                $winners->push($winner);
            }
        }

        return $winners->sort(function (GameProvider $a, GameProvider $b) {
            $c = $a->sort_order <=> $b->sort_order;

            return $c !== 0 ? $c : $a->id <=> $b->id;
        })->values();
    }
}
