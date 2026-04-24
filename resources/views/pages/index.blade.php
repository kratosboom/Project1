@extends('layouts.admin')

@section('title', 'Kelola halaman — '.config('app.name'))

@section('content')
<div class="mb-8 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-extrabold tracking-tight text-white">Kelola halaman</h1>
        <p class="mt-1 text-slate-400">CRUD halaman dinamis — klik judul untuk publik jika dipublikasikan.</p>
    </div>
    <a href="{{ route('admin.halaman.create') }}" class="rounded-full bg-primary px-5 py-2.5 text-sm font-bold text-black shadow-lg shadow-primary/25 transition hover:brightness-110">+ Halaman baru</a>
</div>

@if($pages->isEmpty())
    <div class="glass rounded-2xl p-12 text-center text-slate-400">
        <p>Belum ada halaman. <a href="{{ route('admin.halaman.create') }}" class="text-primary hover:underline">Buat yang pertama</a>.</p>
    </div>
@else
    <div class="overflow-hidden rounded-2xl border border-white/10 glass">
        <table class="min-w-full divide-y divide-white/10 text-left text-sm">
            <thead class="bg-white/5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <tr>
                    <th class="px-4 py-3 sm:px-6">Judul</th>
                    <th class="hidden px-4 py-3 sm:table-cell sm:px-6">Slug</th>
                    <th class="px-4 py-3 sm:px-6">Status</th>
                    <th class="hidden px-4 py-3 md:table-cell md:px-6">Diperbarui</th>
                    <th class="px-4 py-3 text-right sm:px-6">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($pages as $p)
                    <tr class="hover:bg-white/[0.03]">
                        <td class="px-4 py-3 sm:px-6">
                            @if($p->is_published)
                                <a href="{{ route('halaman.show', $p) }}" class="font-medium text-white hover:text-primary">{{ $p->title }}</a>
                            @else
                                <span class="font-medium text-slate-400">{{ $p->title }}</span>
                                <span class="ml-2 text-xs text-amber-400/90">draf</span>
                            @endif
                        </td>
                        <td class="hidden font-mono text-xs text-cyan-200/80 sm:table-cell sm:px-6">{{ $p->slug }}</td>
                        <td class="px-4 py-3 sm:px-6">
                            @if($p->is_published)
                                <span class="rounded-full bg-emerald-500/15 px-2 py-0.5 text-xs text-emerald-300">Publik</span>
                            @else
                                <span class="rounded-full bg-slate-500/20 px-2 py-0.5 text-xs text-slate-400">Draf</span>
                            @endif
                        </td>
                        <td class="hidden text-slate-500 md:table-cell md:px-6">{{ $p->updated_at->diffForHumans() }}</td>
                        <td class="px-4 py-3 text-right sm:px-6">
                            <div class="flex flex-wrap justify-end gap-2">
                                <a href="{{ route('admin.halaman.edit', $p) }}" class="rounded-lg border border-white/10 px-2 py-1 text-xs text-slate-300 hover:bg-white/5">Edit</a>
                                <form action="{{ route('admin.halaman.destroy', $p) }}" method="post" class="inline" onsubmit="return confirm('Hapus halaman ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-rose-500/30 px-2 py-1 text-xs text-rose-300 hover:bg-rose-500/10">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
