<?php

return [

    /*
    | API-Sports — Football v3. Key hanya disimpan di .env (jangan di-commit).
    | Satu key API-Sports berlaku untuk semua olahraga (football, nba, mma, ...).
    */
    'api_key' => env('API_SPORTS_KEY'),
    'base_url' => env('API_FOOTBALL_URL', 'https://v3.football.api-sports.io'),
    'timeout' => (int) env('API_SPORTS_TIMEOUT', 15),

    // Zona waktu tampilan & parameter "timezone" ke API.
    'timezone' => env('LIVESCORE_TIMEZONE', 'Asia/Jakarta'),

    // Liga yang disimpan & ditampilkan (ID API-Football), urut = urutan tampil.
    'leagues' => array_values(array_filter(array_map('intval', explode(',', (string) env(
        'LIVESCORE_LEAGUES',
        '1,2,39,140,135,78,61,274'
    ))))),

    // Nama tampilan (Bahasa Indonesia) & slug URL untuk liga populer.
    'league_names' => [
        1 => 'Piala Dunia',
        2 => 'Liga Champions UEFA',
        3 => 'Liga Europa UEFA',
        4 => 'Euro',
        39 => 'Liga Primer Inggris',
        61 => 'Ligue 1 Prancis',
        78 => 'Bundesliga Jerman',
        135 => 'Serie A Italia',
        140 => 'La Liga Spanyol',
        274 => 'Liga 1 Indonesia',
    ],

    // Musim per liga bila tidak bisa ditebak (mis. liga kalender). Kosong = otomatis.
    'season' => env('LIVESCORE_SEASON') ? (int) env('LIVESCORE_SEASON') : null,

    /*
    | Kuota. Paket Free = 100 request/hari. Bila sisa kuota <= reserve,
    | sinkron yang tidak penting dilewati agar jatah skor hari ini tetap aman.
    */
    'quota' => [
        'daily_limit' => (int) env('API_SPORTS_DAILY_LIMIT', 100),
        'reserve' => (int) env('API_SPORTS_QUOTA_RESERVE', 10),
    ],

    /*
    | Interval scheduler (cron expression). Default disesuaikan paket Free (~76 req/hari).
    | Paket berbayar: mis. LIVESCORE_CRON_TODAY="* * * * *" (tiap menit).
    */
    'schedule' => [
        'today' => env('LIVESCORE_CRON_TODAY', '*/30 * * * *'),
        'around' => env('LIVESCORE_CRON_AROUND', '15 */6 * * *'),
        'details' => env('LIVESCORE_CRON_DETAILS', '45 */2 * * *'),
        'standings' => env('LIVESCORE_CRON_STANDINGS', '20 4 * * *'),
        'leagues' => env('LIVESCORE_CRON_LEAGUES', '10 3 * * 1'),
    ],

    // Detail (events/lineups/statistik) maks ids per request (batas API = 20).
    'details_batch' => 20,
];
