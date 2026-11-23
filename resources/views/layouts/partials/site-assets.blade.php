<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Outfit', 'system-ui', 'sans-serif'],
                    display: ['"Cormorant Garamond"', 'Georgia', 'serif'],
                },
                colors: { primary: '{{ $theme['primary'] ?? '#fb2323' }}' },
            },
        },
    };
</script>
<style>
    :root {
        --primary: {{ $theme['primary'] ?? '#fb2323' }};
        --secondary: {{ $theme['secondary'] ?? '#b30000' }};
        --bg: {{ $theme['bg'] ?? '#111827' }};
        --nav: {{ $theme['nav'] ?? '#1f2937' }};
        /* Token desain: aksen = primary (emas), permukaan = secondary (zamrud) */
        --gold: var(--primary);
        --gold-soft: color-mix(in srgb, var(--primary) 70%, #fff 30%);
        --emerald: var(--secondary);
        --ink: #f4efe2;
        --muted: #9fb0a8;
        --line: color-mix(in srgb, var(--primary) 22%, transparent);
        --surface: color-mix(in srgb, var(--secondary) 14%, var(--bg) 86%);
        --surface-2: color-mix(in srgb, var(--secondary) 22%, var(--bg) 78%);
    }
    html { scroll-behavior: smooth; }
    body {
        font-family: 'Outfit', sans-serif;
        background-color: var(--bg);
        background-image:
            radial-gradient(1200px 600px at 85% -10%, color-mix(in srgb, var(--secondary) 32%, transparent), transparent 70%),
            radial-gradient(900px 500px at -10% 30%, color-mix(in srgb, var(--secondary) 18%, transparent), transparent 70%);
        background-attachment: fixed;
        color: var(--ink);
        -webkit-font-smoothing: antialiased;
    }
    .font-display { font-family: 'Cormorant Garamond', Georgia, serif; }
    .glass {
        background: color-mix(in srgb, var(--nav) 72%, transparent);
        backdrop-filter: blur(14px) saturate(140%);
        -webkit-backdrop-filter: blur(14px) saturate(140%);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }
    .btn-primary {
        background: linear-gradient(135deg, var(--gold-soft), var(--gold) 55%, color-mix(in srgb, var(--gold) 75%, #000 25%));
        color: #15110a;
        font-weight: 700;
        border: 1px solid color-mix(in srgb, var(--gold) 60%, #fff 40%);
        transition: transform .25s ease, box-shadow .25s ease, filter .25s ease;
    }
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 28px -12px color-mix(in srgb, var(--gold) 80%, transparent);
        filter: brightness(1.06);
    }
    .btn-secondary {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--line);
        color: var(--ink);
        transition: background .25s ease, border-color .25s ease, color .25s ease;
    }
    .btn-secondary:hover {
        background: color-mix(in srgb, var(--gold) 10%, transparent);
        border-color: color-mix(in srgb, var(--gold) 55%, transparent);
        color: #fff;
    }
    .gold-text {
        background: linear-gradient(100deg, var(--gold-soft) 0%, var(--gold) 45%, color-mix(in srgb, var(--gold) 70%, #7a5b10 30%) 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .hairline { border-color: var(--line); }
    .ornament {
        display: flex; align-items: center; gap: .75rem;
        color: var(--gold); font-size: .65rem; letter-spacing: .32em; text-transform: uppercase; font-weight: 600;
    }
    .ornament::before, .ornament::after {
        content: ''; height: 1px; flex: 1;
        background: linear-gradient(90deg, transparent, var(--line), transparent);
    }
    .lux-panel {
        background: linear-gradient(180deg, color-mix(in srgb, var(--surface-2) 85%, transparent), color-mix(in srgb, var(--surface) 92%, transparent));
        border: 1px solid var(--line);
        box-shadow: 0 1px 0 rgba(255,255,255,.04) inset, 0 24px 60px -32px rgba(0,0,0,.8);
    }
    .lux-card {
        background: linear-gradient(180deg, var(--surface-2), var(--surface));
        border: 1px solid rgba(255, 255, 255, 0.06);
        transition: transform .4s cubic-bezier(.2,.7,.2,1), border-color .4s ease, box-shadow .4s ease;
    }
    .lux-card:hover {
        transform: translateY(-4px);
        border-color: color-mix(in srgb, var(--gold) 45%, transparent);
        box-shadow: 0 26px 50px -28px color-mix(in srgb, var(--gold) 55%, #000 45%);
    }
    .lux-input {
        background: color-mix(in srgb, var(--bg) 70%, transparent);
        border: 1px solid rgba(255,255,255,.08);
        color: var(--ink);
        transition: border-color .2s ease, box-shadow .2s ease;
    }
    .lux-input:focus {
        outline: none;
        border-color: color-mix(in srgb, var(--gold) 60%, transparent);
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--gold) 14%, transparent);
    }
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: var(--bg); }
    ::-webkit-scrollbar-thumb { background: color-mix(in srgb, var(--gold) 30%, #222); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar { height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.02); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: var(--line); border-radius: 10px; }
    @keyframes live-blink { 0%, 100% { opacity: 1; } 50% { opacity: .3; } }
    .live-dot { animation: live-blink 1.6s ease-in-out infinite; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    :focus-visible { outline: 2px solid var(--gold); outline-offset: 2px; }
    details > summary { list-style: none; }
    details > summary::-webkit-details-marker { display: none; }
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; scroll-behavior: auto !important; }
    }
</style>
