@extends('layouts.tokyo99')

@php
    $brand = $site['brand_name'] ?? config('app.name');
    $hero = $site['hero_banner_url'] ?? null;

    $footerEngine = trim((string) ($site['maxwin_modal_footer_text'] ?? ''));
    if ($footerEngine === '') {
        $footerEngine = 'Engine powered by brand analytics';
    }
    $kapLabel = trim((string) ($site['maxwin_modal_kapital_label'] ?? ''));
    if ($kapLabel === '') {
        $kapLabel = 'Modal kapital (IDR)';
    }
    $defKapStr = trim((string) ($site['maxwin_default_kapital'] ?? ''));
    $defKap = $defKapStr !== '' ? max(1, (int) $defKapStr) : 50000;

    $resolveCta = static function (?string $raw): array {
        $u = trim((string) $raw);
        if ($u === '') {
            return ['href' => '#', 'enabled' => false, 'target' => null, 'rel' => 'nofollow'];
        }
        if (str_starts_with($u, '//') || str_starts_with($u, 'http://') || str_starts_with($u, 'https://')) {
            $href = $u;
        } else {
            $href = url($u);
        }
        $ext = str_starts_with($href, 'http://') || str_starts_with($href, 'https://') || str_starts_with($href, '//');

        return [
            'href' => $href,
            'enabled' => true,
            'target' => $ext ? '_blank' : null,
            'rel' => $ext ? 'noopener noreferrer' : null,
        ];
    };
    $mainCta = $resolveCta($site['main_sekarang_url'] ?? null);
    $hajarCta = $resolveCta($site['hajar_sekarang_url'] ?? null);

    // Data ringkas untuk hero & SEO (tanpa query tambahan)
    $totalGames = $games->total();
    $totalProviders = $providers->count();
    $topRtp = $games->max('rtp');
    $today = now()->locale('id')->translatedFormat('j F Y');
    $listTitle = (! $gamesShowAll && isset($activeProvider))
        ? 'RTP Slot '.$activeProvider->name.' Hari Ini'
        : 'Daftar RTP Slot Hari Ini';
    $marquee = $site['marquee_text'] ?? __('Selamat datang. Jam & pola hanya contoh tata letak; kelola teks di admin pengaturan.');
@endphp

@section('title', (! $gamesShowAll && isset($activeProvider) ? 'RTP '.$activeProvider->name.' — ' : '').$brand.' — RTP Live, Jam Gacor & Pola Gacor Hari Ini')
@section('meta_description', 'Cek RTP live slot hari ini di '.$brand.': persentase RTP terbaru, jam gacor, dan pola gacor (turbo, auto, manual) dari '.$totalGames.' game & '.$totalProviders.' provider. Diperbarui '.$today.'.')

