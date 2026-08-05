@props([
    'data' => [],
    'color' => 'indigo',
    'height' => 128,
    'formatter' => null,
])

@php
    $barClass = match ($color) {
        'emerald' => 'bg-emerald-500 dark:bg-emerald-400',
        'amber' => 'bg-amber-500 dark:bg-amber-400',
        'sky' => 'bg-sky-500 dark:bg-sky-400',
        'violet' => 'bg-violet-500 dark:bg-violet-400',
        'rose' => 'bg-rose-500 dark:bg-rose-400',
        'zinc' => 'bg-zinc-400 dark:bg-zinc-500',
        default => 'bg-indigo-500 dark:bg-indigo-400',
    };

    $max = count($data) ? max(array_values($data)) : 0;
    $max = $max > 0 ? $max : 1;
@endphp

<div class="flex items-end gap-2" style="height: {{ $height }}px">
    @foreach ($data as $label => $value)
        <div class="group relative flex h-full flex-1 flex-col items-center justify-end gap-1.5">
            <span
                class="pointer-events-none absolute -top-6 hidden rounded-md bg-zinc-900 px-1.5 py-0.5 text-xs font-medium whitespace-nowrap text-white group-hover:block dark:bg-white dark:text-zinc-900">
                {{ $formatter ? $formatter($value) : $value }}
            </span>
            <div class="w-full rounded-t-md {{ $barClass }} transition-[height]"
                style="height: {{ $value > 0 ? max(4, round($value / $max * 100)) : 2 }}%"></div>
            <span class="text-[10px] font-medium text-zinc-400 dark:text-zinc-500">{{ $label }}</span>
        </div>
    @endforeach
</div>
