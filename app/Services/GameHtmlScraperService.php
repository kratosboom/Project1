<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Game;
use App\Models\GameProvider;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GameHtmlScraperService
{
    /**
     * Parse a slot listing HTML page into a list of game payloads.
     * Supports two formats:
     *   1) tokyo99-like:  <div class="game-card" data-name data-provider data-rtp data-hot>
     *   2) laju22-like:   <div class="game-box" data-title data-filter> (no RTP data)
     *
     * @return list<array{name:string,provider_label:string,rtp:float,is_hot:bool,is_best:bool,image_url:?string,jam_gacor:string,pola:array<string,string>,ways:?string}>
     */
    public function parse(string $html, ?string $filterProviderLabel = null): array
    {
        if (trim($html) === '') {
            return [];
        }

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();

        $xpath = new DOMXPath($doc);
        $filter = $filterProviderLabel !== null ? strtoupper(trim($filterProviderLabel)) : '';

        $cards = $this->parseGameCards($xpath, $filter);
        if ($cards !== []) {
            return $cards;
        }

        return $this->parseGameBoxes($xpath, $filter);
    }

    /**
     * Tokyo99-style cards with rich data attributes.
     *
     * @return list<array<string,mixed>>
     */
    private function parseGameCards(DOMXPath $xpath, string $filter): array
    {
        $nodes = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' game-card ')]");

        if (! $nodes || $nodes->length === 0) {
            return [];
        }

        $results = [];

        foreach ($nodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $name = trim($node->getAttribute('data-name'));
            $providerLabel = trim($node->getAttribute('data-provider'));

            if ($name === '') {
                continue;
            }

            if ($filter !== '' && strtoupper($providerLabel) !== $filter) {
                continue;
            }

            $results[] = [
                'name' => $name,
                'provider_label' => $providerLabel,
                'rtp' => (float) ($node->getAttribute('data-rtp') ?: 0),
                // is_hot is derived from user clicks (ClickTracker), NOT from source site.
                'is_hot' => false,
                'is_best' => $this->detectBestBadge($node, $xpath),
                'image_url' => $this->extractImage($node, $xpath, $name),
                'jam_gacor' => $this->extractJamGacor($node, $xpath) ?: '00.00 - 00.00',
                'pola' => $this->extractPola($node, $xpath),
            ];
        }

        return $results;
    }

    /**
     * laju22-style boxes: only name + image. RTP/jam/pola must be filled manually.
     *
     * @return list<array<string,mixed>>
     */
    private function parseGameBoxes(DOMXPath $xpath, string $filter): array
    {
        $nodes = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' game-box ')]");

        if (! $nodes || $nodes->length === 0) {
            return [];
        }

        $results = [];

        foreach ($nodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $name = trim($node->getAttribute('data-title'));

            if ($name === '') {
                continue;
            }

            $dataFilter = strtoupper($node->getAttribute('data-filter'));

            if ($filter !== '' && ! str_contains($dataFilter, $filter)) {
                continue;
            }

            // is_hot is derived from user clicks (ClickTracker), NOT from source site flags.
            $isNew = str_contains($dataFilter, 'NEW') || str_contains($dataFilter, 'BARU');

            $imgNodes = $node->getElementsByTagName('img');
            $imageUrl = null;

            foreach ($imgNodes as $img) {
                if (! $img instanceof DOMElement) {
                    continue;
                }
                $src = $img->getAttribute('src');
                if ($src === '') {
                    $src = $img->getAttribute('data-src');
                }
                if ($src === '') {
                    continue;
                }
                if (! str_starts_with($src, 'data:')) {
                    $imageUrl = $src;
                    break;
                }
            }

            $results[] = [
                'name' => $name,
                'provider_label' => '',
                'rtp' => 0.0,
                'is_hot' => false,
                'is_best' => $isNew,
                'image_url' => $imageUrl,
                'jam_gacor' => '00.00 - 00.00',
                'pola' => [],
            ];
        }

        return $results;
    }

    /**
     * Upsert parsed cards into the `games` table.
     *
     * @param  list<array<string,mixed>>  $cards
     * @return array{created:int,updated:int,deleted:int}
     */
    public function upsert(array $cards, GameProvider $provider, bool $fresh = false): array
    {
        return DB::transaction(function () use ($cards, $provider, $fresh): array {
            $deleted = 0;

            if ($fresh) {
                $deleted = Game::query()->where('game_provider_id', $provider->id)->delete();
            }

            $created = 0;
            $updated = 0;
            $nextSort = ((int) Game::query()->where('game_provider_id', $provider->id)->max('sort_order')) + 1;

            foreach ($cards as $card) {
                $existing = Game::query()
                    ->where('game_provider_id', $provider->id)
                    ->where('name', $card['name'])
                    ->first();

                $sortOrder = $existing?->sort_order ?? $nextSort++;

                $attributes = [
                    'image_url' => $card['image_url'],
                    'rtp' => $card['rtp'],
                    'is_hot' => $card['is_hot'],
                    'is_best' => $card['is_best'],
                    'jam_gacor' => $card['jam_gacor'],
                    'pola' => $card['pola'],
                    'sort_order' => $sortOrder,
                ];

                if (Schema::hasColumn('games', 'is_active')) {
                    $attributes['is_active'] = true;
                }

                $game = Game::query()->updateOrCreate(
                    [
                        'game_provider_id' => $provider->id,
                        'name' => $card['name'],
                    ],
                    $attributes,
                );

                $game->wasRecentlyCreated ? $created++ : $updated++;
            }

            return ['created' => $created, 'updated' => $updated, 'deleted' => $deleted];
        });
    }

    private function detectBestBadge(DOMElement $node, DOMXPath $xpath): bool
    {
        $badges = $xpath->query('.//img[@alt]', $node);

        if (! $badges) {
            return false;
        }

        foreach ($badges as $badge) {
            if (! $badge instanceof DOMElement) {
                continue;
            }
            if (strcasecmp(trim($badge->getAttribute('alt')), 'BEST') === 0) {
                return true;
            }
        }

        return false;
    }

    private function extractImage(DOMElement $node, DOMXPath $xpath, string $name): ?string
    {
        $imgs = $xpath->query('.//img[@alt='.$this->xpathLiteral($name).']', $node);

        if ($imgs && $imgs->length > 0 && $imgs->item(0) instanceof DOMElement) {
            $src = $imgs->item(0)->getAttribute('src');
            if ($src !== '') {
                return $src;
            }
        }

        $fallback = $xpath->query(".//div[contains(@class, 'aspect-square')]//img", $node);

        if ($fallback && $fallback->length > 0 && $fallback->item(0) instanceof DOMElement) {
            $src = $fallback->item(0)->getAttribute('src');
            if ($src !== '') {
                return $src;
            }
        }

        return null;
    }

    private function extractJamGacor(DOMElement $node, DOMXPath $xpath): string
    {
        $paragraphs = $xpath->query(".//p[contains(@class, 'tracking-widest') or contains(@class, 'uppercase')]", $node);

        if (! $paragraphs) {
            return '';
        }

        foreach ($paragraphs as $p) {
            if (! $p instanceof DOMElement) {
                continue;
            }
            $text = trim(preg_replace('/\s+/', ' ', $p->textContent) ?? '');
            if (preg_match('/^\d{1,2}[.:]\d{2}\s*-\s*\d{1,2}[.:]\d{2}$/', $text)) {
                return $text;
            }
        }

        return '';
    }

    /**
     * @return array<string,string>
     */
    private function extractPola(DOMElement $node, DOMXPath $xpath): array
    {
        $rows = $xpath->query(".//div[contains(@class, 'flex') and contains(@class, 'justify-between')]", $node);

        if (! $rows) {
            return [];
        }

        $pola = [];

        foreach ($rows as $row) {
            if (! $row instanceof DOMElement) {
                continue;
            }
            $spans = $row->getElementsByTagName('span');
            if ($spans->length < 2) {
                continue;
            }
            $label = trim(preg_replace('/\s+/', ' ', $spans->item(0)->textContent) ?? '');
            $value = trim(preg_replace('/\s+/', ' ', $spans->item(1)->textContent) ?? '');

            if ($label === '' || $value === '') {
                continue;
            }

            if (! preg_match('/^\d+\s*x$/i', $value)) {
                continue;
            }

            $key = Str::snake(Str::lower($label));
            $pola[$key] = strtoupper(str_replace(' ', '', $value));
        }

        return $pola;
    }

    private function xpathLiteral(string $value): string
    {
        if (! str_contains($value, "'")) {
            return "'{$value}'";
        }

        if (! str_contains($value, '"')) {
            return "\"{$value}\"";
        }

        $parts = explode("'", $value);
        $expr = 'concat(';
        foreach ($parts as $i => $part) {
            if ($i > 0) {
                $expr .= ", \"'\", ";
            }
            $expr .= "'{$part}'";
        }

        return $expr.')';
    }
}
