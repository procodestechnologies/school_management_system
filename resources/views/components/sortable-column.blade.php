@props([
    'column',
    'align' => 'start',
])

@php
    $currentColumn = request()->query('sort');
    $currentDirection = request()->query('direction') === 'desc' ? 'desc' : 'asc';
    $isSorted = $currentColumn === $column;
    $nextDirection = $isSorted && $currentDirection === 'asc' ? 'desc' : 'asc';

    $query = request()->query();
    $query['sort'] = $column;
    $query['direction'] = $nextDirection;
    unset($query['page']);
@endphp

<flux:table.column :align="$align">
    <a href="{{ request()->url().'?'.http_build_query($query) }}"
        class="group/sortable-link -my-1 -ms-2 -me-2 flex items-center gap-1 rounded-sm px-2 py-1 in-[.group\/end-align]:flex-row-reverse">
        <span>{{ $slot }}</span>
        <span class="rounded-sm text-zinc-400 group-hover/sortable-link:text-zinc-800 dark:group-hover/sortable-link:text-white">
            @if ($isSorted)
                @if ($currentDirection === 'asc')
                    <flux:icon.chevron-up variant="micro" />
                @else
                    <flux:icon.chevron-down variant="micro" />
                @endif
            @else
                <span class="opacity-0 group-hover/sortable-link:opacity-100">
                    <flux:icon.chevron-down variant="micro" />
                </span>
            @endif
        </span>
    </a>
</flux:table.column>
