<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $site['brand_name'] ?? config('app.name'))</title>
    <link rel="icon" type="image/png" href="{{ $site['favicon_url'] ?? $site['logo_url'] ?? 'https://asset.t99group.com/assets/logo/TOKYO99/favicon.png' }}">
    @include('layouts.partials.tokyo99-assets')
    @stack('head')
</head>
<body class="min-h-screen">
    @php
        $navBrand = $site['brand_name'] ?? config('app.name');
        $navLogo = $site['logo_url'] ?? 'https://asset.t99group.com/assets/logo/TOKYO99/favicon.png';
        $urlLogin = $site['login_url'] ?? null;
        $urlDaftar = $site['register_url'] ?? null;
        $linkLogin = $urlLogin ? (str_starts_with($urlLogin, 'http') || str_starts_with($urlLogin, '//') ? $urlLogin : url($urlLogin)) : route('login');
        $linkDaftar = $urlDaftar ? (str_starts_with($urlDaftar, 'http') || str_starts_with($urlDaftar, '//') ? $urlDaftar : url($urlDaftar)) : '#';
    @endphp
    <nav class="sticky top-0 z-50 flex h-16 items-center border-b border-white/5 glass px-6">
        <div class="mx-auto flex h-full w-full max-w-7xl items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ $navLogo }}" alt="" class="h-10 w-10 shrink-0 object-contain" width="40" height="40">
                    <span class="text-lg font-extrabold tracking-tight text-white">{{ $navBrand }}</span>
                </a>
                <a href="{{ route('testimoni') }}" class="hidden text-sm text-gray-400 hover:text-white sm:inline">Testimoni</a>
                <a href="{{ route('bukti_jackpot') }}" class="hidden text-sm text-gray-400 hover:text-white sm:inline">Bukti Jackpot</a>
            </div>
            <div class="flex space-x-3">
                <a href="{{ $linkLogin }}" class="rounded-full btn-secondary px-4 py-2 text-sm font-bold sm:px-6">LOGIN</a>
                <a href="{{ $linkDaftar }}" class="rounded-full btn-primary px-4 py-2 text-sm font-bold sm:px-6" @if($linkDaftar === '#') rel="nofollow" onclick="return false;" @endif>DAFTAR</a>
            </div>
        </div>
    </nav>

    <main id="main" class="mx-auto max-w-7xl px-4 py-8">
        @if(session('ok'))
            <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('ok') }}</div>
        @endif
        @yield('content')
    </main>

    <footer class="border-t border-white/5 py-8 text-center text-xs text-gray-500">
        @if(!empty($site['footer_text']))
            <div class="mx-auto max-w-3xl px-4 text-gray-400">{!! nl2br(e($site['footer_text'])) !!}</div>
        @endif
        <p class="mt-2">&copy; {{ date('Y') }} {{ $navBrand }} @if(empty($site['footer_text'])) — {{ __('Beranda; demo lokal') }} @endif</p>
    </footer>
    <script>
        function openSimulator() {}
    </script>
    @stack('scripts')
</body>
</html>