@push('head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $listTitle.' — '.$brand,
            'numberOfItems' => $games->count(),
            'itemListElement' => $games->values()->map(fn ($g, $i) => [
                '@type' => 'ListItem',
                'position' => $games->firstItem() + $i,
                'name' => $g->name.($g->provider ? ' ('.$g->provider->name.')' : '').' — RTP '.number_format($g->rtp, 2).'%',
            ])->all(),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
@endpush

@section('content')
    {{-- ===================== HERO ===================== --}}
    <section aria-labelledby="hero-title" class="lux-panel relative mb-14 overflow-hidden rounded-[32px]">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -right-24 -top-32 h-96 w-96 rounded-full blur-3xl" style="background: color-mix(in srgb, var(--secondary) 55%, transparent);"></div>
            <div class="absolute -bottom-40 left-1/3 h-80 w-80 rounded-full blur-3xl" style="background: color-mix(in srgb, var(--gold) 14%, transparent);"></div>
            <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(var(--gold) 1px, transparent 1px); background-size: 22px 22px;"></div>
            <div class="absolute inset-x-10 top-0 h-px" style="background: linear-gradient(90deg, transparent, var(--gold), transparent);"></div>
        </div>

        <div class="relative grid gap-10 px-6 py-12 sm:px-12 sm:py-16 lg:grid-cols-[1.35fr_1fr] lg:items-center lg:gap-14 lg:py-20">
            <div>
                <p class="inline-flex items-center gap-2.5 rounded-full border hairline bg-black/25 px-4 py-1.5 text-[11px] font-medium uppercase tracking-[0.22em] text-[color:var(--gold-soft)]">
                    <span class="live-dot inline-block h-1.5 w-1.5 rounded-full bg-[color:var(--gold)]" aria-hidden="true"></span>
                    RTP Live &middot; Diperbarui {{ $today }}
                </p>
                <h1 id="hero-title" class="mt-6 font-display text-[2.6rem] font-semibold leading-[1.02] tracking-tight text-[color:var(--ink)] sm:text-6xl lg:text-7xl">
                    RTP Live &amp; Pola Gacor
                    <span class="gold-text block">{{ $brand }}</span>
                </h1>
                <p class="mt-6 max-w-xl text-[15px] leading-relaxed text-[color:var(--muted)] sm:text-base">
                    Pantau persentase RTP slot terbaru, jam gacor, dan pola spin dari
                    <strong class="font-semibold text-[color:var(--ink)]">{{ number_format($totalGames) }} game</strong> &amp;
                    <strong class="font-semibold text-[color:var(--ink)]">{{ $totalProviders }} provider</strong> pilihan — dirangkum rapi setiap hari.
                </p>
                <div class="mt-9 flex flex-wrap items-center gap-3">
                    <a
                        href="{{ $mainCta['href'] }}"
                        class="btn-primary inline-flex items-center gap-2.5 rounded-full px-8 py-3.5 text-xs uppercase tracking-[0.18em]"
                        @if($mainCta['enabled'])
                            @if(!empty($mainCta['target'])) target="{{ $mainCta['target'] }}" @endif
                            @if(!empty($mainCta['rel'])) rel="{{ $mainCta['rel'] }}" @endif
                        @else
                            rel="nofollow" onclick="return false;"
                        @endif
                    >
                        Main Sekarang <i class="fas fa-arrow-right text-[10px]" aria-hidden="true"></i>
                    </a>
                    <a href="#daftar-rtp" class="btn-secondary inline-flex items-center gap-2.5 rounded-full px-8 py-3.5 text-xs font-semibold uppercase tracking-[0.18em]">
                        Lihat Daftar RTP
                    </a>
                </div>
            </div>

            <dl class="grid grid-cols-3 gap-px overflow-hidden rounded-3xl border hairline bg-[color:var(--line)] lg:grid-cols-1">
                <div class="bg-[color:var(--surface)] px-4 py-5 text-center sm:px-6 lg:flex lg:items-center lg:justify-between lg:py-6 lg:text-left">
                    <dt class="order-2 text-[10px] font-medium uppercase tracking-[0.2em] text-[color:var(--muted)]">Total Game</dt>
                    <dd class="mt-1 font-display text-3xl font-semibold tabular-nums text-[color:var(--ink)] sm:text-4xl lg:mt-0">{{ number_format($totalGames) }}</dd>
                </div>
                <div class="bg-[color:var(--surface)] px-4 py-5 text-center sm:px-6 lg:flex lg:items-center lg:justify-between lg:py-6 lg:text-left">
                    <dt class="order-2 text-[10px] font-medium uppercase tracking-[0.2em] text-[color:var(--muted)]">Provider</dt>
                    <dd class="mt-1 font-display text-3xl font-semibold tabular-nums text-[color:var(--ink)] sm:text-4xl lg:mt-0">{{ $totalProviders }}</dd>
                </div>
                <div class="bg-[color:var(--surface)] px-4 py-5 text-center sm:px-6 lg:flex lg:items-center lg:justify-between lg:py-6 lg:text-left">
                    <dt class="order-2 text-[10px] font-medium uppercase tracking-[0.2em] text-[color:var(--muted)]">RTP Teratas</dt>
                    <dd class="gold-text mt-1 font-display text-3xl font-semibold tabular-nums sm:text-4xl lg:mt-0">{{ $topRtp ? number_format($topRtp, 2).'%' : '—' }}</dd>
                </div>
            </dl>
        </div>

        @if($hero)
            <figure class="relative mx-6 mb-6 overflow-hidden rounded-2xl border hairline sm:mx-12 sm:mb-12">
                <img src="{{ $hero }}" alt="Promo {{ $brand }}" class="h-auto w-full object-cover" width="1200" height="320" fetchpriority="high">
            </figure>
        @endif
    </section>

    {{-- ===================== PROVIDER ===================== --}}
    <section aria-labelledby="provider-title" class="mb-10">
        <div class="mb-5 flex items-end justify-between gap-4">
            <div>
                <p class="ornament justify-start [&::before]:hidden">Koleksi</p>
                <h2 id="provider-title" class="mt-2 font-display text-3xl font-semibold text-[color:var(--ink)] sm:text-4xl">Pilih Provider</h2>
            </div>
        </div>
        <div class="-mx-4 overflow-x-auto px-4 pb-3 custom-scrollbar sm:-mx-6 sm:px-6 md:mx-0 md:px-0">
            <div class="provider-grid pr-4" id="categoryNav" role="tablist" aria-label="Filter provider">
                @if($providers->isNotEmpty())
                    <a
                        href="{{ route('home') }}"
                        class="category-btn font-semibold uppercase whitespace-nowrap {{ $gamesShowAll ? 'active' : '' }}"
                        role="tab"
                        @if($gamesShowAll) aria-current="page" aria-selected="true" @else aria-selected="false" @endif
                    >
                        <i class="fas fa-border-all" aria-hidden="true"></i>
                        Semua
                    </a>
                @endif
                @forelse($providers as $p)
                    @php
                        $isActive = ! ($gamesShowAll ?? false) && isset($activeProvider) && $activeProvider->is($p);
                    @endphp
                    <a
                        href="{{ route('home', ['provider' => $p->slug]) }}"
                        class="category-btn font-semibold uppercase whitespace-nowrap {{ $isActive ? 'active' : '' }}"
                        role="tab"
                        @if($isActive) aria-current="page" aria-selected="true" @else aria-selected="false" @endif
                    >
                        <i class="fas {{ $p->icon_class ?? 'fa-gamepad' }}" aria-hidden="true"></i>
                        {{ $p->navLabel() }}
                    </a>
                @empty
                    <span class="text-xs text-[color:var(--muted)]">Tambah provider di admin.</span>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ===================== DAFTAR RTP ===================== --}}
    <section id="daftar-rtp" aria-labelledby="list-title" class="scroll-mt-24">
        <div class="mb-6">
            <p class="ornament justify-start [&::before]:hidden">Live hari ini</p>
            <h2 id="list-title" class="mt-2 font-display text-3xl font-semibold text-[color:var(--ink)] sm:text-4xl">{{ $listTitle }}</h2>
        </div>

        <div class="mb-8 grid gap-3 lg:grid-cols-[1fr_auto]">
            <div class="group relative">
                <label for="gameSearch" class="sr-only">Cari game</label>
                <i class="fas fa-magnifying-glass pointer-events-none absolute left-5 top-1/2 -translate-y-1/2 text-sm text-[color:var(--muted)] transition-colors group-focus-within:text-[color:var(--gold)]" aria-hidden="true"></i>
                <input type="search" id="gameSearch" placeholder="Cari game berdasarkan nama…" class="lux-input w-full rounded-full py-4 pl-12 pr-5 text-sm placeholder:text-[color:var(--muted)]" autocomplete="off">
            </div>
            <div class="flex items-center gap-3 rounded-full border border-white/10 bg-black/20 py-1.5 pl-5 pr-1.5">
                <label for="gameSort" class="whitespace-nowrap text-[10px] font-medium uppercase tracking-[0.2em] text-[color:var(--muted)]">Urutkan</label>
                <select id="gameSort" class="lux-input min-w-[150px] cursor-pointer rounded-full border-0 py-2.5 pl-4 pr-8 text-xs">
                    <option value="rtp_high">RTP Tertinggi</option>
                    <option value="rtp_low">RTP Terendah</option>
                    <option value="az">Nama A–Z</option>
                    <option value="za">Nama Z–A</option>
                </select>
            </div>
            <div class="ticker relative flex items-center gap-4 overflow-hidden rounded-full border border-white/10 bg-black/20 py-2.5 pl-2.5 pr-4 lg:col-span-2" role="note" aria-label="Informasi">
                <span class="relative z-10 shrink-0 rounded-full bg-[color:var(--gold)] px-3 py-1 text-[9px] font-bold uppercase tracking-[0.2em] text-black">Info</span>
                <div class="min-w-0 flex-1 overflow-hidden [mask-image:linear-gradient(90deg,transparent,#000_6%,#000_94%,transparent)]">
                    <div class="ticker-track whitespace-nowrap text-xs text-[color:var(--muted)]">
                        <span class="pr-16">{{ $marquee }}</span>
                        <span class="pr-16" aria-hidden="true">{{ $marquee }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6" id="gamesGrid">
            @forelse($games as $g)
                @php
                    $pola = $g->pola ?? ['turbo' => '20X', 'auto' => '50X', 'manual' => '100X'];
                    $isBest = $g->is_best;
                    $modal = $g->modal_data ?? [];
                    $mKap = (int) ($modal['kapital'] ?? $defKap);
                    $mPred = (int) ($modal['prediksi'] ?? round($mKap * 38.5 * ($g->rtp / 100)));
                    $mWr = $modal['winrate'] ?? round(min(98.5, (float) $g->rtp - 1.2 + (crc32($g->name) % 7) * 0.15), 1);
                    $mStars = (int) min(5, max(1, $modal['stars'] ?? 5));
                @endphp
                <article class="game-card lux-card group relative flex h-full flex-col overflow-hidden rounded-[22px]" data-name="{{ $g->name }}" data-rtp="{{ $g->rtp }}" data-provider-id="{{ $g->game_provider_id }}" data-is-hot="{{ $g->is_hot ? '1' : '0' }}">
                    <div class="group/image relative m-2 mb-0 aspect-square overflow-hidden rounded-[16px] bg-black/40">
                        @if($isBest)
                            <span class="pointer-events-none absolute left-2 top-2 z-30 inline-flex items-center gap-1 rounded-full border border-[color:var(--gold)]/60 bg-black/70 px-2.5 py-1 text-[9px] font-bold uppercase tracking-[0.16em] text-[color:var(--gold)] backdrop-blur" style="border-color: color-mix(in srgb, var(--gold) 60%, transparent);">
                                <i class="fas fa-crown text-[8px]" aria-hidden="true"></i> Best
                            </span>
                        @elseif($g->is_hot)
                            <span class="pointer-events-none absolute left-2 top-2 z-30 inline-flex items-center gap-1 rounded-full bg-[color:var(--gold)] px-2.5 py-1 text-[9px] font-bold uppercase tracking-[0.16em] text-black shadow-lg">
                                <i class="fas fa-fire text-[8px]" aria-hidden="true"></i> Hot
                            </span>
                        @endif
                        <img src="{{ $g->image_url }}" alt="Slot {{ $g->name }}{{ $g->provider ? ' — '.$g->provider->name : '' }}" loading="lazy" decoding="async" width="400" height="400" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" onerror="this.onerror=null;this.src='https://placehold.co/400x400/0b1f18/d4af37?text={{ urlencode($g->name) }}'">
                        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute inset-0 hidden items-center justify-center bg-black/55 opacity-0 backdrop-blur-[2px] transition-opacity duration-300 group-hover/image:opacity-100 md:flex">
                            <a
                                href="{{ $mainCta['href'] }}"
                                class="btn-primary translate-y-3 rounded-full px-6 py-2.5 text-[10px] uppercase tracking-[0.18em] transition-transform duration-300 group-hover/image:translate-y-0"
                                @if($mainCta['enabled'])
                                    @if(!empty($mainCta['target'])) target="{{ $mainCta['target'] }}" @endif
                                    @if(!empty($mainCta['rel'])) rel="{{ $mainCta['rel'] }}" @endif
                                @else
                                    rel="nofollow"
                                    onclick="return false;"
                                @endif
                            >Main Sekarang</a>
                        </div>
                    </div>

                    <div class="flex min-h-0 flex-1 flex-col px-3.5 pb-3.5 pt-3.5 sm:px-4 sm:pb-4">
                        <h3 class="truncate text-[13px] font-semibold uppercase tracking-wide text-[color:var(--ink)]" title="{{ $g->name }}">{{ $g->name }}</h3>
                        <p class="mt-0.5 text-[10px] font-medium uppercase tracking-[0.18em] text-[color:var(--gold)]/90" style="color: color-mix(in srgb, var(--gold) 85%, transparent);">{{ $g->provider?->name ?? '—' }}</p>

                        <div class="mt-4">
                            <div class="mb-1.5 flex items-baseline justify-between">
                                <span class="text-[9px] font-medium uppercase tracking-[0.2em] text-[color:var(--muted)]">RTP</span>
                                <span class="text-sm font-semibold tabular-nums text-[color:var(--ink)]">{{ number_format($g->rtp, 2) }}%</span>
                            </div>
                            <div class="relative h-1.5 overflow-hidden rounded-full bg-white/[0.06]" role="meter" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $g->rtp }}" aria-label="RTP {{ $g->name }}">
                                <div class="bar-fill-anim relative h-full overflow-hidden rounded-full" style="width: {{ min(100, $g->rtp) }}%; background: linear-gradient(90deg, var(--secondary), var(--gold));">
                                    <div class="shimmer-effect absolute inset-0 h-full w-full"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 rounded-xl border border-white/[0.06] bg-black/20 px-3 py-2 text-center">
                            <p class="flex items-center justify-center gap-1.5 text-[8px] font-medium uppercase tracking-[0.2em] text-[color:var(--muted)]"><i class="far fa-clock text-[color:var(--gold)]" aria-hidden="true"></i> Jam gacor</p>
                            <p class="mt-0.5 whitespace-nowrap text-xs font-semibold tabular-nums text-[color:var(--ink)]">{{ $g->jam_gacor }}</p>
                        </div>

                        <dl class="mt-2 grid grid-cols-3 gap-1.5 text-center">
                            <div class="rounded-lg bg-white/[0.03] px-1 py-1.5">
                                <dt class="text-[8px] uppercase tracking-[0.14em] text-[color:var(--muted)]">Turbo</dt>
                                <dd class="text-[11px] font-semibold text-[color:var(--ink)]">{{ $pola['turbo'] ?? '—' }}</dd>
                            </div>
                            <div class="rounded-lg bg-white/[0.03] px-1 py-1.5">
                                <dt class="text-[8px] uppercase tracking-[0.14em] text-[color:var(--muted)]">Auto</dt>
                                <dd class="text-[11px] font-semibold text-[color:var(--ink)]">{{ $pola['auto'] ?? '—' }}</dd>
                            </div>
                            <div class="rounded-lg bg-white/[0.03] px-1 py-1.5">
                                <dt class="text-[8px] uppercase tracking-[0.14em] text-[color:var(--muted)]">Manual</dt>
                                <dd class="text-[11px] font-semibold text-[color:var(--ink)]">{{ $pola['manual'] ?? '—' }}</dd>
                            </div>
                        </dl>

                        <button
                            type="button"
                            class="js-analisis-maxwin btn-secondary mt-4 flex w-full cursor-pointer items-center justify-center gap-2 rounded-full py-2.5 text-[9px] font-semibold uppercase tracking-[0.18em] active:scale-[0.98] sm:text-[10px]"
                            data-name="{{ $g->name }}"
                            data-rtp="{{ $g->rtp }}"
                            data-kapital="{{ $mKap }}"
                            data-prediksi="{{ $mPred }}"
                            data-winrate="{{ $mWr }}"
                            data-stars="{{ $mStars }}"
                        >
                            <i class="fas fa-chart-line text-[color:var(--gold)]" aria-hidden="true"></i>
                            Analisis Maxwin
                        </button>
                    </div>
                </article>
            @empty
                <div class="lux-panel col-span-full rounded-3xl px-6 py-14 text-center text-sm text-[color:var(--muted)]">
                    <i class="fas fa-gem mb-4 text-2xl text-[color:var(--gold)]" aria-hidden="true"></i>
                    @if (! $gamesShowAll && isset($activeProvider))
                        <p>Belum ada game untuk provider <strong class="text-[color:var(--ink)]">{{ $activeProvider->name }}</strong>.</p>
                        <p class="mx-auto mt-3 max-w-md text-xs">
                            Kalau baru scrape/impor, sering ada <strong class="text-[color:var(--ink)]">dua baris provider</strong> (mis. <code>microgaming</code> vs <code>micro-gaming</code>) — game terpasang ke salah satu saja. Gabungkan atau hapus provider kosong di Admin → Provider.
                        </p>
                    @else
                        <p>Belum ada game. Tambah dari admin → Game, atau jalankan <code>php artisan db:seed --class=SiteContentSeeder</code>.</p>
                    @endif
                </div>
            @endforelse
        </div>

        @if ($games->hasPages())
            <div class="mt-12 w-full">
                {{ $games->onEachSide(1)->links('vendor.pagination.tokyo99') }}
            </div>
        @endif
    </section>

    {{-- Modal Analisis Maxwin --}}
    <div
        id="maxwinModal"
        class="fixed inset-0 z-[200] hidden items-end justify-center p-0 sm:items-center sm:p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="maxwinModalTitle"
        aria-hidden="true"
    >
        <button type="button" class="js-maxwin-backdrop absolute inset-0 bg-black/80 backdrop-blur-sm" aria-label="Tutup"></button>
        <div class="lux-panel relative z-10 flex max-h-[min(100dvh,720px)] w-full max-w-md flex-col overflow-y-auto rounded-t-[28px] p-6 sm:rounded-[28px] sm:p-7">
            <div class="pointer-events-none absolute inset-x-10 top-0 h-px" style="background: linear-gradient(90deg, transparent, var(--gold), transparent);" aria-hidden="true"></div>
            <div class="relative mb-6 pr-10 text-left">
                <button
                    type="button"
                    class="js-maxwin-close absolute right-0 top-0 flex h-9 w-9 items-center justify-center rounded-full border hairline text-[color:var(--ink)] transition hover:bg-white/5"
                    aria-label="Tutup"
                >
                    <i class="fas fa-xmark text-sm" aria-hidden="true"></i>
                </button>
                <p class="text-[10px] font-medium uppercase tracking-[0.24em] text-[color:var(--gold)]">Analisis Maxwin</p>
                <p id="maxwinModalTitle" class="mt-2 font-display text-2xl font-semibold leading-tight text-[color:var(--ink)]">
                    <span id="maxwinGameTitle">—</span>
                </p>
                <p id="maxwinRtpLine" class="mt-2 text-xs font-medium uppercase tracking-[0.16em] text-[color:var(--muted)]">
                    Realtime RTP live: <span class="font-semibold text-[color:var(--gold)]"><span id="maxwinRtpVal">0.00</span>%</span>
                </p>
            </div>

            <div class="mb-3 rounded-2xl border border-white/[0.06] bg-black/25 px-5 py-4">
                <p class="text-[10px] font-medium uppercase tracking-[0.2em] text-[color:var(--muted)]">{{ $kapLabel }}</p>
                <p id="maxwinKapital" class="mt-1 font-display text-3xl font-semibold tabular-nums text-[color:var(--ink)]">0</p>
            </div>

            <div class="mb-3 flex items-center justify-between gap-3 rounded-2xl border border-white/[0.06] bg-black/25 px-5 py-4">
                <div>
                    <p class="text-[10px] font-medium uppercase tracking-[0.2em] text-[color:var(--muted)]">Prediksi kemenangan</p>
                    <p id="maxwinPrediksi" class="gold-text mt-1 font-display text-3xl font-semibold tabular-nums">IDR 0</p>
                </div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border hairline text-[color:var(--gold)]">
                    <i class="fas fa-coins" aria-hidden="true"></i>
                </span>
            </div>

            <div class="mb-6 grid grid-cols-2 gap-3">
                <div class="rounded-2xl border border-white/[0.06] bg-black/25 p-4 text-center">
                    <p class="text-[9px] font-medium uppercase tracking-[0.2em] text-[color:var(--muted)]">Win rate</p>
                    <p id="maxwinWinrate" class="mt-1 text-xl font-semibold tabular-nums text-[color:var(--ink)]">0%</p>
                </div>
                <div class="rounded-2xl border border-white/[0.06] bg-black/25 p-4 text-center">
                    <p class="text-[9px] font-medium uppercase tracking-[0.2em] text-[color:var(--muted)]">Difficulty</p>
                    <div id="maxwinStars" class="mt-2 flex justify-center gap-0.5" aria-hidden="true"></div>
                </div>
            </div>

            <a
                href="{{ $hajarCta['href'] }}"
                class="js-maxwin-cta btn-primary mb-5 flex w-full items-center justify-center gap-2 rounded-full py-4 text-xs uppercase tracking-[0.2em]"
                @if($hajarCta['enabled'])
                    @if(!empty($hajarCta['target'])) target="{{ $hajarCta['target'] }}" @endif
                    @if(!empty($hajarCta['rel'])) rel="{{ $hajarCta['rel'] }}" @endif
                @else
                    rel="nofollow"
                    onclick="return false;"
                @endif
            >
                <i class="fas fa-bolt" aria-hidden="true"></i>
                Hajar Sekarang
            </a>

            <p class="text-center text-[9px] font-medium uppercase tracking-[0.2em] text-[color:var(--muted)]">
                {{ $footerEngine }}
            </p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const input = document.getElementById('gameSearch');
            const grid = document.getElementById('gamesGrid');
            if (!input || !grid) return;
            input.addEventListener('input', function () {
                const q = (this.value || '').toLowerCase().trim();
                grid.querySelectorAll('.game-card').forEach(function (card) {
                    const name = (card.getAttribute('data-name') || '').toLowerCase();
                    card.style.display = !q || name.indexOf(q) !== -1 ? '' : 'none';
                });
            });
            const sort = document.getElementById('gameSort');
            if (sort) {
                sort.addEventListener('change', function () {
                    const cards = Array.from(grid.querySelectorAll('.game-card'));
                    const mode = this.value;
                    cards.sort(function (a, b) {
                        const ra = parseFloat(a.getAttribute('data-rtp') || 0);
                        const rb = parseFloat(b.getAttribute('data-rtp') || 0);
                        const na = (a.getAttribute('data-name') || '').toLowerCase();
                        const nb = (b.getAttribute('data-name') || '').toLowerCase();
                        if (mode === 'rtp_high') return rb - ra;
                        if (mode === 'rtp_low') return ra - rb;
                        if (mode === 'az') return na.localeCompare(nb);
                        if (mode === 'za') return nb.localeCompare(na);
                        return 0;
                    });
                    cards.forEach(function (c) { grid.appendChild(c); });
                });
            }
        })();
        (function () {
            const modal = document.getElementById('maxwinModal');
            if (!modal) return;

            function fmtIdr(n) {
                const num = Math.max(0, parseInt(n, 10) || 0);
                return 'IDR ' + new Intl.NumberFormat('id-ID').format(num);
            }

            function openMaxwin(d) {
                const titleEl = document.getElementById('maxwinGameTitle');
                const rtpVal = document.getElementById('maxwinRtpVal');
                const kapitalEl = document.getElementById('maxwinKapital');
                const prediksiEl = document.getElementById('maxwinPrediksi');
                const winrateEl = document.getElementById('maxwinWinrate');
                const starsEl = document.getElementById('maxwinStars');
                if (!titleEl || !rtpVal || !kapitalEl || !prediksiEl || !winrateEl || !starsEl) return;

                const rtp = parseFloat(d.rtp, 10);
                titleEl.textContent = (d.name || '—').toUpperCase();
                rtpVal.textContent = (isNaN(rtp) ? 0 : rtp).toFixed(2);
                kapitalEl.textContent = new Intl.NumberFormat('id-ID').format(parseInt(d.kapital, 10) || 0);
                prediksiEl.textContent = fmtIdr(d.prediksi);
                const wr = parseFloat(d.winrate, 10);
                winrateEl.textContent = (isNaN(wr) ? 0 : wr).toFixed(1) + '%';

                const stars = Math.min(5, Math.max(1, parseInt(d.stars, 10) || 5));
                starsEl.innerHTML = '';
                for (var s = 0; s < 5; s++) {
                    var icon = document.createElement('i');
                    icon.setAttribute('aria-hidden', 'true');
                    icon.className = 'fas fa-star text-sm';
                    icon.style.color = s < stars ? 'var(--gold)' : 'rgba(255,255,255,.15)';
                    starsEl.appendChild(icon);
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }

            function closeMaxwin() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }

            document.querySelectorAll('.js-analisis-maxwin').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    openMaxwin(btn.dataset);
                });
            });

            modal.querySelectorAll('.js-maxwin-close, .js-maxwin-backdrop').forEach(function (el) {
                el.addEventListener('click', closeMaxwin);
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeMaxwin();
            });
        })();
    </script>
@endpush
