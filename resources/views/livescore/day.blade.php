@extends('layouts.site')

@php
    $brand = $site['brand_name'] ?? config('app.name');
    $dateLong = $day->copy()->locale('id')->translatedFormat('l, j F Y');
    $dateShort = $day->copy()->locale('id')->translatedFormat('j F Y');
    $pageTitle = $isToday
        ? $brand.' — Skor Langsung Sepak Bola Hari Ini, Jadwal & Hasil'
        : 'Jadwal & Hasil Sepak Bola '.$dateShort.' — '.$brand;
    $pageDesc = $isToday
        ? 'Skor langsung sepak bola hari ini '.$dateShort.': '.$total.' pertandingan dari Liga Primer, La Liga, Serie A, Bundesliga, Liga 1 dan lainnya. Hasil, jadwal kickoff WIB & klasemen.'
        : 'Jadwal dan hasil pertandingan sepak bola '.$dateLong.': '.$total.' pertandingan, skor akhir, jam kickoff WIB, dan detail tiap laga.';
@endphp

@section('title', $pageTitle)
@section('meta_description', $pageDesc)

@if($total > 0)
    @push('head')
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'ItemList',
                'name' => ($isToday ? 'Pertandingan sepak bola hari ini' : 'Pertandingan sepak bola '.$dateShort),
                'itemListElement' => $groups->flatten(1)->values()->map(fn ($f, $i) => [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'url' => route('pertandingan.show', $f),
                    'name' => $f->title(),
                ])->all(),
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
        </script>
    @endpush
@endif

@section('content')
    <header class="mb-8">
        <p class="ornament justify-start [&::before]:hidden">{{ $isToday ? 'Live score' : 'Jadwal & hasil' }}</p>
        <div class="mt-3 flex flex-wrap items-end justify-between gap-4">
            <h1 class="font-display text-4xl font-semibold leading-tight text-[color:var(--ink)] sm:text-5xl">
                @if($isToday)
                    Skor Langsung <span class="gold-text">Hari Ini</span>
                @else
                    Sepak Bola <span class="gold-text">{{ $dateShort }}</span>
                @endif
            </h1>
            <p class="text-xs text-[color:var(--muted)]">
                {{ $dateLong }}
                @if($lastSynced) · Diperbarui {{ $lastSynced->timezone(config('livescore.timezone'))->format('H:i') }} WIB @endif
            </p>
        </div>
    </header>

    <div class="mb-8">@include('livescore.partials.date-tabs', ['day' => $day])</div>

    @if($isToday && $live->isNotEmpty())
        <section aria-labelledby="live-title" class="mb-10">
            <h2 id="live-title" class="mb-4 flex items-center gap-2.5 text-sm font-semibold uppercase tracking-[0.2em] text-[color:var(--live)]">
                <span class="live-dot inline-block h-2 w-2 rounded-full bg-[color:var(--live)]" aria-hidden="true"></span>
                Sedang berlangsung · {{ $live->count() }}
            </h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($live as $f)
                    <a href="{{ route('pertandingan.show', $f) }}" class="lux-card group block rounded-3xl p-5">
                        <div class="mb-4 flex items-center justify-between gap-2 text-[10px] uppercase tracking-[0.16em] text-[color:var(--muted)]">
                            <span class="truncate">{{ $f->league->displayName() }}</span>
                            <span class="live-badge">{{ $f->statusLabel() }}</span>
                        </div>
                        <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3 text-center">
                            <span class="flex min-w-0 flex-col items-center gap-2">
                                @include('livescore.partials.team-logo', ['team' => $f->home, 'size' => 40])
                                <span class="w-full truncate text-sm text-[color:var(--ink)]">{{ $f->home->name }}</span>
                            </span>
                            <span class="font-display text-4xl font-semibold tabular-nums text-[color:var(--ink)]">{{ $f->goals_home ?? 0 }}<span class="mx-1.5 text-[color:var(--muted)]">:</span>{{ $f->goals_away ?? 0 }}</span>
                            <span class="flex min-w-0 flex-col items-center gap-2">
                                @include('livescore.partials.team-logo', ['team' => $f->away, 'size' => 40])
                                <span class="w-full truncate text-sm text-[color:var(--ink)]">{{ $f->away->name }}</span>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section aria-labelledby="list-title">
        <div class="mb-4 flex items-baseline justify-between gap-3">
            <h2 id="list-title" class="font-display text-2xl font-semibold text-[color:var(--ink)] sm:text-3xl">
                {{ $isToday ? 'Semua pertandingan hari ini' : 'Semua pertandingan' }}
            </h2>
            @if($total)<p class="text-xs text-[color:var(--muted)]">{{ $total }} laga · {{ $finished }} selesai</p>@endif
        </div>

        @forelse($groups as $items)
            <article class="lux-panel mb-5 overflow-hidden rounded-3xl">
                @include('livescore.partials.league-heading', ['league' => $items->first()->league])
                <ul class="divide-y divide-white/5">
                    @foreach($items as $f)
                        @include('livescore.partials.match-row', ['f' => $f])
                    @endforeach
                </ul>
            </article>
        @empty
            <div class="lux-panel rounded-3xl px-6 py-16 text-center">
                <i class="far fa-futbol mb-4 text-3xl text-[color:var(--gold)]" aria-hidden="true"></i>
                <p class="text-[color:var(--ink)]">Belum ada pertandingan dari liga pilihan pada tanggal ini.</p>
                <p class="mt-2 text-sm text-[color:var(--muted)]">Coba tanggal lain di atas, atau lihat klasemen liga di bawah.</p>
            </div>
        @endforelse
    </section>

    @if($navLeagues->isNotEmpty())
        <section aria-labelledby="leagues-title" class="mt-14">
            <h2 id="leagues-title" class="mb-4 font-display text-2xl font-semibold text-[color:var(--ink)] sm:text-3xl">Liga populer</h2>
            <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($navLeagues as $l)
                    <li>
                        <a href="{{ route('liga.show', $l) }}" class="lux-card flex items-center gap-3 rounded-2xl px-4 py-3">
                            @if($l->logo)<span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white p-1"><img src="{{ $l->logo }}" alt="" width="22" height="22" loading="lazy" class="h-[22px] w-[22px] object-contain"></span>@endif
                            <span class="truncate text-sm text-[color:var(--ink)]">{{ $l->displayName() }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
@endsection

@if($isToday && $live->isNotEmpty())
    @push('scripts')
        <script>
            // Muat ulang ringan saat ada laga live & tab terlihat (data server diperbarui oleh scheduler).
            setInterval(function () { if (document.visibilityState === 'visible') location.reload(); }, 180000);
        </script>
    @endpush
@endif
