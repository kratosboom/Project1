<?php

/**
 * Ekstrak daftar game (nama + URL gambar) dari file HTML hasil **simpan dari browser**
 * (Ctrl+S → Webpage, Complete) — diperlukan karena laju22.net memakai Cloudflare.
 *
 * Usage:
 *   php scripts/extract-saved-casino-page.php "C:\path\laju22-pragmatic.html"
 *   php scripts/extract-saved-casino-page.php ./laju22.html storage/app/laju22-import.json --provider=pragmatic
 */

declare(strict_types=1);

$args = array_slice($argv, 1);
$providerDefault = 'pragmatic';
$in = null;
$out = dirname(__DIR__).'/storage/app/laju22-import.json';

foreach ($args as $a) {
    if (str_starts_with($a, '--provider=')) {
        $providerDefault = substr($a, 11);
        $providerDefault = match ($providerDefault) {
            'pragmatic-play' => 'pragmatic',
            default => $providerDefault,
        };
    } elseif ($in === null && ! str_starts_with($a, '--')) {
        $in = $a;
    } else {
        $out = $a;
    }
}

if ($in === null || ! is_readable($in)) {
    fwrite(STDERR, "Usage: php extract-saved-casino-page.php <file.html> [out.json] [--provider=pragmatic]\n");
    fwrite(STDERR, "Catatan: unduh dulu halaman di browser (setelah teka-teki Cloudflare selesai), lalu arahkan skrip ke file .html\n");
    exit(1);
}

$html = file_get_contents($in);
if ($html === false) {
    fwrite(STDERR, "Tidak bisa baca: {$in}\n");
    exit(1);
}

$games = [];
$seen = [];

$push = function (string $name, ?string $source, string $prov) use (&$games, &$seen) {
    $name = html_entity_decode(trim($name), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    if ($name === '' || $name === 'HOT' || $name === 'BEST' || strcasecmp($name, 'logo') === 0) {
        return;
    }
    if ($source !== null) {
        $source = trim($source);
        if ($source === '' || str_contains($source, 'placehold')) {
            $source = null;
        }
    }
    $key = mb_strtolower($name).'|'.($source ?? '');
    if (isset($seen[$key])) {
        return;
    }
    $seen[$key] = true;
    $games[] = ['name' => $name, 'provider' => $prov, 'source' => $source];
};

// ---- Strategi 1: kartu ala grid (sama seperti file TOKYO99) ----
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NOERROR | LIBXML_NOWARNING);
$xp = new DOMXPath($dom);
foreach ($xp->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' game-card ')]") as $node) {
    if (! $node instanceof DOMElement) {
        continue;
    }
    $name = $node->getAttribute('data-name') ?: $node->getAttribute('data-title');
    $prov = providerSlug($node->getAttribute('data-provider') ?: $providerDefault);
    if ($name === '') {
        continue;
    }
    $src = null;
    foreach ($xp->query('.//img[contains(@class,"object-cover")]', $node) as $im) {
        if (! $im instanceof DOMElement) {
            continue;
        }
        $s = $im->getAttribute('src');
        if (str_contains($s, 'best-tag')) {
            continue;
        }
        $src = $s ?: null;
        break;
    }
    $push($name, $src, $prov);
}

