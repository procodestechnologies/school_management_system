<x-layouts::frontend.app title="Contact">
    <x-frontend.page-hero eyebrow="Contact"
        description="Tell us about your school and what you need, and our team will get back to you.">
        Let's talk about your school
    </x-frontend.page-hero>

    <section class="pb-20 sm:pb-28">
        <x-frontend.container class="grid gap-16 lg:grid-cols-5">
            <div class="lg:col-span-2" data-animate="left">
                <h2 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-white">
                    What can we help with?
                </h2>
                <p class="mt-3 text-zinc-600 dark:text-zinc-400">
                    Pick the topic that fits best and share a few details — our team will get back to you.
                </p>

                <ul class="mt-8 space-y-5">
                    <li class="flex gap-3">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                            <flux:icon icon="building-office-2" class="size-5" />
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">New school / sign up</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Bringing your school onto the platform, or questions about plans.</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                            <flux:icon icon="shield-check" class="size-5" />
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">Support for an existing school</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Already on the platform and need a hand with something.</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                            <flux:icon icon="chat-bubble-left-right" class="size-5" />
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">General inquiry</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Anything else you'd like to ask.</p>
                        </div>
                    </li>
                </ul>

                <div class="mt-10 rounded-2xl border border-zinc-200 bg-zinc-50 p-5 dark:border-white/10 dark:bg-white/[0.03]">
                    <p class="text-sm font-medium text-zinc-900 dark:text-white">
                        Parent of a current student?
                    </p>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        For results, attendance or fee balances, try the chat assistant in the corner of your
                        screen — it's faster than waiting for a reply here.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-3" data-animate="right">
                <div class="rounded-3xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-white/10 dark:bg-white/[0.03]">
                    <livewire:contact-form />
                </div>
            </div>
        </x-frontend.container>
    </section>
</x-layouts::frontend.app>
