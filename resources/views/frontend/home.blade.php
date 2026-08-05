@php
    $chatbotCommands = config('chatbot.commands', []);
    $verifiedCommandCount = collect($chatbotCommands)->where('verified', true)->count();
@endphp

<x-layouts::frontend.app>
    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 -top-40 -z-10 flex justify-center overflow-hidden blur-3xl">
            <div class="aspect-1155/678 w-[72rem] bg-gradient-to-tr from-indigo-300 to-zinc-100 opacity-30 dark:from-indigo-900 dark:to-zinc-900 dark:opacity-40"
                style="clip-path: polygon(74% 44%, 100% 61%, 97% 26%, 85% 0%, 80% 2%, 72% 32%, 60% 62%, 32% 0%, 0% 5%, 12% 45%, 29% 70%, 22% 100%, 63% 90%)">
            </div>
        </div>

        <x-frontend.container class="grid gap-16 pt-16 pb-20 lg:grid-cols-2 lg:items-center lg:pt-24 lg:pb-28">
            <div data-animate="left">
                <x-frontend.badge icon="sparkles">
                    One platform, every school office
                </x-frontend.badge>

                <h1 class="mt-6 text-4xl font-semibold tracking-tight text-balance text-zinc-900 sm:text-5xl dark:text-white">
                    Run admissions, attendance, fees and exams —
                    <span class="text-indigo-600 dark:text-indigo-400">without the spreadsheets.</span>
                </h1>

                <p class="mt-6 max-w-xl text-lg text-pretty text-zinc-600 dark:text-zinc-400">
                    {{ config('app.name') }} gives administrators, teachers and parents one shared, secure
                    workspace to manage a school from enrollment all the way through to the report card.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <flux:button :href="route('register')" variant="primary" wire:navigate>
                        Get started free
                    </flux:button>
                    <flux:button href="#how-it-works" variant="outline">
                        See how it works
                    </flux:button>
                </div>

                <p class="mt-4 text-sm text-zinc-500 dark:text-zinc-500">
                    Create an account, set up your school's workspace, and our team reviews and activates it.
                </p>

                <div class="mt-10 flex flex-wrap gap-2">
                    @foreach (['Admissions', 'Attendance', 'Fees & Payments', 'Exams & Report Cards', 'Timetable', 'Parent Portal'] as $module)
                        <span class="rounded-full border border-zinc-200 px-3 py-1 text-xs font-medium text-zinc-600 dark:border-white/10 dark:text-zinc-400">
                            {{ $module }}
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="relative" data-animate="right" style="--reveal-delay:120ms">
                <div class="rounded-2xl border border-zinc-200 bg-white p-2 shadow-2xl shadow-zinc-900/10 dark:border-white/10 dark:bg-zinc-900">
                    <div class="flex items-center gap-1.5 px-3 py-2.5">
                        <span class="size-2.5 rounded-full bg-red-400"></span>
                        <span class="size-2.5 rounded-full bg-amber-400"></span>
                        <span class="size-2.5 rounded-full bg-emerald-400"></span>
                        <span class="ml-3 text-xs font-medium text-zinc-400">Riverside Academy · Overview</span>
                    </div>

                    <div class="grid grid-cols-3 gap-3 rounded-xl bg-zinc-50 p-4 dark:bg-white/5">
                        <div class="col-span-2 space-y-4 rounded-lg bg-white p-4 shadow-sm dark:bg-zinc-800">
                            <div class="flex items-center justify-between">
                                <div class="h-2.5 w-24 rounded-full bg-zinc-200 dark:bg-white/10"></div>
                                <div class="h-5 w-14 rounded-full bg-indigo-100 dark:bg-indigo-500/20"></div>
                            </div>
                            <div class="space-y-2">
                                <div class="h-2 w-full rounded-full bg-zinc-100 dark:bg-white/5"></div>
                                <div class="h-2 w-5/6 rounded-full bg-zinc-100 dark:bg-white/5"></div>
                                <div class="h-2 w-4/6 rounded-full bg-zinc-100 dark:bg-white/5"></div>
                            </div>
                            <div class="flex items-end gap-1.5 pt-2">
                                <div class="h-8 w-4 rounded bg-indigo-200 dark:bg-indigo-500/30"></div>
                                <div class="h-12 w-4 rounded bg-indigo-300 dark:bg-indigo-500/50"></div>
                                <div class="h-6 w-4 rounded bg-indigo-200 dark:bg-indigo-500/30"></div>
                                <div class="h-16 w-4 rounded bg-indigo-500"></div>
                                <div class="h-10 w-4 rounded bg-indigo-300 dark:bg-indigo-500/50"></div>
                                <div class="h-14 w-4 rounded bg-indigo-400 dark:bg-indigo-500/70"></div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="rounded-lg bg-white p-3 shadow-sm dark:bg-zinc-800">
                                <flux:icon icon="check-circle" class="size-4 text-emerald-500" />
                                <div class="mt-2 h-2 w-14 rounded-full bg-zinc-200 dark:bg-white/10"></div>
                            </div>
                            <div class="rounded-lg bg-white p-3 shadow-sm dark:bg-zinc-800">
                                <flux:icon icon="bell-alert" class="size-4 text-amber-500" />
                                <div class="mt-2 h-2 w-16 rounded-full bg-zinc-200 dark:bg-white/10"></div>
                            </div>
                            <div class="rounded-lg bg-white p-3 shadow-sm dark:bg-zinc-800">
                                <flux:icon icon="banknotes" class="size-4 text-indigo-500" />
                                <div class="mt-2 h-2 w-10 rounded-full bg-zinc-200 dark:bg-white/10"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="animate-float absolute -left-6 top-8 hidden items-center gap-2 rounded-xl border border-zinc-200 bg-white px-3 py-2 shadow-lg sm:flex dark:border-white/10 dark:bg-zinc-800">
                    <flux:icon icon="finger-print" class="size-4 text-indigo-600 dark:text-indigo-400" />
                    <span class="text-xs font-medium text-zinc-700 dark:text-zinc-200">Attendance synced</span>
                </div>
                <div class="animate-float-delayed absolute -right-4 bottom-6 hidden items-center gap-2 rounded-xl border border-zinc-200 bg-white px-3 py-2 shadow-lg sm:flex dark:border-white/10 dark:bg-zinc-800">
                    <flux:icon icon="chat-bubble-left-right" class="size-4 text-emerald-600 dark:text-emerald-400" />
                    <span class="text-xs font-medium text-zinc-700 dark:text-zinc-200">Parent verified</span>
                </div>
            </div>
        </x-frontend.container>
    </section>

    {{-- Features --}}
    <section id="features" class="scroll-mt-20 py-20 sm:py-28">
        <x-frontend.container>
            <x-frontend.section-heading eyebrow="Platform" data-animate
                description="Every module your school office already juggles — connected instead of scattered across paper files and spreadsheets.">
                One system for the entire school office
            </x-frontend.section-heading>

            @php
                $homeFeatures = [
                    ['icon' => 'academic-cap', 'title' => 'Students & Admissions', 'body' => "Enroll students, keep guardian and address details in one place, and give every learner a single academic profile that follows them through the school."],
                    ['icon' => 'finger-print', 'title' => 'Biometric Attendance', 'body' => 'Connect ZKTeco biometric devices for tap-in attendance that syncs automatically — no manual registers to reconcile at the end of the day.'],
                    ['icon' => 'banknotes', 'title' => 'Fees & M-Pesa Payments', 'body' => "Bill fees per term, track balances per student, and accept M-Pesa payments directly so the finance office isn't chasing paper receipts."],
                    ['icon' => 'clipboard-document-check', 'title' => 'Exams & Report Cards', 'body' => 'Record marks, compute grades, and generate report cards that teachers, parents and administrators can all trust.'],
                    ['icon' => 'calendar-days', 'title' => 'Timetable & Classes', 'body' => 'Build class timetables, assign subjects and teachers, and keep every lesson slot conflict-free across the term.'],
                    ['icon' => 'chat-bubble-left-right', 'title' => 'Parent Portal & Alerts', 'body' => 'Parents get a secure self-service assistant for results, attendance and fee balances — verified by a one-time code, every time.'],
                ];
            @endphp

            <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($homeFeatures as $feature)
                    <x-frontend.feature-card :icon="$feature['icon']" :title="$feature['title']" data-animate
                        style="--reveal-delay:{{ $loop->index * 70 }}ms">
                        {{ $feature['body'] }}
                    </x-frontend.feature-card>
                @endforeach
            </div>
        </x-frontend.container>
    </section>

    {{-- How it works --}}
    <section id="how-it-works" class="scroll-mt-20 bg-zinc-50 py-20 sm:py-28 dark:bg-white/[0.02]">
        <x-frontend.container>
            <x-frontend.section-heading eyebrow="Getting started" data-animate
                description="No lengthy onboarding project — most schools are recording attendance and fees within their first week.">
                From sign-up to your first school day
            </x-frontend.section-heading>

            <div class="mx-auto mt-16 grid max-w-4xl gap-x-12 gap-y-10 sm:grid-cols-2">
                <x-frontend.step number="1" title="Create your account" data-animate style="--reveal-delay:0ms">
                    Register as your school's administrator in minutes — just a name, email and password.
                </x-frontend.step>

                <x-frontend.step number="2" title="Set up your workspace" data-animate style="--reveal-delay:80ms">
                    Add your institution's details. Our team reviews and activates every new school before it
                    goes live.
                </x-frontend.step>

                <x-frontend.step number="3" title="Bring in students, staff & classes" data-animate style="--reveal-delay:160ms">
                    Add students, teachers, classes and subjects, or import the ones you already track elsewhere.
                </x-frontend.step>

                <x-frontend.step number="4" title="Run your term" data-animate style="--reveal-delay:240ms">
                    Track attendance, collect fees, record exam results, and keep parents updated automatically.
                </x-frontend.step>
            </div>
        </x-frontend.container>
    </section>

    {{-- Roles --}}
    <section id="roles" class="scroll-mt-20 py-20 sm:py-28">
        <x-frontend.container>
            <x-frontend.section-heading eyebrow="Built for your whole team" data-animate
                description="Administrators, teachers and parents each get exactly the view they need — nothing more, nothing overwhelming.">
                Everyone gets the right view
            </x-frontend.section-heading>

            <div class="mt-16 grid gap-6 lg:grid-cols-3">
                <x-frontend.role-card icon="building-office-2" title="Administrators" data-animate style="--reveal-delay:0ms">
                    <x-frontend.check-item>Approve enrollments and oversee every module from one dashboard</x-frontend.check-item>
                    <x-frontend.check-item>Manage staff accounts, roles and permissions</x-frontend.check-item>
                    <x-frontend.check-item>See school-wide attendance, fees and exam reports</x-frontend.check-item>
                </x-frontend.role-card>

                <x-frontend.role-card icon="user-group" title="Teachers" data-animate style="--reveal-delay:90ms">
                    <x-frontend.check-item>Record attendance and exam marks for their classes</x-frontend.check-item>
                    <x-frontend.check-item>View timetables and lesson assignments at a glance</x-frontend.check-item>
                    <x-frontend.check-item>Keep student academic records up to date</x-frontend.check-item>
                </x-frontend.role-card>

                <x-frontend.role-card icon="device-phone-mobile" title="Parents" data-animate style="--reveal-delay:180ms">
                    <x-frontend.check-item>Check results, attendance and fee balances instantly</x-frontend.check-item>
                    <x-frontend.check-item>Verified securely with a one-time code — no account needed</x-frontend.check-item>
                    <x-frontend.check-item>Available right from the chat assistant, any time</x-frontend.check-item>
                </x-frontend.role-card>
            </div>
        </x-frontend.container>
    </section>

    {{-- Assistant spotlight --}}
    <section id="assistant" class="scroll-mt-20 bg-zinc-50 py-20 sm:py-28 dark:bg-white/[0.02]">
        <x-frontend.container class="grid gap-16 lg:grid-cols-2 lg:items-center">
            <div data-animate="left">
                <p class="text-sm font-semibold tracking-wide text-indigo-600 uppercase dark:text-indigo-400">
                    Parent self-service
                </p>
                <h2 class="mt-3 text-3xl font-semibold tracking-tight text-balance text-zinc-900 sm:text-4xl dark:text-white">
                    A real assistant, not a demo
                </h2>
                <p class="mt-4 text-lg text-pretty text-zinc-600 dark:text-zinc-400">
                    Every page on this site — including this one — has the same assistant your parents will use.
                    Click the chat bubble in the corner and try a command like <code class="rounded bg-zinc-200 px-1.5 py-0.5 text-sm dark:bg-white/10">/result</code>
                    or <code class="rounded bg-zinc-200 px-1.5 py-0.5 text-sm dark:bg-white/10">/fees</code>.
                </p>

                <dl class="mt-10 grid grid-cols-3 gap-4 border-t border-zinc-200 pt-8 dark:border-white/10">
                    <x-frontend.stat :value="config('chatbot.otp_length').'-digit'" label="one-time codes" />
                    <x-frontend.stat :value="config('chatbot.otp_expiry_minutes').' min'" label="code expiry" />
                    <x-frontend.stat :value="$verifiedCommandCount" label="self-service commands" />
                </dl>
            </div>

            <div class="relative" data-animate="right">
                <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-xl dark:border-white/10 dark:bg-zinc-900">
                    <div class="flex items-center gap-2 border-b border-zinc-100 pb-3 dark:border-white/10">
                        <span class="flex size-8 items-center justify-center rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-900">
                            <flux:icon icon="chat-bubble-left-right" class="size-4" />
                        </span>
                        <span class="text-sm font-semibold text-zinc-900 dark:text-white">
                            {{ config('app.name') }} Assistant
                        </span>
                    </div>

                    <div class="mt-3 flex justify-start">
                        <div class="max-w-[85%] rounded-2xl bg-zinc-100 px-3 py-2 text-sm text-zinc-800 dark:bg-white/10 dark:text-zinc-100">
                            Hi! I'm the school assistant. Type / to see what I can help with.
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-1.5">
                        @foreach ($chatbotCommands as $key => $definition)
                            <span title="{{ $definition['description'] }}"
                                class="rounded-full border border-zinc-200 px-2.5 py-1 text-xs text-zinc-600 dark:border-white/10 dark:text-zinc-300">
                                /{{ $key }}
                            </span>
                        @endforeach
                    </div>

                    <p class="mt-4 text-xs text-zinc-400 dark:text-zinc-500">
                        ↘ This is a preview — the real thing is live in the corner of your screen.
                    </p>
                </div>
            </div>
        </x-frontend.container>
    </section>

    {{-- Security --}}
    <section class="py-20 sm:py-28">
        <x-frontend.container>
            <x-frontend.section-heading eyebrow="Trust & security" data-animate
                description="Every school's data lives in its own workspace, and every sensitive request is checked before anything is shared.">
                Built to be trusted with student records
            </x-frontend.section-heading>

            <div class="mt-16 grid gap-6 sm:grid-cols-3">
                <x-frontend.feature-card icon="lock-closed" title="Private by design" data-animate style="--reveal-delay:0ms">
                    Every institution's students, staff and records live in their own isolated workspace on the
                    platform.
                </x-frontend.feature-card>

                <x-frontend.feature-card icon="shield-check" title="Granular roles" data-animate style="--reveal-delay:80ms">
                    Fine-grained permissions mean staff only ever see what their role allows them to.
                </x-frontend.feature-card>

                <x-frontend.feature-card icon="finger-print" title="Verified parent access" data-animate style="--reveal-delay:160ms">
                    Every sensitive request from a parent is verified with a one-time emailed code before any
                    data is shared.
                </x-frontend.feature-card>
            </div>
        </x-frontend.container>
    </section>

    {{-- Final CTA --}}
    <section class="pb-20 sm:pb-28">
        <x-frontend.container data-animate="scale">
            <x-frontend.cta-band heading="Ready to bring your school onto one platform?"
                description="Create your account and set up your school's workspace today.">
                <flux:button :href="route('register')" variant="primary" wire:navigate>
                    Get started free
                </flux:button>
                <flux:button :href="route('login')" variant="ghost" wire:navigate>
                    Log in
                </flux:button>
            </x-frontend.cta-band>
        </x-frontend.container>
    </section>
</x-layouts::frontend.app>
