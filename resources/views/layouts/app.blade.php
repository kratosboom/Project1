<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" type="image/png" href="https://asset.t99group.com/assets/logo/TOKYO99/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
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
        .border-primary\/40 { border-color: color-mix(in srgb, var(--primary) 40%, transparent) !important; }
        .border-primary\/50 { border-color: color-mix(in srgb, var(--primary) 50%, transparent) !important; }
        .ring-primary\/50 { --tw-ring-color: color-mix(in srgb, var(--primary) 50%, transparent) !important; }
        .text-primary\/90 { color: color-mix(in srgb, var(--primary) 90%, white 10%) !important; }
        .text-primary\/80 { color: color-mix(in srgb, var(--primary) 80%, white 20%) !important; }
        .bg-primary\/10 { background-color: color-mix(in srgb, var(--primary) 10%, transparent) !important; }
        .bg-primary\/15 { background-color: color-mix(in srgb, var(--primary) 15%, transparent) !important; }
        .bg-primary\/20 { background-color: color-mix(in srgb, var(--primary) 20%, transparent) !important; }
        .shadow-primary\/25 { --tw-shadow-color: color-mix(in srgb, var(--primary) 25%, transparent) !important; }
        .shadow-primary\/30 { --tw-shadow-color: color-mix(in srgb, var(--primary) 30%, transparent) !important; }
        .hover\:border-primary\/30:hover { border-color: color-mix(in srgb, var(--primary) 30%, transparent) !important; }
        .hover\:border-primary\/40:hover { border-color: color-mix(in srgb, var(--primary) 40%, transparent) !important; }
        .hover\:text-primary:hover { color: var(--primary) !important; }
        .hover\:bg-primary\/20:hover { background-color: color-mix(in srgb, var(--primary) 20%, transparent) !important; }
    </style>
</head>
<body class="min-h-full bg-[#111827] font-sans text-gray-100 antialiased ink-bg bg-hero">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -left-32 top-20 h-72 w-72 rounded-full bg-primary/15 blur-3xl"></div>
        <div class="absolute -right-20 bottom-10 h-96 w-96 rounded-full bg-primary/20 blur-3xl"></div>
    </div>
    @include('layouts.navigation')
    <main class="relative z-10 mx-auto max-w-6xl px-4 py-10 sm:px-6">
        @if(session('ok'))
            <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('ok') }}</div>
        @endif
        @if(session('status') && is_string(session('status')))
            <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>
    <footer class="relative z-10 border-t border-white/5 py-8 text-center text-xs text-gray-500">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
    </footer>
</body>
</html>
