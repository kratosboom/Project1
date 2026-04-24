<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel — '.($site['brand_name'] ?? config('app.name')))</title>
    <link rel="icon" type="image/png" href="{{ $site['favicon_url'] ?? $site['logo_url'] ?? 'https://asset.t99group.com/assets/logo/TOKYO99/favicon.png' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.documentElement.classList.add('dark');
        });
    </script>
    <style>
        :root {
            --primary: {{ $theme['primary'] ?? '#fb2323' }};
            --secondary: {{ $theme['secondary'] ?? '#b30000' }};
            --bg: {{ $theme['bg'] ?? '#111827' }};
            --nav: {{ $theme['nav'] ?? '#1f2937' }};
        }
        body { font-family: 'Outfit', system-ui, sans-serif; }
        .ink-bg { background: var(--bg); }
        .bg-hero { background-image: radial-gradient(ellipse 100% 80% at 50% -40%, color-mix(in srgb, var(--primary) 20%, transparent), transparent 55%), radial-gradient(ellipse 60% 50% at 100% 0%, color-mix(in srgb, var(--secondary) 14%, transparent), transparent 50%), radial-gradient(ellipse 50% 40% at 0% 100%, rgba(55, 65, 81, 0.25), transparent 50%); }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .bg-primary { background-color: var(--primary) !important; }
        .text-primary { color: var(--primary) !important; }
        .border-primary\/30 { border-color: color-mix(in srgb, var(--primary) 30%, transparent) !important; }
        .border-primary\/50 { border-color: color-mix(in srgb, var(--primary) 50%, transparent) !important; }
        .ring-primary\/30 { --tw-ring-color: color-mix(in srgb, var(--primary) 30%, transparent) !important; }
        .hover\:brightness-110:hover { filter: brightness(1.1) saturate(1.03); }
    </style>
    @stack('head')
</head>
<body class="min-h-full bg-[#111827] font-sans text-gray-100 antialiased ink-bg bg-hero" x-data="{ sidebarOpen: false }">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -left-32 top-20 h-72 w-72 rounded-full bg-primary/15 blur-3xl"></div>
        <div class="absolute -right-20 bottom-10 h-96 w-96 rounded-full bg-primary/20 blur-3xl"></div>
    </div>

    <div
        x-show="sidebarOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-black/60 md:hidden"
        x-on:click="sidebarOpen = false"
    ></div>

    <aside
        id="admin-sidebar"
        class="fixed inset-y-0 left-0 z-50 flex w-64 max-w-[85vw] flex-col border-r border-white/5 bg-[#0c1017]/95 shadow-2xl backdrop-blur-xl transition duration-200 ease-out md:shadow-none"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
    >
        @include('layouts.partials.admin-sidebar')
    </aside>

    <div class="flex min-h-screen flex-col md:pl-64">
        <header class="sticky top-0 z-30 flex h-14 items-center justify-between border-b border-white/5 glass px-4 py-0 md:hidden">
            <button type="button" x-on:click="sidebarOpen = true" class="rounded-lg p-2 text-gray-300 hover:bg-white/5" aria-label="Buka menu" aria-controls="admin-sidebar" :aria-expanded="sidebarOpen ? 'true' : 'false'">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>
            <span class="text-sm font-extrabold tracking-tight text-white">Panel admin</span>
            <span class="w-10" aria-hidden="true"></span>
        </header>

        <main class="relative z-10 flex-1 px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-6xl">
                @if(session('ok'))
                    <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('ok') }}</div>
                @endif
                @if(session('status') && is_string(session('status')))
                    <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('status') }}</div>
                @endif
                @yield('content')
            </div>
        </main>

        <footer class="relative z-10 border-t border-white/5 py-6 text-center text-xs text-gray-500 md:pl-0">
            <p>&copy; {{ date('Y') }} {{ $site['brand_name'] ?? config('app.name') }} — Panel</p>
        </footer>
    </div>
    <style>
        [x-cloak] { display: none !important; }
        /* Override @tailwindcss/forms: input putih + abu-abu terang = sulit dibaca */
        main input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):not([type="hidden"]),
        main textarea,
        main select {
            background-color: rgb(15 23 42) !important; /* slate-900 */
            color: rgb(248 250 252) !important; /* slate-50 */
            border-color: rgba(255 255 255 / 0.12) !important;
        }
        main input::placeholder,
        main textarea::placeholder {
            color: rgb(148 163 184) !important; /* slate-400 */
            opacity: 1 !important;
        }
        main select option {
            background-color: rgb(15 23 42);
            color: rgb(248 250 252);
        }
        main input:-webkit-autofill,
        main textarea:-webkit-autofill,
        main select:-webkit-autofill {
            -webkit-text-fill-color: rgb(248 250 252) !important;
            box-shadow: 0 0 0 1000px rgb(15 23 42) inset !important;
        }
    </style>
    @stack('scripts')
</body>
</html>
