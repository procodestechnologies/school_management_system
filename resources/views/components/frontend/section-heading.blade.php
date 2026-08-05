@props([
    'eyebrow' => null,
    'description' => null,
    'align' => 'center',
])

<div {{ $attributes->class(['max-w-2xl', $align === 'center' ? 'mx-auto text-center' : '']) }}>
    @if ($eyebrow)
        <p class="text-sm font-semibold tracking-wide text-indigo-600 uppercase dark:text-indigo-400">
            {{ $eyebrow }}
        </p>
    @endif

    <h2 class="mt-3 text-3xl font-semibold tracking-tight text-balance text-zinc-900 sm:text-4xl dark:text-white">
        {{ $slot }}
    </h2>

    @if ($description)
        <p class="mt-4 text-lg text-pretty text-zinc-600 dark:text-zinc-400">
            {{ $description }}
        </p>
    @endif
</div>
