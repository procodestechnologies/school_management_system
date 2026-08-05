@props([
    'icon' => null,
    'title' => null,
])

<div {{ $attributes->class(['group relative rounded-2xl border border-zinc-200 bg-white p-6 transition hover:-translate-y-0.5 hover:border-zinc-300 hover:shadow-lg hover:shadow-zinc-900/5 dark:border-white/10 dark:bg-white/[0.03] dark:hover:border-white/20']) }}>
    <div class="flex size-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
        <flux:icon :icon="$icon" class="size-6" />
    </div>

    <h3 class="mt-4 text-base font-semibold text-zinc-900 dark:text-white">
        {{ $title }}
    </h3>

    <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
        {{ $slot }}
    </p>
</div>
