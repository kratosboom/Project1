@extends('layouts.admin')

@section('title', 'Pengaturan situs — '.config('app.name'))

@section('content')
@php $s = $settings; @endphp
<div class="mb-8">
    <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">Pengaturan situs</h1>
    <p class="mt-1 text-sm text-gray-400">Nama brand, logo, hero, footer, teks jalan, link login/daftar, teks modal analisis maxwin, dan URL tombol Main / Hajar di beranda.</p>
</div>

<div class="glass mb-6 max-w-3xl rounded-2xl p-6 sm:p-8">
    <h2 class="text-lg font-bold text-white">Theme controller (6 warna)</h2>
    <p class="mt-1 text-sm text-gray-400">Pilih 1 tema agar warna keseluruhan template selaras.</p>
    <form method="post" action="{{ route('admin.theme.update') }}" class="mt-5 space-y-4">
        @csrf
        @method('PUT')
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($themePresets as $themeKey => $themeData)
                @php $active = old('theme_preset', $s['theme_preset'] ?? 'crimson') === $themeKey; @endphp
                <label class="cursor-pointer rounded-xl border p-3 transition {{ $active ? 'border-primary/60 bg-white/5' : 'border-white/10 hover:border-white/25' }}">
                    <input type="radio" name="theme_preset" value="{{ $themeKey }}" class="sr-only" @checked($active)>
                    <div class="mb-2 flex gap-1.5">
                        <span class="h-4 w-4 rounded-full border border-white/20" style="background: {{ $themeData['primary'] }}"></span>
                        <span class="h-4 w-4 rounded-full border border-white/20" style="background: {{ $themeData['secondary'] }}"></span>
                        <span class="h-4 w-4 rounded-full border border-white/20" style="background: {{ $themeData['bg'] }}"></span>
                        <span class="h-4 w-4 rounded-full border border-white/20" style="background: {{ $themeData['nav'] }}"></span>
                    </div>
                    <p class="text-sm font-semibold text-slate-100">{{ $themeData['label'] }}</p>
                </label>
            @endforeach
        </div>
        <div class="pt-1">
            <button type="submit" class="rounded-xl bg-primary px-5 py-2 text-sm font-bold text-black transition hover:brightness-110">Simpan tema</button>
        </div>
    </form>
</div>

