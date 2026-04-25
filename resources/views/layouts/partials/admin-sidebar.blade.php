@php
    $linkBase = 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition';
    $linkIdle = 'text-gray-400 hover:bg-white/5 hover:text-white';
    $linkActive = 'bg-primary/15 text-primary';
    $adminLogo = $site['logo_url'] ?? 'https://asset.t99group.com/assets/logo/TOKYO99/favicon.png';
    $adminBrand = $site['brand_name'] ?? config('app.name');
@endphp

<div class="flex h-full min-h-0 flex-1 flex-col">
    <div class="border-b border-white/5 px-4 py-5">
        <a href="{{ route('admin.dashboard') }}" x-on:click="sidebarOpen = false" class="flex items-center gap-3">
            <img src="{{ $adminLogo }}" alt="" class="h-10 w-10 shrink-0 object-contain" width="40" height="40">
            <div class="min-w-0">
                <p class="truncate text-sm font-extrabold tracking-tight text-white">Panel admin</p>
                <p class="truncate text-xs text-gray-500">{{ $adminBrand }}</p>
            </div>
        </a>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4" aria-label="Menu admin">
        <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-gray-600">Menu</p>

        <a href="{{ route('admin.dashboard') }}" x-on:click="sidebarOpen = false" class="{{ $linkBase }} {{ request()->routeIs('admin.dashboard') ? $linkActive : $linkIdle }}">
            <svg class="h-5 w-5 shrink-0 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
            Ringkasan
        </a>

        <a href="{{ route('admin.pengaturan.edit') }}" x-on:click="sidebarOpen = false" class="{{ $linkBase }} {{ request()->routeIs('admin.pengaturan.*') ? $linkActive : $linkIdle }}">
            <svg class="h-5 w-5 shrink-0 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
            Pengaturan situs
        </a>

        <a href="{{ route('admin.provider.index') }}" x-on:click="sidebarOpen = false" class="{{ $linkBase }} {{ request()->routeIs('admin.provider.*') ? $linkActive : $linkIdle }}">
            <svg class="h-5 w-5 shrink-0 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 2.25L2.25 19.5M6.429 6.75 2.25 3.75l4.179 2.25m0 0 5.571 2.25M15.75 3.75 21.75 3.75M15.75 3.75 12 19.5l-2.25-1.5-2.25 1.5" /></svg>
            Provider game
        </a>

        <a href="{{ route('admin.game.index') }}" x-on:click="sidebarOpen = false" class="{{ $linkBase }} {{ request()->routeIs('admin.game.*') ? $linkActive : $linkIdle }}">
            <svg class="h-5 w-5 shrink-0 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5V18M15 7.5V18M9 7.5V18M3 7.5V18M2.25 3.75h19.5a.75.75 0 0 1 0 1.5H2.25a.75.75 0 0 1 0-1.5Z" /></svg>
            Game &amp; impor
        </a>

        <a href="{{ route('admin.testimoni.index') }}" x-on:click="sidebarOpen = false" class="{{ $linkBase }} {{ request()->routeIs('admin.testimoni.*') ? $linkActive : $linkIdle }}">
            <svg class="h-5 w-5 shrink-0 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.50 48.7 48.7 0 0 0 3.423-.38c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 2.5c-1.256 0-2.49.1-3.7.3-1.584.234-2.707 1.626-2.707 3.23V18" /></svg>
            Testimoni &amp; bukti
        </a>

        <a href="{{ route('admin.halaman.index') }}" x-on:click="sidebarOpen = false" class="{{ $linkBase }} {{ request()->routeIs('admin.halaman.index') || request()->routeIs('admin.halaman.edit') ? $linkActive : $linkIdle }}">
            <svg class="h-5 w-5 shrink-0 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V12M10.5 2.25h5.25c.621 0 1.125.504 1.125 1.125v2.25" /></svg>
            Semua halaman
        </a>

        <a href="{{ route('admin.halaman.create') }}" x-on:click="sidebarOpen = false" class="{{ $linkBase }} {{ request()->routeIs('admin.halaman.create') ? $linkActive : $linkIdle }}">
            <svg class="h-5 w-5 shrink-0 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Halaman baru
        </a>

        <a href="{{ route('admin.users.index') }}" x-on:click="sidebarOpen = false" class="{{ $linkBase }} {{ request()->routeIs('admin.users.*') ? $linkActive : $linkIdle }}">
            <svg class="h-5 w-5 shrink-0 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a8.97 8.97 0 0 0 3.75 1.04m-7.5-1.04a8.97 8.97 0 0 1-3.75 1.04m11.25-1.04a9 9 0 1 0-11.25 0m11.25 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-3.75-.82m7.5 0v-.001M15 11.25a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
            User admin
        </a>

        <p class="mb-2 mt-6 px-3 text-[10px] font-bold uppercase tracking-wider text-gray-600">Situs</p>

        <a href="{{ route('home') }}" x-on:click="sidebarOpen = false" class="{{ $linkBase }} {{ $linkIdle }}">
            <svg class="h-5 w-5 shrink-0 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
            Lihat beranda
        </a>
    </nav>

    <div class="border-t border-white/5 p-3">
        <div class="mb-3 rounded-lg bg-white/5 px-3 py-2">
            <p class="truncate text-xs font-medium text-white">{{ auth()->user()->name ?? 'Pengguna' }}</p>
            <p class="truncate text-[11px] text-gray-500">{{ auth()->user()->email }}</p>
        </div>
        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-3 py-2.5 text-left text-sm font-medium text-gray-400 transition hover:bg-rose-500/10 hover:text-rose-300">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" /></svg>
                Keluar
            </button>
        </form>
    </div>
</div>
