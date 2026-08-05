@props([
    'icon' => null,
    'title' => null,
])

<div {{ $attributes->class(['rounded-2xl border border-zinc-200 bg-white p-6 dark:border-white/10 dark:bg-white/[0.03]']) }}>
    <div class="flex items-center gap-3">
        <div class="flex size-9 items-center justify-center rounded-lg bg-zinc-900 text-white dark:bg-white dark:text-zinc-900">
            <flux:icon :icon="$icon" class="size-5" />
        </div>
        <h3 class="text-base font-semibold text-zinc-900 dark:text-white">
            {{ $title }}
        </h3>
    </div>

    <ul class="mt-4 space-y-2.5 text-sm text-zinc-600 dark:text-zinc-400">
        {{ $slot }}
    </ul>
</div>
