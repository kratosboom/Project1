@extends('layouts.admin')

@section('title', 'User admin — '.config('app.name'))

@section('content')
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">User admin</h1>
        <p class="mt-1 text-sm text-gray-400">Kelola akun user yang bisa login ke panel admin.</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-black">+ User</a>
</div>

<form method="get" class="mb-4">
    <div class="flex items-center gap-2">
        <input
            type="search"
            name="q"
            value="{{ $search }}"
            placeholder="Cari nama / email..."
            class="w-full rounded-xl border border-white/10 bg-black/40 px-3 py-2.5 text-sm"
        >
        <button type="submit" class="rounded-xl border border-white/10 px-4 py-2.5 text-sm font-semibold text-gray-200 hover:bg-white/5">Cari</button>
    </div>
</form>

<div class="overflow-hidden rounded-2xl border border-white/10">
    <table class="w-full min-w-[600px] text-left text-sm">
        <thead class="border-b border-white/10 bg-white/5 text-xs font-bold uppercase tracking-wider text-gray-500">
            <tr>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Verifikasi email</th>
                <th class="px-4 py-3">Dibuat</th>
                <th class="px-4 py-3 w-40 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @forelse($users as $user)
                <tr class="text-gray-300">
                    <td class="px-4 py-3">
                        <span class="font-semibold text-white">{{ $user->name }}</span>
                    </td>
                    <td class="px-4 py-3">{{ $user->email }}</td>
                    <td class="px-4 py-3">{{ $user->email_verified_at ? 'Ya' : 'Belum' }}</td>
                    <td class="px-4 py-3">{{ $user->created_at?->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-3">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-primary hover:underline">Edit</a>
                            @if(auth()->id() !== $user->id)
                                <form method="post" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus user ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-300 hover:underline">Hapus</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada user.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($users->hasPages())
    <div class="mt-6">{{ $users->links('vendor.pagination.admin') }}</div>
@endif
@endsection
