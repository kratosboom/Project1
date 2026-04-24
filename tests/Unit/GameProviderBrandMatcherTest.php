<?php

namespace Tests\Unit;

use App\Models\GameProvider;
use App\Support\GameProviderBrandMatcher;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GameProviderBrandMatcherTest extends TestCase
{
    #[Test]
    public function normalize_collapses_hyphens_and_case(): void
    {
        $this->assertSame('spadegaming', GameProviderBrandMatcher::normalize('Spade-Gaming'));
        $this->assertSame('pgsoft', GameProviderBrandMatcher::normalize('pg-soft'));
    }

    #[Test]
    public function resolve_prefers_provider_with_more_games(): void
    {
        $empty = new GameProvider([
            'id' => 1,
            'slug' => 'microgaming',
            'name' => 'MICROGAMING',
            'sort_order' => 0,
            'is_hot_games' => false,
        ]);
        $empty->games_count = 0;

        $full = new GameProvider([
            'id' => 2,
            'slug' => 'micro-gaming',
            'name' => 'Micro Gaming',
            'sort_order' => 10,
            'is_hot_games' => false,
        ]);
        $full->games_count = 301;

        $resolved = GameProviderBrandMatcher::resolveFromUrlSlug(
            new Collection([$empty, $full]),
            'microgaming',
        );

        $this->assertNotNull($resolved);
        $this->assertSame('micro-gaming', $resolved->slug);
        $this->assertSame(301, $resolved->games_count);
    }

    #[Test]
    public function matching_accepts_url_slug_matching_name_slug(): void
    {
        $p = new GameProvider([
            'id' => 3,
            'slug' => 'spade-gaming',
            'name' => 'Spade Gaming',
            'sort_order' => 0,
            'is_hot_games' => false,
        ]);
        $p->games_count = 50;

        $m = GameProviderBrandMatcher::matchingProviders(new Collection([$p]), 'spadegaming');
        $this->assertCount(1, $m);
    }

    #[Test]
    public function dedupe_keeps_one_tab_per_normalized_slug(): void
    {
        $a = new GameProvider(['id' => 1, 'slug' => 'pgsoft', 'name' => 'PGSOFT', 'sort_order' => 1, 'is_hot_games' => false]);
        $a->games_count = 0;
        $b = new GameProvider(['id' => 2, 'slug' => 'pg-soft', 'name' => 'PG SOFT', 'sort_order' => 2, 'is_hot_games' => false]);
        $b->games_count = 200;

        $nav = GameProviderBrandMatcher::dedupeForNavigation(new Collection([$a, $b]));
        $this->assertCount(1, $nav);
        $this->assertSame('pg-soft', $nav->first()->slug);
    }
}