// ---- Strategi 2: JSON-LD VideoGame ----
if (preg_match_all('#<script[^>]*type=["\']application/ld\+json["\'][^>]*>(.*?)</script>#s', $html, $m)) {
    foreach ($m[1] as $block) {
        $block = html_entity_decode($block, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        try {
            $j = json_decode($block, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            continue;
        }
        $items = is_array($j) && array_is_list($j) ? $j : [$j];
        foreach ($items as $item) {
            walkLdJson($item, $push, $providerDefault);
        }
    }
}

// ---- Strategi 3: __NEXT_DATA__ (Next.js) — cari objek game di dalam pohon JSON ----
if (preg_match('#<script[^>]*id=["\']__NEXT_DATA__["\'][^>]*>(.*?)</script>#s', $html, $m)) {
    try {
        $next = json_decode($m[1], true, 512, JSON_THROW_ON_ERROR);
        extractGamesFromJsonTree($next, $push, $providerDefault);
    } catch (Throwable) {
        // abaikan
    }
}

// ---- Strategi 4: link / card dengan img + alt (umum) ----
if (count($games) < 3) {
    foreach ($xp->query('//a[.//img[@alt and @src]]') as $a) {
        if (! $a instanceof DOMElement) {
            continue;
        }
        $img = $xp->query('.//img', $a)->item(0);
        if (! $img instanceof DOMElement) {
            continue;
        }
        $name = $img->getAttribute('alt');
        $src = $img->getAttribute('src') ?: null;
        $href = $a->getAttribute('href') ?? '';
        if (! str_contains($href, 'slot') && ! str_contains($href, 'game') && ! str_contains($href, 'pragmatic')) {
            continue;
        }
        $push($name, $src, providerSlug($providerDefault));
    }
}

$games = dedupeGamesByNamePreferImage($games);

$payload = ['games' => $games];
file_put_contents($out, json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."\n");

fwrite(STDOUT, "OK: ".count($games)." game → {$out}\n");
if (count($games) === 0) {
    fwrite(STDERR, "Tidak ada data terbaca. Pastikan file hasil Simpan laman (bukan hanya 'Just a moment' Cloudflare).\n");
    exit(2);
}

function providerSlug(string $p): string
{
    $p = strtoupper(trim($p));
    $p = match ($p) {
        'PRAGMATIC-PLAY', 'PRAGMATIC PLAY' => 'PRAGMATIC',
        default => $p,
    };

    return $p !== '' ? $p : 'PRAGMATIC';
}

/**
 * Satu baris per nama game; utamakan entri yang punya URL gambar.
 *
 * @param  array<int, array{name: string, provider: string, source: ?string}>  $games
 * @return array<int, array{name: string, provider: string, source: ?string}>
 */
function dedupeGamesByNamePreferImage(array $games): array
{
    $by = [];
    foreach ($games as $g) {
        $k = mb_strtolower($g['name']);
        if (! isset($by[$k])) {
            $by[$k] = $g;

            continue;
        }
        $prev = $by[$k];
        $hasNew = ! empty($g['source']);
        $hasPrev = ! empty($prev['source']);
        if ($hasNew && ! $hasPrev) {
            $by[$k] = $g;
        }
    }

    return array_values($by);
}

/**
 * @param  mixed  $item
 * @param  callable(string, ?string, string): void  $push
 */
function walkLdJson(mixed $item, callable $push, string $defaultProv): void
{
    if (! is_array($item)) {
        return;
    }
    $type = $item['@type'] ?? null;
    if ($type === 'VideoGame' && ! empty($item['name'])) {
        $img = $item['image'] ?? null;
        if (is_array($img)) {
            $img = is_string($img['url'] ?? null) ? $img['url'] : (is_string($img[0] ?? null) ? $img[0] : null);
        }
        $push((string) $item['name'], is_string($img) ? $img : null, providerSlug($defaultProv));
    }
    foreach ($item as $v) {
        if (is_array($v)) {
            walkLdJson($v, $push, $defaultProv);
        }
    }
}

/**
 * @param  mixed  $node
 * @param  callable(string, ?string, string): void  $push
 */
function extractGamesFromJsonTree(mixed $node, callable $push, string $defaultProv, int $depth = 0): void
{
    if ($depth > 25 || ! is_array($node)) {
        return;
    }
    $candidates = ['name', 'title', 'gameName', 'game_name'];
    $imgKeys = ['image', 'imageUrl', 'image_url', 'thumbnail', 'thumb', 'cover', 'icon', 'src', 'url'];
    foreach ($candidates as $ck) {
        foreach ($imgKeys as $ik) {
            if (! isset($node[$ck], $node[$ik]) || ! is_string($node[$ck])) {
                continue;
            }
            $img = $node[$ik];
            if (is_string($img) && (str_starts_with($img, 'http') || str_starts_with($img, '//') || str_starts_with($img, '/'))) {
                $push($node[$ck], str_starts_with($img, '//') ? 'https:'.$img : $img, providerSlug($defaultProv));
                return;
            }
        }
    }
    foreach ($node as $v) {
        if (is_array($v)) {
            extractGamesFromJsonTree($v, $push, $defaultProv, $depth + 1);
        }
    }
}
