@extends('layouts.admin')

@section('title', 'Provider game — '.config('app.name'))

@section('content')
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">Provider</h1>
        <p class="mt-1 text-sm text-gray-400">Tab kategori + logo (opsional) — ambil logo otomatis dari website.</p>
    </div>
    <a href="{{ route('admin.provider.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-black">+ Provider</a>
</div>

<div class="overflow-hidden rounded-2xl border border-white/10">
    <table class="w-full min-w-[600px] text-left text-sm">
        <thead class="border-b border-white/10 bg-white/5 text-xs font-bold uppercase tracking-wider text-gray-500">
            <tr>
                <th class="px-4 py-3">Nama / slug</th>
                <th class="px-4 py-3">HOT GAMES</th>
                <th class="px-4 py-3">Urutan</th>
                <th class="px-4 py-3 w-32"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @forelse($providers as $p)
                <tr class="text-gray-300">
                    <td class="px-4 py-3">
                        <span class="font-semibold text-white">{{ $p->name }}</span>
                        <p class="text-xs text-gray-500">{{ $p->slug }}</p>
                    </td>
                    <td class="px-4 py-3">{{ $p->is_hot_games ? 'Ya' : '—' }}</td>
                    <td class="px-4 py-3 tabular-nums">{{ $p->sort_order }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.provider.edit', $p) }}" class="text-primary hover:underline">Edit</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada provider. Jalankan seeder atau tambah manual.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
