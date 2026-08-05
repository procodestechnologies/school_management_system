@props([
    'value' => null,
    'label' => null,
])

<div {{ $attributes->class(['text-center']) }}>
    <dt class="text-3xl font-semibold tracking-tight text-zinc-900 sm:text-4xl dark:text-white">
        {{ $value }}
    </dt>
    <dd class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
        {{ $label }}
    </dd>
</div>
