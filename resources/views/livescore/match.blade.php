@extends('layouts.site')

@php
    $brand = $site['brand_name'] ?? config('app.name');
    $f = $fixture;
    $tz = config('livescore.timezone');
    $kick = $f->localKickoff();
    $dateLong = $kick->copy()->locale('id')->translatedFormat('l, j F Y');
    $leagueName = $f->league->displayName();
    $score = $f->hasScore() ? $f->goals_home.'-'.$f->goals_away : null;

    $pageTitle = match (true) {
        $f->isFinished() => 'Hasil '.$f->home->name.' '.$score.' '.$f->away->name.' — '.$leagueName.' | '.$brand,
        $f->isLive() => 'LIVE '.$f->home->name.' '.$score.' '.$f->away->name.' — Skor Langsung | '.$brand,
        default => $f->title().' — Jadwal, Prediksi Susunan & Live Score ('.$kick->locale('id')->translatedFormat('j M Y').') | '.$brand,
    };
    $pageDesc = match (true) {
        $f->isFinished() => $f->home->name.' vs '.$f->away->name.' berakhir '.$score.' di '.$leagueName.' ('.$dateLong.'). Lihat pencetak gol, kartu, statistik, dan susunan pemain.',
        $f->isLive() => 'Skor langsung '.$f->title().' ('.$leagueName.'): '.$score.' menit '.$f->statusLabel().'. Update gol, kartu, dan statistik pertandingan.',
        default => $f->title().' di '.$leagueName.', '.$dateLong.' pukul '.$kick->format('H:i').' WIB'.($f->venue_name ? ' di '.$f->venue_name : '').'. Ikuti skor langsung dan statistiknya di sini.',
    };

    $events = collect($f->events ?? []);
    $stats = collect($f->statistics ?? []);
    $lineups = collect($f->lineups ?? []);
    $homeStats = collect(data_get($stats->firstWhere('team.id', $f->home->api_id), 'statistics', []))->pluck('value', 'type');
    $awayStats = collect(data_get($stats->firstWhere('team.id', $f->away->api_id), 'statistics', []))->pluck('value', 'type');
    $statLabels = [
        'Ball Possession' => 'Penguasaan bola', 'Total Shots' => 'Total tembakan', 'Shots on Goal' => 'Tembakan ke gawang',
        'Corner Kicks' => 'Tendangan sudut', 'Fouls' => 'Pelanggaran', 'Offsides' => 'Offside',
        'Yellow Cards' => 'Kartu kuning', 'Red Cards' => 'Kartu merah', 'Passes %' => 'Akurasi umpan', 'expected_goals' => 'Expected goals (xG)',
    ];
    $eventStatus = match ($f->status_short) {
        'PST' => 'https://schema.org/EventPostponed',
        'CANC', 'ABD' => 'https://schema.org/EventCancelled',
        default => 'https://schema.org/EventScheduled',
    };
@endphp

@section('title', $pageTitle)
@section('meta_description', $pageDesc)

@push('head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                array_filter([
                    '@type' => 'SportsEvent',
                    'name' => $f->title(),
                    'description' => $pageDesc,
                    'sport' => 'Soccer',
                    'startDate' => $kick->toIso8601String(),
                    'eventStatus' => $eventStatus,
                    'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
                    'url' => route('pertandingan.show', $f),
                    'image' => array_values(array_filter([$f->home->logo, $f->away->logo])) ?: null,
                    'location' => ['@type' => 'Place', 'name' => $f->venue_name ?? $leagueName, 'address' => $f->venue_city ?? $f->league->country ?? ''],
                    'homeTeam' => array_filter(['@type' => 'SportsTeam', 'name' => $f->home->name, 'logo' => $f->home->logo]),
                    'awayTeam' => array_filter(['@type' => 'SportsTeam', 'name' => $f->away->name, 'logo' => $f->away->logo]),
                    'superEvent' => ['@type' => 'SportsEvent', 'name' => $leagueName, 'url' => route('liga.show', $f->league)],
                ]),
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => route('home')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => $leagueName, 'item' => route('liga.show', $f->league)],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $f->title(), 'item' => route('pertandingan.show', $f)],
                    ],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
@endpush