<div class="glass max-w-3xl rounded-2xl p-6 sm:p-8">
    <form method="post" action="{{ route('admin.pengaturan.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="brand_name" class="mb-1.5 block text-sm font-medium text-slate-300">Nama brand</label>
            <input type="text" name="brand_name" id="brand_name" value="{{ old('brand_name', $s['brand_name'] ?? '') }}" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary/30" placeholder="{{ config('app.name') }}">
        </div>

        <div>
            <label for="logo_url" class="mb-1.5 block text-sm font-medium text-slate-300">URL logo (nav)</label>
            <input type="url" name="logo_url" id="logo_url" value="{{ old('logo_url', $s['logo_url'] ?? '') }}" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary/30" placeholder="https://...">
        </div>

        <div>
            <label for="favicon_url" class="mb-1.5 block text-sm font-medium text-slate-300">URL favicon</label>
            <input type="url" name="favicon_url" id="favicon_url" value="{{ old('favicon_url', $s['favicon_url'] ?? '') }}" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100 focus:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary/30" placeholder="https://.../favicon.png">
        </div>

        <div>
            <label for="hero_banner_url" class="mb-1.5 block text-sm font-medium text-slate-300">Banner hero (beranda)</label>
            <input type="url" name="hero_banner_url" id="hero_banner_url" value="{{ old('hero_banner_url', $s['hero_banner_url'] ?? '') }}" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100 focus:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary/30" placeholder="https://... (gambar lebar)">
        </div>

        <div>
            <label for="footer_text" class="mb-1.5 block text-sm font-medium text-slate-300">Teks footer</label>
            <textarea name="footer_text" id="footer_text" rows="3" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary/30" placeholder="Baris bawah di footer (HTML sederhana OK)">{{ old('footer_text', $s['footer_text'] ?? '') }}</textarea>
        </div>

        <div>
            <label for="marquee_text" class="mb-1.5 block text-sm font-medium text-slate-300">Teks jalan (marquee)</label>
            <textarea name="marquee_text" id="marquee_text" rows="2" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100 focus:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary/30">{{ old('marquee_text', $s['marquee_text'] ?? '') }}</textarea>
        </div>

        <div class="rounded-xl border border-white/10 bg-black/20 p-4 sm:p-5">
            <h3 class="text-sm font-bold text-white">Modal analisis maxwin &amp; tombol game</h3>
            <p class="mt-1 text-xs text-gray-500">Tampilan di kartu game (hover) dan popup setelah &quot;Analisis maxwin&quot;. URL boleh absolut (<code class="text-gray-400">https://...</code>) atau path situs (<code class="text-gray-400">/slug</code>).</p>
            <div class="mt-4 space-y-4">
                <div>
                    <label for="maxwin_modal_kapital_label" class="mb-1.5 block text-sm font-medium text-slate-300">Label blok modal kapital</label>
                    <input type="text" name="maxwin_modal_kapital_label" id="maxwin_modal_kapital_label" value="{{ old('maxwin_modal_kapital_label', $s['maxwin_modal_kapital_label'] ?? '') }}" maxlength="160" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary/30" placeholder="Modal kapital (IDR)">
                </div>
                <div>
                    <label for="maxwin_default_kapital" class="mb-1.5 block text-sm font-medium text-slate-300">Nilai modal kapital default (IDR)</label>
                    <input type="text" name="maxwin_default_kapital" id="maxwin_default_kapital" value="{{ old('maxwin_default_kapital', $s['maxwin_default_kapital'] ?? '') }}" inputmode="numeric" pattern="[0-9]*" maxlength="14" class="w-full max-w-xs rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary/30" placeholder="50000 (jika game tidak punya nilai sendiri)">
                </div>
                <div>
                    <label for="maxwin_modal_footer_text" class="mb-1.5 block text-sm font-medium text-slate-300">Teks footer di bawah modal</label>
                    <input type="text" name="maxwin_modal_footer_text" id="maxwin_modal_footer_text" value="{{ old('maxwin_modal_footer_text', $s['maxwin_modal_footer_text'] ?? '') }}" maxlength="500" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary/30" placeholder="Engine powered by brand analytics">
                </div>
                <div>
                    <label for="main_sekarang_url" class="mb-1.5 block text-sm font-medium text-slate-300">URL tombol &quot;Main Sekarang&quot; (hover kartu)</label>
                    <input type="text" name="main_sekarang_url" id="main_sekarang_url" value="{{ old('main_sekarang_url', $s['main_sekarang_url'] ?? '') }}" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary/30" placeholder="https://... atau /promo">
                </div>
                <div>
                    <label for="hajar_sekarang_url" class="mb-1.5 block text-sm font-medium text-slate-300">URL tombol &quot;Hajar sekarang&quot; (di modal)</label>
                    <input type="text" name="hajar_sekarang_url" id="hajar_sekarang_url" value="{{ old('hajar_sekarang_url', $s['hajar_sekarang_url'] ?? '') }}" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary/30" placeholder="https://...">
                </div>
            </div>
        </div>

        <div>
            <label for="login_url" class="mb-1.5 block text-sm font-medium text-slate-300">URL / route Login (nav)</label>
            <input type="text" name="login_url" id="login_url" value="{{ old('login_url', $s['login_url'] ?? '') }}" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100 focus:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary/30" placeholder="Kosong = {{ url('/login') }}">
        </div>

        <div>
            <label for="register_url" class="mb-1.5 block text-sm font-medium text-slate-300">URL Daftar (nav)</label>
            <input type="text" name="register_url" id="register_url" value="{{ old('register_url', $s['register_url'] ?? '') }}" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2.5 text-slate-100 focus:border-primary/50 focus:outline-none focus:ring-1 focus:ring-primary/30" placeholder="https://... atau #">
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-2">
            <button type="submit" class="rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-black transition hover:brightness-110">Simpan pengaturan</button>
            <a href="{{ route('admin.dashboard') }}" class="rounded-xl border border-white/15 px-6 py-2.5 text-slate-300 hover:bg-white/5">Batal</a>
        </div>
    </form>
</div>
@endsection
