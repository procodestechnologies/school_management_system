@props([
    'heading' => null,
    'description' => null,
])

<div {{ $attributes->class(['dark relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-zinc-900 via-zinc-900 to-indigo-950 px-6 py-16 text-center shadow-2xl sm:px-16']) }}>
    <div class="pointer-events-none absolute top-1/2 left-1/2 -z-10 size-96 -translate-x-1/2 -translate-y-1/2 rounded-full bg-indigo-500/20 blur-3xl"></div>

    <h2 class="mx-auto max-w-2xl text-3xl font-semibold tracking-tight text-balance text-white sm:text-4xl">
        {{ $heading }}
    </h2>

    @if ($description)
        <p class="mx-auto mt-4 max-w-xl text-base text-pretty text-zinc-300">
            {{ $description }}
        </p>
    @endif

    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
        {{ $slot }}
    </div>
</div>