@section('content')
    <nav aria-label="Breadcrumb" class="mb-6 text-xs text-[color:var(--muted)]">
        <ol class="flex flex-wrap items-center gap-2">
            <li><a href="{{ route('home') }}" class="hover:text-[color:var(--gold)]">Beranda</a></li>
            <li aria-hidden="true">/</li>
            <li><a href="{{ route('liga.show', $f->league) }}" class="hover:text-[color:var(--gold)]">{{ $leagueName }}</a></li>
            <li aria-hidden="true">/</li>
            <li aria-current="page" class="truncate text-[color:var(--ink)]">{{ $f->title() }}</li>
        </ol>
    </nav>

    {{-- Papan skor --}}
    <header class="lux-panel relative mb-10 overflow-hidden rounded-[32px] px-5 py-8 sm:px-10 sm:py-12">
        <div class="pointer-events-none absolute inset-x-10 top-0 h-px" style="background: linear-gradient(90deg, transparent, var(--gold), transparent);" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-32 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full blur-3xl" style="background: color-mix(in srgb, var(--secondary) 50%, transparent);" aria-hidden="true"></div>

        <p class="relative text-center text-[10px] uppercase tracking-[0.22em] text-[color:var(--muted)]">
            <a href="{{ route('liga.show', $f->league) }}" class="text-[color:var(--gold)] hover:underline">{{ $leagueName }}</a>
            @if($f->round) · {{ str_replace('Regular Season - ', 'Pekan ', $f->round) }} @endif
        </p>

        <h1 class="sr-only">{{ $f->title() }}{{ $score ? ' '.$score : '' }} — {{ $leagueName }}</h1>

        <div class="relative mt-7 grid grid-cols-[1fr_auto_1fr] items-center gap-3 sm:gap-8">
            <div class="flex min-w-0 flex-col items-center gap-3 text-center">
                @include('livescore.partials.team-logo', ['team' => $f->home, 'size' => 72])
                <p class="w-full font-display text-lg font-semibold leading-tight text-[color:var(--ink)] sm:text-2xl">{{ $f->home->name }}</p>
            </div>

            <div class="text-center">
                @if($f->hasScore())
                    <p class="font-display text-5xl font-semibold tabular-nums leading-none text-[color:var(--ink)] sm:text-7xl">
                        {{ $f->goals_home }}<span class="mx-2 text-[color:var(--muted)] sm:mx-3">:</span>{{ $f->goals_away }}
                    </p>
                @else
                    <p class="font-display text-4xl font-semibold tabular-nums text-[color:var(--ink)] sm:text-6xl">{{ $kick->format('H:i') }}</p>
                @endif
                <p class="mt-3">
                    @if($f->isLive())
                        <span class="live-badge"><span class="live-dot mr-1.5 inline-block h-1.5 w-1.5 rounded-full bg-current align-middle" aria-hidden="true"></span>LIVE {{ $f->statusLabel() }}</span>
                    @elseif($f->isFinished())
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-[color:var(--muted)]">Selesai{{ $f->status_short !== 'FT' ? ' ('.$f->statusLabel().')' : '' }}</span>
                    @else
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-[color:var(--gold)]">{{ $f->isUpcoming() ? 'WIB' : $f->statusLabel() }}</span>
                    @endif
                </p>
                @if(($f->score['halftime']['home'] ?? null) !== null && ! $f->isUpcoming())
                    <p class="mt-1 text-[11px] text-[color:var(--muted)]">Babak I: {{ $f->score['halftime']['home'] }}-{{ $f->score['halftime']['away'] }}</p>
                @endif
                @if($p = $f->penalty())
                    <p class="mt-1 text-[11px] text-[color:var(--gold)]">Adu penalti: {{ $p['home'] }}-{{ $p['away'] }}</p>
                @endif
            </div>

            <div class="flex min-w-0 flex-col items-center gap-3 text-center">
                @include('livescore.partials.team-logo', ['team' => $f->away, 'size' => 72])
                <p class="w-full font-display text-lg font-semibold leading-tight text-[color:var(--ink)] sm:text-2xl">{{ $f->away->name }}</p>
            </div>
        </div>

        <dl class="relative mt-9 flex flex-wrap justify-center gap-x-8 gap-y-2 text-xs text-[color:var(--muted)]">
            <div class="flex items-center gap-2"><dt><i class="far fa-calendar text-[color:var(--gold)]" aria-hidden="true"></i><span class="sr-only">Tanggal</span></dt><dd>{{ $dateLong }}, {{ $kick->format('H:i') }} WIB</dd></div>
            @if($f->venue_name)<div class="flex items-center gap-2"><dt><i class="fas fa-location-dot text-[color:var(--gold)]" aria-hidden="true"></i><span class="sr-only">Stadion</span></dt><dd>{{ $f->venue_name }}{{ $f->venue_city ? ', '.$f->venue_city : '' }}</dd></div>@endif
            @if($f->referee)<div class="flex items-center gap-2"><dt><i class="fas fa-flag text-[color:var(--gold)]" aria-hidden="true"></i><span class="sr-only">Wasit</span></dt><dd>Wasit: {{ $f->referee }}</dd></div>@endif
        </dl>
    </header>

    @if($events->isEmpty() && $stats->isEmpty() && $lineups->isEmpty())
        <div class="lux-panel rounded-3xl px-6 py-12 text-center">
            <i class="far fa-clock mb-3 text-2xl text-[color:var(--gold)]" aria-hidden="true"></i>
            <p class="text-[color:var(--ink)]">{{ $f->isUpcoming() ? 'Pertandingan belum dimulai.' : 'Detail pertandingan sedang dimuat.' }}</p>
            <p class="mt-1 text-sm text-[color:var(--muted)]">Momen penting, statistik, dan susunan pemain akan muncul di sini.</p>
        </div>
    @else
        <div class="grid gap-10 lg:grid-cols-2">
            @if($events->isNotEmpty())
                <section aria-labelledby="events-title" class="min-w-0">
                    <h2 id="events-title" class="mb-4 font-display text-2xl font-semibold text-[color:var(--ink)] sm:text-3xl">Momen penting</h2>
                    <ol class="lux-panel divide-y divide-white/5 overflow-hidden rounded-3xl">
                        @foreach($events as $e)
                            @php
                                $isHome = data_get($e, 'team.id') === $f->home->api_id;
                                $type = $e['type'] ?? '';
                                $detail = $e['detail'] ?? '';
                                [$icon, $label] = match (true) {
                                    $type === 'Goal' && $detail === 'Own Goal' => ['fa-futbol text-rose-300', 'Gol bunuh diri'],
                                    $type === 'Goal' && $detail === 'Penalty' => ['fa-futbol text-[color:var(--gold)]', 'Gol penalti'],
                                    $type === 'Goal' && $detail === 'Missed Penalty' => ['fa-xmark text-rose-300', 'Penalti gagal'],
                                    $type === 'Goal' => ['fa-futbol text-[color:var(--gold)]', 'Gol'],
                                    $type === 'Card' && str_contains($detail, 'Red') => ['fa-square text-rose-500', 'Kartu merah'],
                                    $type === 'Card' && str_contains($detail, 'Second') => ['fa-square text-rose-500', 'Kartu kuning kedua'],
                                    $type === 'Card' => ['fa-square text-amber-300', 'Kartu kuning'],
                                    strtolower($type) === 'subst' => ['fa-right-left text-emerald-300', 'Pergantian'],
                                    $type === 'Var' => ['fa-tv text-sky-300', 'VAR: '.$detail],
                                    default => ['fa-circle text-[color:var(--muted)]', $detail],
                                };
                                $min = ($e['time']['elapsed'] ?? '').(($e['time']['extra'] ?? null) ? '+'.$e['time']['extra'] : '')."'";
                            @endphp
                            <li class="grid grid-cols-[1fr_52px_1fr] items-center gap-2 px-4 py-3 text-sm">
                                <div class="{{ $isHome ? '' : 'invisible' }} min-w-0 text-right">
                                    @if($isHome)
                                        <p class="truncate text-[color:var(--ink)]">{{ data_get($e, 'player.name') }}</p>
                                        <p class="truncate text-[11px] text-[color:var(--muted)]">{{ $label }}{{ data_get($e, 'assist.name') ? ' · '.(strtolower($type) === 'subst' ? 'masuk: ' : 'assist: ').data_get($e, 'assist.name') : '' }}</p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-center">
                                    <i class="fas {{ $icon }} text-xs" aria-hidden="true"></i>
                                    <span class="mt-1 text-[11px] font-semibold tabular-nums text-[color:var(--muted)]">{{ $min }}</span>
                                </div>
                                <div class="{{ $isHome ? 'invisible' : '' }} min-w-0">
                                    @if(! $isHome)
                                        <p class="truncate text-[color:var(--ink)]">{{ data_get($e, 'player.name') }}</p>
                                        <p class="truncate text-[11px] text-[color:var(--muted)]">{{ $label }}{{ data_get($e, 'assist.name') ? ' · '.(strtolower($type) === 'subst' ? 'masuk: ' : 'assist: ').data_get($e, 'assist.name') : '' }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif

            @if($homeStats->isNotEmpty())
                <section aria-labelledby="stats-title" class="min-w-0">
                    <h2 id="stats-title" class="mb-4 font-display text-2xl font-semibold text-[color:var(--ink)] sm:text-3xl">Statistik</h2>
                    <div class="lux-panel space-y-5 rounded-3xl p-5 sm:p-6">
                        @foreach($statLabels as $key => $label)
                            @continue(! $homeStats->has($key))
                            @php
                                $h = $homeStats[$key]; $a = $awayStats[$key] ?? 0;
                                $hn = (float) str_replace('%', '', (string) $h); $an = (float) str_replace('%', '', (string) $a);
                                $sum = $hn + $an; $hp = $sum > 0 ? round($hn / $sum * 100) : 50;
                            @endphp
                            <div>
                                <div class="mb-1.5 flex items-baseline justify-between text-sm">
                                    <span class="font-semibold tabular-nums text-[color:var(--ink)]">{{ $h ?? 0 }}</span>
                                    <span class="text-[11px] uppercase tracking-[0.14em] text-[color:var(--muted)]">{{ $label }}</span>
                                    <span class="font-semibold tabular-nums text-[color:var(--ink)]">{{ $a ?? 0 }}</span>
                                </div>
                                <div class="flex h-1.5 gap-1" aria-hidden="true">
                                    <span class="rounded-full" style="flex: {{ $hp }}; background: var(--gold);"></span>
                                    <span class="rounded-full bg-white/15" style="flex: {{ 100 - $hp }};"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($lineups->count() === 2)
                <section aria-labelledby="lineup-title" class="min-w-0 lg:col-span-2">
                    <h2 id="lineup-title" class="mb-4 font-display text-2xl font-semibold text-[color:var(--ink)] sm:text-3xl">Susunan pemain</h2>
                    <div class="grid gap-5 md:grid-cols-2">
                        @foreach($lineups as $lu)
                            <div class="lux-panel overflow-hidden rounded-3xl">
                                <div class="flex items-center justify-between border-b border-white/5 px-5 py-3">
                                    <p class="text-sm font-semibold text-[color:var(--ink)]">{{ data_get($lu, 'team.name') }}</p>
                                    @if(!empty($lu['formation']))<p class="rounded-full border hairline px-3 py-0.5 text-xs tabular-nums text-[color:var(--gold)]">{{ $lu['formation'] }}</p>@endif
                                </div>
                                <ol class="grid grid-cols-1 gap-x-4 px-5 py-3 text-sm sm:grid-cols-2">
                                    @foreach($lu['startXI'] ?? [] as $p)
                                        <li class="flex items-center gap-3 py-1.5">
                                            <span class="w-6 text-right text-xs font-semibold tabular-nums text-[color:var(--gold)]">{{ data_get($p, 'player.number') }}</span>
                                            <span class="truncate text-[color:var(--ink)]">{{ data_get($p, 'player.name') }}</span>
                                            <span class="ml-auto text-[10px] uppercase text-[color:var(--muted)]">{{ data_get($p, 'player.pos') }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                                @if(data_get($lu, 'coach.name'))
                                    <p class="border-t border-white/5 px-5 py-3 text-xs text-[color:var(--muted)]">Pelatih: <span class="text-[color:var(--ink)]">{{ data_get($lu, 'coach.name') }}</span></p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    @endif

    @if($headToHead->isNotEmpty())
        <section aria-labelledby="h2h-title" class="mt-12">
            <h2 id="h2h-title" class="mb-4 font-display text-2xl font-semibold text-[color:var(--ink)] sm:text-3xl">Pertemuan sebelumnya</h2>
            <ul class="lux-panel divide-y divide-white/5 overflow-hidden rounded-3xl">
                @foreach($headToHead as $h2h)
                    @include('livescore.partials.match-row', ['f' => $h2h, 'showLeague' => true])
                @endforeach
            </ul>
        </section>
    @endif
@endsection

@if($f->isLive())
    @push('scripts')
        <script>setInterval(function () { if (document.visibilityState === 'visible') location.reload(); }, 180000);</script>
    @endpush
@endif
