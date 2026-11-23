@php
    $navBrand = $site['brand_name'] ?? config('app.name');
    $navLogo = $site['logo_url'] ?? asset('favicon.ico');

    $seoTitle = trim($__env->yieldContent('title')) ?: $navBrand;
    $seoDesc = trim($__env->yieldContent('meta_description'))
        ?: $navBrand.' — skor langsung, jadwal, dan hasil pertandingan sepak bola terkini.';
    $seoImage = $navLogo;
    $seoUrl = url()->full();

    $navLinks = collect([
        ['label' => 'Beranda', 'href' => route('home'), 'active' => request()->routeIs('home')],
    ])->merge(($navPages ?? collect())->map(fn ($p) => [
        'label' => $p->title,
        'href' => route('halaman.show', $p->slug),
        'active' => request()->is('halaman/'.$p->slug),
    ]));
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDesc }}">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <link rel="canonical" href="{{ $seoUrl }}">
    <meta name="theme-color" content="{{ $theme['bg'] ?? '#111827' }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="{{ $navBrand }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDesc }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDesc }}">
    <meta name="twitter:image" content="{{ $seoImage }}">
    <link rel="icon" href="{{ $site['favicon_url'] ?? $site['logo_url'] ?? asset('favicon.ico') }}">
    @include('layouts.partials.site-assets')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                ['@type' => 'Organization', '@id' => url('/').'#organization', 'name' => $navBrand, 'url' => url('/'), 'logo' => $navLogo],
                ['@type' => 'WebSite', '@id' => url('/').'#website', 'name' => $navBrand, 'url' => url('/'), 'inLanguage' => 'id-ID', 'publisher' => ['@id' => url('/').'#organization']],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
    @stack('head')
</head>
<body class="flex min-h-screen flex-col">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[300] focus:rounded-lg focus:bg-black focus:px-4 focus:py-2">Langsung ke konten</a>

    <header class="glass sticky top-0 z-50 border-x-0 border-t-0 hairline">
        <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between gap-4 px-4 sm:h-[72px] sm:px-6">
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3" aria-label="{{ $navBrand }} — Beranda">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border hairline bg-black/30 p-1.5">
                    <img src="{{ $navLogo }}" alt="Logo {{ $navBrand }}" class="h-full w-full object-contain" width="40" height="40">
                </span>
                <span class="truncate font-display text-xl font-semibold leading-none tracking-wide text-[color:var(--ink)] sm:text-2xl">{{ $navBrand }}</span>
            </a>
            <nav aria-label="Navigasi utama" class="hidden md:block">
                <ul class="flex items-center gap-7">
                    @foreach($navLinks as $link)
                        <li>
                            <a href="{{ $link['href'] }}" class="relative py-2 text-[13px] font-medium tracking-wide transition-colors {{ $link['active'] ? 'text-[color:var(--gold)]' : 'text-[color:var(--muted)] hover:text-[color:var(--ink)]' }}" @if($link['active']) aria-current="page" @endif>
                                {{ $link['label'] }}
                                @if($link['active'])<span class="absolute -bottom-0.5 left-0 right-0 mx-auto h-px w-6 bg-[color:var(--gold)]"></span>@endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
            <details class="relative md:hidden" id="mobileNav">
                <summary class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border hairline text-[color:var(--ink)]" aria-label="Buka menu">
                    <i class="fas fa-bars text-sm" aria-hidden="true"></i>
                </summary>
                <nav aria-label="Navigasi seluler" class="lux-panel absolute right-0 top-12 w-60 rounded-2xl p-2" style="background: var(--nav);">
                    <ul>
                        @foreach($navLinks as $link)
                            <li>
                                <a href="{{ $link['href'] }}" class="flex items-center justify-between rounded-xl px-4 py-3 text-sm {{ $link['active'] ? 'bg-white/5 text-[color:var(--gold)]' : 'text-[color:var(--ink)] hover:bg-white/5' }}" @if($link['active']) aria-current="page" @endif>
                                    {{ $link['label'] }}
                                    <i class="fas fa-chevron-right text-[10px] opacity-40" aria-hidden="true"></i>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </details>
        </div>
    </header>

    <main id="main" class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 sm:py-10">
        @if(session('ok'))
            <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200" role="status">{{ session('ok') }}</div>
        @endif
        @yield('content')
    </main>

    <footer class="mt-16 border-t hairline bg-black/20">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-[1.4fr_1fr]">
            <div>
                <a href="{{ route('home') }}" class="font-display text-3xl font-semibold text-[color:var(--ink)]">{{ $navBrand }}</a>
                @if(!empty($site['footer_text']))
                    <p class="mt-4 max-w-xl text-sm leading-relaxed text-[color:var(--muted)]">{!! nl2br(e($site['footer_text'])) !!}</p>
                @endif
            </div>
            <nav aria-label="Tautan footer">
                <p class="ornament mb-4 justify-start [&::before]:hidden">Jelajahi</p>
                <ul class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    @foreach($navLinks as $link)
                        <li><a href="{{ $link['href'] }}" class="text-[color:var(--muted)] transition-colors hover:text-[color:var(--gold)]">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>
        </div>
        <div class="border-t border-white/5">
            <p class="mx-auto max-w-7xl px-4 py-5 text-center text-xs tracking-wide text-[color:var(--muted)] sm:px-6">&copy; {{ date('Y') }} {{ $navBrand }}. Seluruh hak cipta dilindungi.</p>
        </div>
    </footer>
    <script>
        (function () {
            var d = document.getElementById('mobileNav');
            if (!d) return;
            document.addEventListener('click', function (e) { if (d.open && !d.contains(e.target)) d.open = false; });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') d.open = false; });
        })();
    </script>
    @stack('scripts')
</body>
</html>
