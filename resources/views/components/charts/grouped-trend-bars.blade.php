@props([
    'data' => [],
    'series' => [],
    'height' => 128,
    'formatter' => null,
])

@php
    $seriesColors = [
        'bg-zinc-300 dark:bg-zinc-600',
        'bg-emerald-500 dark:bg-emerald-400',
    ];

    $max = 0;
    foreach ($data as $bucket) {
        foreach ($series as $key => $seriesLabel) {
            $max = max($max, $bucket[$key] ?? 0);
        }
    }
    $max = $max > 0 ? $max : 1;
@endphp

@if (count($series) > 1)
    <div class="mb-3 flex items-center gap-4 text-xs text-zinc-500 dark:text-zinc-400">
        @foreach ($series as $key => $seriesLabel)
            <span class="flex items-center gap-1.5">
                <span class="size-2 rounded-full {{ $seriesColors[$loop->index] ?? 'bg-indigo-500' }}"></span>
                {{ $seriesLabel }}
            </span>
        @endforeach
    </div>
@endif

<div class="flex items-end gap-3" style="height: {{ $height }}px">
    @foreach ($data as $label => $bucket)
        <div class="group relative flex h-full flex-1 flex-col items-end justify-end gap-1.5">
            <span
                class="pointer-events-none absolute -top-10 hidden w-max flex-col rounded-md bg-zinc-900 px-2 py-1 text-xs font-medium whitespace-nowrap text-white group-hover:flex dark:bg-white dark:text-zinc-900">
                @foreach ($series as $key => $seriesLabel)
                    <span>{{ $seriesLabel }}: {{ $formatter ? $formatter($bucket[$key] ?? 0) : ($bucket[$key] ?? 0) }}</span>
                @endforeach
            </span>
            <div class="flex h-full w-full items-end gap-0.5">
                @foreach ($series as $key => $seriesLabel)
                    @php $value = $bucket[$key] ?? 0; @endphp
                    <div class="flex-1 rounded-t-md {{ $seriesColors[$loop->index] ?? 'bg-indigo-500' }} transition-[height]"
                        style="height: {{ $value > 0 ? max(4, round($value / $max * 100)) : 2 }}%"></div>
                @endforeach
            </div>
            <span class="text-[10px] font-medium text-zinc-400 dark:text-zinc-500">{{ $label }}</span>
        </div>
    @endforeach
</div>
