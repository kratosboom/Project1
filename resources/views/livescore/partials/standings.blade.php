{{-- $standing (Standing) --}}
@foreach($standing->groups as $group)
    @php $groupName = count($standing->groups) > 1 ? ($group[0]['group'] ?? null) : null; @endphp
    <div class="lux-panel overflow-hidden rounded-3xl {{ ! $loop->last ? 'mb-6' : '' }}">
        @if($groupName)
            <p class="border-b border-white/5 px-5 py-3 text-sm font-semibold text-[color:var(--ink)]">{{ $groupName }}</p>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full min-w-[520px] text-sm">
                <caption class="sr-only">Klasemen{{ $groupName ? ' '.$groupName : '' }}</caption>
                <thead>
                    <tr class="text-[10px] uppercase tracking-[0.16em] text-[color:var(--muted)]">
                        <th scope="col" class="w-10 px-4 py-3 text-left font-medium">#</th>
                        <th scope="col" class="px-2 py-3 text-left font-medium">Tim</th>
                        <th scope="col" class="px-2 py-3 text-center font-medium" title="Main">M</th>
                        <th scope="col" class="px-2 py-3 text-center font-medium" title="Menang">M</th>
                        <th scope="col" class="px-2 py-3 text-center font-medium" title="Seri">S</th>
                        <th scope="col" class="px-2 py-3 text-center font-medium" title="Kalah">K</th>
                        <th scope="col" class="hidden px-2 py-3 text-center font-medium sm:table-cell" title="Selisih gol">SG</th>
                        <th scope="col" class="px-4 py-3 text-center font-medium">Poin</th>
                        <th scope="col" class="hidden px-4 py-3 text-left font-medium md:table-cell">Performa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($group as $row)
                        <tr class="border-t border-white/5">
                            <td class="px-4 py-3 tabular-nums">
                                <span class="rank-pill {{ ! empty($row['description']) ? 'is-zone' : '' }}" @if(!empty($row['description'])) title="{{ $row['description'] }}" @endif>{{ $row['rank'] }}</span>
                            </td>
                            <th scope="row" class="px-2 py-3 text-left font-normal">
                                <span class="flex items-center gap-2.5">
                                    @if(!empty($row['team']['logo']))
                                        <img src="{{ $row['team']['logo'] }}" alt="" width="20" height="20" loading="lazy" class="h-5 w-5 object-contain">
                                    @endif
                                    <span class="truncate text-[color:var(--ink)]">{{ $row['team']['name'] }}</span>
                                </span>
                            </th>
                            <td class="px-2 py-3 text-center tabular-nums text-[color:var(--muted)]">{{ $row['all']['played'] }}</td>
                            <td class="px-2 py-3 text-center tabular-nums text-[color:var(--muted)]">{{ $row['all']['win'] }}</td>
                            <td class="px-2 py-3 text-center tabular-nums text-[color:var(--muted)]">{{ $row['all']['draw'] }}</td>
                            <td class="px-2 py-3 text-center tabular-nums text-[color:var(--muted)]">{{ $row['all']['lose'] }}</td>
                            <td class="hidden px-2 py-3 text-center tabular-nums text-[color:var(--muted)] sm:table-cell">{{ $row['goalsDiff'] > 0 ? '+' : '' }}{{ $row['goalsDiff'] }}</td>
                            <td class="px-4 py-3 text-center font-semibold tabular-nums text-[color:var(--ink)]">{{ $row['points'] }}</td>
                            <td class="hidden px-4 py-3 md:table-cell">
                                <span class="flex gap-1" aria-label="Performa: {{ $row['form'] ?? '-' }}">
                                    @foreach(str_split((string) ($row['form'] ?? '')) as $r)
                                        @if($r !== '')<span class="form-dot form-{{ strtolower($r) }}" aria-hidden="true">{{ ['W' => 'M', 'D' => 'S', 'L' => 'K'][$r] ?? $r }}</span>@endif
                                    @endforeach
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach
