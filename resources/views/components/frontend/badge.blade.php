@props([
    'icon' => null,
])

<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-3 py-1 text-xs font-medium text-zinc-600 dark:border-white/10 dark:bg-white/5 dark:text-zinc-300']) }}>
    @if ($icon)
        <flux:icon :icon="$icon" class="size-3.5" />
    @endif
    {{ $slot }}
</span>
