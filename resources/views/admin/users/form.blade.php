@extends('layouts.admin')

@section('title', ($isEdit ? 'Edit user admin' : 'Buat user admin').' — '.config('app.name'))

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">{{ $isEdit ? 'Edit user admin' : 'Buat user admin' }}</h1>
    <p class="mt-1 text-sm text-gray-400">
        {{ $isEdit ? 'Perbarui data akun login panel admin.' : 'Tambahkan akun login baru untuk panel admin.' }}
    </p>
</div>

@if($errors->any())
    <div class="mb-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="max-w-2xl overflow-hidden rounded-2xl border border-white/10 bg-black/20 p-5">
    <form method="post" action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}" class="space-y-4">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div>
            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-gray-500">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-xl border border-white/10 bg-black/40 px-3 py-2.5 text-sm">
        </div>

        <div>
            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-gray-500">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-xl border border-white/10 bg-black/40 px-3 py-2.5 text-sm">
        </div>

        <div>
            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-gray-500">Password {{ $isEdit ? '(opsional)' : '' }}</label>
            <input type="password" name="password" {{ $isEdit ? '' : 'required' }} class="w-full rounded-xl border border-white/10 bg-black/40 px-3 py-2.5 text-sm">
        </div>

        <div>
            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-gray-500">Konfirmasi password</label>
            <input type="password" name="password_confirmation" {{ $isEdit ? '' : 'required' }} class="w-full rounded-xl border border-white/10 bg-black/40 px-3 py-2.5 text-sm">
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-black">{{ $isEdit ? 'Update' : 'Simpan' }}</button>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-xl border border-white/10 px-4 py-2.5 text-sm font-semibold text-gray-200 hover:bg-white/5">Batal</a>
        </div>
    </form>
</div>
@endsection
