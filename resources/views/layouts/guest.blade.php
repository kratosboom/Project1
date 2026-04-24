<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="https://asset.t99group.com/assets/logo/TOKYO99/favicon.png">
    @include('layouts.partials.tokyo99-assets')
</head>
<body class="min-h-screen">
    <nav class="sticky top-0 z-50 border-b border-white/5 glass py-4 px-6">
        <div class="mx-auto flex max-w-7xl items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="https://asset.t99group.com/assets/logo/TOKYO99/favicon.png" alt="" class="h-10 w-10 object-contain" width="40" height="40">
                    <span class="text-lg font-extrabold tracking-tight text-white">{{ config('app.name') }}</span>
                </a>
                <a href="{{ route('testimoni') }}" class="hidden text-sm text-gray-400 hover:text-white sm:inline">Testimoni</a>
                <a href="{{ route('bukti_jackpot') }}" class="hidden text-sm text-gray-400 hover:text-white sm:inline">Bukti Jackpot</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="rounded-full btn-secondary px-4 py-2 text-sm font-bold sm:px-6">{{ __('Beranda') }}</a>
            </div>
        </div>
    </nav>

    <main class="mx-auto w-full max-w-md flex-1 px-4 py-10 sm:py-14">
        <div class="glass rounded-2xl border border-white/10 p-6 shadow-xl sm:p-8">
            {{ $slot }}
        </div>
    </main>

    <footer class="border-t border-white/5 py-8 text-center text-xs text-gray-500">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
    </footer>
</body>
</html>
