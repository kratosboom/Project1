<nav x-data="{ open: false }" class="relative z-20 border-b border-white/5 glass">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <div class="flex h-16 items-center justify-between">
            <div class="flex min-w-0 items-center gap-4 sm:gap-6">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2 text-white">
                    <img src="https://asset.t99group.com/assets/logo/TOKYO99/favicon.png" alt="" class="h-9 w-9 shrink-0 object-contain" width="36" height="36">
                    <span class="truncate text-base font-extrabold tracking-tight sm:text-lg">{{ config('app.name') }}</span>
                </a>
                <a href="{{ route('home') }}" class="hidden text-sm text-gray-400 hover:text-white sm:inline">Beranda</a>
                <a href="{{ route('testimoni') }}" class="hidden text-sm text-gray-400 hover:text-white sm:inline">Testimoni</a>
                <a href="{{ route('bukti_jackpot') }}" class="hidden text-sm text-gray-400 hover:text-white sm:inline" title="Bukti Jackpot">Bukti Jackpot</a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="hidden text-sm text-gray-400 hover:text-white sm:inline @if(request()->routeIs('admin.*')) text-primary @endif">Panel</a>
                @endauth
            </div>
            <div class="hidden items-center gap-2 sm:flex">
                @auth
                    <a href="{{ route('admin.halaman.create') }}" class="rounded-full bg-primary px-4 py-2 text-sm font-bold text-black shadow-lg shadow-primary/25 transition hover:brightness-110">+ Halaman Baru</a>
                    <form method="post" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="rounded-lg px-3 py-2 text-sm text-gray-400 transition hover:bg-white/5 hover:text-white">Keluar</button>
                    </form>
                @endauth
            </div>
            <div class="flex sm:hidden">
                <button @click="open = ! open" type="button" class="inline-flex items-center justify-center rounded-lg p-2 text-gray-300 hover:bg-white/5 hover:text-white" aria-label="Menu">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-white/5 sm:hidden">
        <div class="space-y-1 px-4 py-3">
            <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 text-gray-300 hover:bg-white/5">Beranda</a>
            <a href="{{ route('testimoni') }}" class="block rounded-lg px-3 py-2 text-gray-300 hover:bg-white/5">Testimoni</a>
            <a href="{{ route('bukti_jackpot') }}" class="block rounded-lg px-3 py-2 text-gray-300 hover:bg-white/5">Bukti Jackpot</a>
            @auth
                <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 text-gray-300 hover:bg-white/5">Panel</a>
                <a href="{{ route('admin.halaman.create') }}" class="block rounded-lg px-3 py-2 font-bold text-primary">+ Halaman Baru</a>
                <form method="post" action="{{ route('logout') }}" class="px-3 pt-2">
                    @csrf
                    <button type="submit" class="text-left text-gray-400">Keluar</button>
                </form>
            @endauth
        </div>
    </div>
</nav>
