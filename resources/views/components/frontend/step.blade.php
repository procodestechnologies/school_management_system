@props([
    'number' => null,
    'title' => null,
])

<div {{ $attributes->class(['relative flex gap-4']) }}>
    <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-zinc-900 text-sm font-semibold text-white dark:bg-white dark:text-zinc-900">
        {{ $number }}
    </div>
    <div>
        <h3 class="text-base font-semibold text-zinc-900 dark:text-white">
            {{ $title }}
        </h3>
        <p class="mt-1 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
            {{ $slot }}
        </p>
    </div>
</div>
