{{-- $league (League), $count (int|null) --}}
<a href="{{ route('liga.show', $league) }}" class="group flex items-center justify-between gap-3 border-b border-white/5 px-4 py-3 sm:px-5">
    <span class="flex min-w-0 items-center gap-3">
        @if($league->logo)
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white p-1"><img src="{{ $league->logo }}" alt="" width="20" height="20" loading="lazy" class="h-5 w-5 object-contain"></span>
        @endif
        <span class="min-w-0">
            <span class="block truncate text-sm font-semibold text-[color:var(--ink)] group-hover:text-[color:var(--gold)]">{{ $league->displayName() }}</span>
            @if($league->country)<span class="block text-[10px] uppercase tracking-[0.16em] text-[color:var(--muted)]">{{ $league->country }}</span>@endif
        </span>
    </span>
    <span class="flex items-center gap-2 text-[10px] uppercase tracking-[0.16em] text-[color:var(--muted)] group-hover:text-[color:var(--gold)]">
        Klasemen <i class="fas fa-chevron-right text-[9px]" aria-hidden="true"></i>
    </span>
</a>
