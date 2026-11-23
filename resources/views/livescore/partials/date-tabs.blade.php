{{-- $day (Carbon, zona livescore) --}}
@php
    $tz = config('livescore.timezone');
    $today = \Illuminate\Support\Carbon::now($tz)->startOfDay();
    $days = collect(range(-3, 3))->map(fn ($o) => $today->copy()->addDays($o));
@endphp
<nav aria-label="Pilih tanggal" class="-mx-4 overflow-x-auto px-4 no-scrollbar sm:mx-0 sm:px-0">
    <ul class="flex min-w-max gap-2">
        @foreach($days as $d)
            @php
                $active = $d->isSameDay($day);
                $label = match ((int) $today->diffInDays($d, false)) { -1 => 'Kemarin', 0 => 'Hari ini', 1 => 'Besok', default => $d->locale('id')->translatedFormat('D') };
            @endphp
            <li>
                <a href="{{ $d->isSameDay($today) ? route('home') : route('jadwal', $d->toDateString()) }}"
                   class="date-tab flex min-w-[76px] flex-col items-center rounded-2xl border px-3 py-2 {{ $active ? 'is-active' : '' }}"
                   @if($active) aria-current="date" @endif>
                    <span class="text-[10px] font-semibold uppercase tracking-[0.16em]">{{ $label }}</span>
                    <span class="mt-0.5 text-sm tabular-nums">{{ $d->locale('id')->translatedFormat('j M') }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>
