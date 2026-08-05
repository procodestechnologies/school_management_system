@props([
    'data' => [], // [['label' => string, 'value' => int, 'color' => string], ...]
])

@php
    $max = count($data) ? max(array_column($data, 'value')) : 0;
    $max = $max > 0 ? $max : 1;
    $total = array_sum(array_column($data, 'value'));

    $colorClass = fn ($color) => match ($color) {
        'emerald' => 'bg-emerald-500',
        'amber' => 'bg-amber-500',
        'red' => 'bg-red-500',
        'sky' => 'bg-sky-500',
        'violet' => 'bg-violet-500',
        'rose' => 'bg-rose-500',
        'zinc' => 'bg-zinc-400',
        default => 'bg-indigo-500',
    };
@endphp

<div class="space-y-3">
    @forelse ($data as $item)
        <div>
            <div class="mb-1 flex items-center justify-between text-sm">
                <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $item['label'] }}</span>
                <span class="text-zinc-500 dark:text-zinc-400">
                    {{ $item['value'] }}
                    @if ($total > 0)
                        <span class="text-xs text-zinc-400 dark:text-zinc-500">({{ round($item['value'] / $total * 100) }}%)</span>
                    @endif
                </span>
            </div>
            <div class="h-2 w-full rounded-full bg-zinc-100 dark:bg-white/5">
                <div class="h-2 rounded-full {{ $colorClass($item['color'] ?? 'indigo') }}"
                    style="width: {{ max(2, round($item['value'] / $max * 100)) }}%"></div>
            </div>
        </div>
    @empty
        <p class="text-sm text-zinc-500 dark:text-zinc-400">No data yet.</p>
    @endforelse
</div>
