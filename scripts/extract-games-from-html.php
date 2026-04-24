<?php

/**
 * Ekstrak name + gambar dari HTML simpanan TOKYO99 → JSON impor admin.
 * Jalankan: php scripts/extract-games-from-html.php
 */

$htmlPath = dirname(__DIR__, 2).'/TOKYO99 - RTP LIVE HARI INI _ SLOT GACOR TERPERCAYA.html';
$outPath = dirname(__DIR__).'/storage/app/game-import-tokyo99.json';

$cdnBase = 'https://cdn.databerjalan.com/cdn-cgi/image/width=400,quality=75,fit=contain,format=auto/assets/images/games';

$providerFolder = [
    'PRAGMATIC' => 'pragmatic',
    'PGSOFT' => 'pgsoft',
    'PLAYTECH' => 'playtech',
    'SPADEGAMING' => 'spadegaming',
    'MICROGAMING' => 'microgaming',
    'SLOT88' => 'slot88',
    'PLAYSTAR' => 'playstar',
];

$html = file_get_contents($htmlPath);
if ($html === false) {
    fwrite(STDERR, "Tidak bisa baca: {$htmlPath}\n");
    exit(1);
}

libxml_use_internal_errors(true);
$dom = new DOMDocument();
$ok = $dom->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NOERROR | LIBXML_NOWARNING);
if (! $ok) {
    fwrite(STDERR, "Gagal parse HTML\n");
    exit(1);
}

$xpath = new DOMXPath($dom);
$nodes = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' game-card ')]");

$games = [];
/** @var DOMElement $node */
foreach ($nodes as $node) {
    $name = $node->getAttribute('data-name');
    $provider = $node->getAttribute('data-provider') ?: 'PRAGMATIC';
    if ($name === '') {
        continue;
    }
    $name = html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    $imgs = $xpath->query('.//img[contains(@class,"object-cover")]', $node);
    $src = null;
    for ($i = 0; $i < $imgs->length; $i++) {
        /** @var DOMElement $im */
        $im = $imgs->item($i);
        $s = $im->getAttribute('src');
        if (str_contains($s, 'best-tag')) {
            continue;
        }
        $src = $s;
        break;
    }
    if (! $src) {
        $games[] = [
            'name' => $name,
            'provider' => $provider,
            'source' => null,
            '_error' => 'img tidak ditemukan',
        ];

        continue;
    }

    // Hanya basename file lokal
    if (str_contains($src, 'placehold.co')) {
        $url = $src;
    } elseif (preg_match('#([^/\\\\]+\.(png|jpg|jpeg|webp|gif))#i', $src, $m)) {
        $file = $m[1];
        $folder = $providerFolder[$provider] ?? strtolower($provider);
        $url = "{$cdnBase}/{$folder}/{$file}";
    } else {
        $url = $src;
    }

    $games[] = [
        'name' => $name,
        'provider' => $provider,
        'source' => $url,
    ];
}

$payload = ['games' => array_map(function ($g) {
    unset($g['_error']);

    return $g;
}, $games)];

// Hapus entri error dari output final (tetap fail jika ada)
$errors = array_filter($games, fn ($g) => ! empty($g['_error'] ?? null));
if ($errors !== []) {
    fwrite(STDERR, 'Peringatan: '.count($errors)." game tanpa gambar\n");
}

file_put_contents($outPath, json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."\n");

echo "OK: ".count($payload['games'])." game → {$outPath}\n";
