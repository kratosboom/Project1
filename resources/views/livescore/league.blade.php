@extends('layouts.site')

@php
    $brand = $site['brand_name'] ?? config('app.name');
    $name = $league->displayName();
    $seasonLabel = $league->season ? ($league->type === 'Cup' || in_array($league->api_id, [1, 4], true) ? $league->season : $league->season.'/'.substr((string) ($league->season + 1), -2)) : null;
@endphp

@section('title', $name.($seasonLabel ? ' '.$seasonLabel : '').': Jadwal, Hasil & Klasemen — '.$brand)
@section('meta_description', 'Klasemen terbaru, hasil pertandingan, dan jadwal '.$name.($seasonLabel ? ' musim '.$seasonLabel : '').' lengkap dengan skor langsung di '.$brand.'.')

@push('head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $name, 'item' => route('liga.show', $league)],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
@endpush

@section('content')
    <nav aria-label="Breadcrumb" class="mb-6 text-xs text-[color:var(--muted)]">
        <ol class="flex flex-wrap items-center gap-2">
            <li><a href="{{ route('home') }}" class="hover:text-[color:var(--gold)]">Beranda</a></li>
            <li aria-hidden="true">/</li>
            <li aria-current="page" class="text-[color:var(--ink)]">{{ $name }}</li>
        </ol>
    </nav>

    <header class="lux-panel relative mb-10 overflow-hidden rounded-[32px] px-6 py-10 sm:px-10">
        <div class="pointer-events-none absolute -right-20 -top-24 h-72 w-72 rounded-full blur-3xl" style="background: color-mix(in srgb, var(--secondary) 55%, transparent);" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-center gap-6">
            @if($league->logo)
                <span class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-white p-3 shadow-xl"><img src="{{ $league->logo }}" alt="Logo {{ $name }}" width="56" height="56" class="h-14 w-14 object-contain"></span>
            @endif
            <div>
                <p class="ornament justify-start [&::before]:hidden">{{ $league->country ?? 'Liga' }}{{ $seasonLabel ? ' · Musim '.$seasonLabel : '' }}</p>
                <h1 class="mt-2 font-display text-4xl font-semibold text-[color:var(--ink)] sm:text-5xl">{{ $name }}</h1>
            </div>
        </div>
    </header>

    <div class="grid gap-10 lg:grid-cols-[1.25fr_1fr]">
        <section aria-labelledby="standing-title" class="min-w-0">
            <h2 id="standing-title" class="mb-4 font-display text-2xl font-semibold text-[color:var(--ink)] sm:text-3xl">Klasemen</h2>
            @if($standing)
                @include('livescore.partials.standings', ['standing' => $standing])
                <p class="mt-3 text-xs text-[color:var(--muted)]">Diperbarui {{ $standing->synced_at?->timezone(config('livescore.timezone'))->locale('id')->translatedFormat('j F Y, H:i') }} WIB · M = main, M = menang, S = seri, K = kalah, SG = selisih gol.</p>
            @else
                <div class="lux-panel rounded-3xl px-6 py-10 text-center text-sm text-[color:var(--muted)]">Klasemen belum tersedia.</div>
            @endif
        </section>

        <div class="min-w-0 space-y-10">
            @if($live->isNotEmpty())
                <section aria-labelledby="live-title">
                    <h2 id="live-title" class="mb-4 text-sm font-semibold uppercase tracking-[0.2em] text-[color:var(--live)]">Sedang berlangsung</h2>
                    <ul class="lux-panel divide-y divide-white/5 overflow-hidden rounded-3xl">
                        @foreach($live as $f) @include('livescore.partials.match-row', ['f' => $f]) @endforeach
                    </ul>
                </section>
            @endif

            <section aria-labelledby="upcoming-title">
                <h2 id="upcoming-title" class="mb-4 font-display text-2xl font-semibold text-[color:var(--ink)] sm:text-3xl">Jadwal</h2>
                @if($upcoming->isNotEmpty())
                    <ul class="lux-panel divide-y divide-white/5 overflow-hidden rounded-3xl">
                        @foreach($upcoming as $f)
                            @include('livescore.partials.match-row', ['f' => $f])
                        @endforeach
                    </ul>
                @else
                    <p class="lux-panel rounded-3xl px-6 py-8 text-center text-sm text-[color:var(--muted)]">Belum ada jadwal tersimpan.</p>
                @endif
            </section>

            <section aria-labelledby="results-title">
                <h2 id="results-title" class="mb-4 font-display text-2xl font-semibold text-[color:var(--ink)] sm:text-3xl">Hasil terbaru</h2>
                @if($results->isNotEmpty())
                    <ul class="lux-panel divide-y divide-white/5 overflow-hidden rounded-3xl">
                        @foreach($results as $f)
                            @include('livescore.partials.match-row', ['f' => $f])
                        @endforeach
                    </ul>
                @else
                    <p class="lux-panel rounded-3xl px-6 py-8 text-center text-sm text-[color:var(--muted)]">Belum ada hasil tersimpan.</p>
                @endif
            </section>
        </div>
    </div>
@endsection
