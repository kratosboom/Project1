<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: ['Outfit', 'system-ui', 'sans-serif'] },
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
    }
    body {
        font-family: 'Outfit', sans-serif;
        background-color: var(--bg);
        color: #fff;
    }
    .glass {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .btn-primary {
        background: var(--primary);
        color: #000;
        font-weight: 700;
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px var(--primary);
        filter: brightness(1.1);
    }
    .btn-secondary {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #fff;
        transition: all 0.3s ease;
    }
    .btn-secondary:hover {
        background: var(--secondary);
        border-color: var(--secondary);
    }
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: var(--bg); }
    ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #444; }
    @keyframes pulse-zoom {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.15); }
    }
    .animate-pulse-zoom {
        animation: pulse-zoom 2s infinite ease-in-out;
        transform-origin: center;
    }
    .category-btn {
        padding: 0.625rem 1.25rem;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        background: rgba(255, 255, 255, 0.05);
        color: #9ca3af;
        border: 1px solid rgba(255, 255, 255, 0.05);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .category-btn.active {
        background: var(--primary);
        color: #000;
        border-color: var(--primary);
    }
    .custom-scrollbar::-webkit-scrollbar { height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.02); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
    .provider-grid {
        display: grid;
        grid-template-rows: repeat(2, auto);
        grid-auto-flow: column;
        gap: 0.75rem;
        width: max-content;
    }
    @keyframes bar-fill { from { width: 0; } }
    .bar-fill-anim { animation: bar-fill 1.2s ease-out forwards; }
    @keyframes shimmer-slide {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    .shimmer-effect::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
        animation: shimmer-slide 2.5s ease-in-out infinite;
    }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
