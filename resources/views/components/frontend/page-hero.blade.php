@props([
    'eyebrow' => null,
    'description' => null,
])

<section class="relative overflow-hidden pt-16 pb-14 sm:pt-20 sm:pb-20">
    <div class="pointer-events-none absolute inset-x-0 -top-32 -z-10 flex justify-center overflow-hidden blur-3xl">
        <div class="aspect-1155/678 w-[60rem] bg-gradient-to-tr from-indigo-300 to-zinc-100 opacity-25 dark:from-indigo-900 dark:to-zinc-900 dark:opacity-30"
            style="clip-path: polygon(74% 44%, 100% 61%, 97% 26%, 85% 0%, 80% 2%, 72% 32%, 60% 62%, 32% 0%, 0% 5%, 12% 45%, 29% 70%, 22% 100%, 63% 90%)">
        </div>
    </div>

    <x-frontend.container>
        <div class="mx-auto max-w-2xl text-center" data-animate>
            @if ($eyebrow)
                <p class="text-sm font-semibold tracking-wide text-indigo-600 uppercase dark:text-indigo-400">
                    {{ $eyebrow }}
                </p>
            @endif

            <h1 class="mt-3 text-4xl font-semibold tracking-tight text-balance text-zinc-900 sm:text-5xl dark:text-white">
                {{ $slot }}
            </h1>

            @if ($description)
                <p class="mt-5 text-lg text-pretty text-zinc-600 dark:text-zinc-400">
                    {{ $description }}
                </p>
            @endif
        </div>
    </x-frontend.container>
</section>
