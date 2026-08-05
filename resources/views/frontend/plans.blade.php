<x-layouts::frontend.app title="Plans">
    <x-frontend.page-hero eyebrow="Plans"
        description="Example pricing shown below for illustration — actual rates depend on your school's size and needs. Get in touch and we'll confirm a quote.">
        Simple pricing that grows with your school
    </x-frontend.page-hero>

    <section class="py-4 sm:py-8">
        <x-frontend.container>
            <div class="grid gap-8 lg:grid-cols-3 lg:items-start">
                <x-frontend.pricing-card name="Starter" price="KES 15,000" period="/term"
                    description="For schools just getting started with digital records."
                    :ctaHref="route('register')" ctaLabel="Get started free" data-animate
                    style="--reveal-delay:0ms">
                    <x-frontend.check-item>Up to 300 students</x-frontend.check-item>
                    <x-frontend.check-item>Admissions & student records</x-frontend.check-item>
                    <x-frontend.check-item>Attendance — manual or biometric</x-frontend.check-item>
                    <x-frontend.check-item>Fee tracking per student</x-frontend.check-item>
                    <x-frontend.check-item>Exams & report cards</x-frontend.check-item>
                    <x-frontend.check-item>Email support</x-frontend.check-item>
                </x-frontend.pricing-card>

                <x-frontend.pricing-card name="Growth" price="KES 35,000" period="/term"
                    description="For schools ready to run everything in one place." :featured="true"
                    :ctaHref="route('register')" ctaLabel="Get started free" data-animate="scale"
                    style="--reveal-delay:90ms">
                    <x-frontend.check-item>Up to 1,000 students</x-frontend.check-item>
                    <x-frontend.check-item>Everything in Starter</x-frontend.check-item>
                    <x-frontend.check-item>M-Pesa fee payments</x-frontend.check-item>
                    <x-frontend.check-item>Parent chat assistant</x-frontend.check-item>
                    <x-frontend.check-item>Timetable & class management</x-frontend.check-item>
                    <x-frontend.check-item>Role-based staff accounts</x-frontend.check-item>
                    <x-frontend.check-item>Priority support</x-frontend.check-item>
                </x-frontend.pricing-card>

                <x-frontend.pricing-card name="Enterprise" price="Custom" :period="null"
                    description="For multi-branch institutions and larger deployments."
                    :ctaHref="route('contact')" ctaLabel="Talk to us" data-animate
                    style="--reveal-delay:180ms">
                    <x-frontend.check-item>Unlimited students</x-frontend.check-item>
                    <x-frontend.check-item>Everything in Growth</x-frontend.check-item>
                    <x-frontend.check-item>Multi-branch / multi-institution support</x-frontend.check-item>
                    <x-frontend.check-item>Dedicated onboarding</x-frontend.check-item>
                    <x-frontend.check-item>Custom integrations</x-frontend.check-item>
                </x-frontend.pricing-card>
            </div>

            <p class="mx-auto mt-10 max-w-2xl text-center text-sm text-zinc-500 dark:text-zinc-500">
                Prices above are illustrative examples, not confirmed rates. <a href="{{ route('contact') }}"
                    wire:navigate class="font-medium text-indigo-600 hover:underline dark:text-indigo-400">Contact us</a>
                for pricing based on your school's size.
            </p>
        </x-frontend.container>
    </section>

    <section class="py-20 sm:py-28">
        <x-frontend.container>
            <x-frontend.section-heading eyebrow="Every plan includes" data-animate>
                The essentials, no matter your size
            </x-frontend.section-heading>

            <div class="mt-16 grid gap-6 sm:grid-cols-3">
                <x-frontend.feature-card icon="lock-closed" title="An isolated workspace" data-animate style="--reveal-delay:0ms">
                    Your school's students, staff and records stay in their own private space on the platform.
                </x-frontend.feature-card>
                <x-frontend.feature-card icon="shield-check" title="Role-based access" data-animate style="--reveal-delay:80ms">
                    Every staff account only sees what its role allows, from day one.
                </x-frontend.feature-card>
                <x-frontend.feature-card icon="chat-bubble-left-right" title="The parent assistant" data-animate style="--reveal-delay:160ms">
                    Verified, self-service answers for parents on every plan, not just the higher tiers.
                </x-frontend.feature-card>
            </div>
        </x-frontend.container>
    </section>

    <section class="bg-zinc-50 py-20 sm:py-28 dark:bg-white/[0.02]">
        <x-frontend.container class="max-w-3xl">
            <x-frontend.section-heading eyebrow="Questions" data-animate>
                A few things schools usually ask
            </x-frontend.section-heading>

            <div class="mt-12 space-y-8" data-animate>
                <div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-white">How does school approval work?</h3>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                        After you register and set up your school's workspace, our team reviews and activates it
                        before your staff can sign in and start using it.
                    </p>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-white">Can I change plans later?</h3>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                        Yes — reach out through the contact page and we'll adjust your plan as your school grows.
                    </p>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-white">Is my school's data separate from other schools?</h3>
                    <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                        Yes. Every institution gets its own isolated workspace — students, staff and records are
                        never shared across schools.
                    </p>
                </div>
            </div>
        </x-frontend.container>
    </section>

    <section class="py-20 sm:py-28">
        <x-frontend.container>
            <x-frontend.cta-band heading="Not sure which plan fits?" description="Tell us about your school and we'll help you figure it out.">
                <flux:button :href="route('contact')" variant="primary" wire:navigate>
                    Talk to us
                </flux:button>
                <flux:button :href="route('register')" variant="ghost" wire:navigate>
                    Get started free
                </flux:button>
            </x-frontend.cta-band>
        </x-frontend.container>
    </section>
</x-layouts::frontend.app>
