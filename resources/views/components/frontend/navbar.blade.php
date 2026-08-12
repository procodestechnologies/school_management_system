@php
    $navLinks = [
        'home' => 'Home',
        'about' => 'About',
        'services' => 'Services',
        'plans' => 'Plans',
        'contact' => 'Contact',
    ];
@endphp

<header x-data="{ mobileOpen: false, scrolled: false }" x-init="scrolled = window.scrollY > 8"
    x-on:scroll.window="scrolled = window.scrollY > 8" x-on:keydown.escape.window="mobileOpen = false"
    class="sticky top-0 z-50 border-b border-transparent bg-white/60 backdrop-blur-md transition-all duration-300 dark:bg-zinc-950/60"
    :class="scrolled ? 'border-zinc-200/70 bg-white/85 shadow-sm dark:border-white/10 dark:bg-zinc-950/85' : ''">
    <x-frontend.container>
        <nav class="flex h-16 items-center justify-between">
            <a href="{{ route('home') }}" wire:navigate
                class="flex items-center gap-2 font-semibold text-zinc-900 transition hover:opacity-80 dark:text-white">
                <img
                    src="{{ asset('logos/solforbs-logo.png') }}"
                    alt="{{ config('app.name', 'Laravel') }}"
                    class="size-10 w-48 object-contain"
                />
                {{ config('app.name') }}
            </a>

            <div class="hidden lg:flex lg:items-center lg:gap-1">
                @foreach ($navLinks as $route => $label)
                    <a href="{{ route($route) }}" wire:navigate
                        @class([
                            'relative rounded-lg px-3 py-2 text-sm font-medium transition',
                            'text-zinc-900 dark:text-white' => request()->routeIs($route),
                            'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white' => ! request()->routeIs($route),
                        ])>
                        {{ $label }}
                        @if (request()->routeIs($route))
                            <span class="absolute inset-x-3 -bottom-[1px] h-0.5 rounded-full bg-indigo-600 dark:bg-indigo-400"></span>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="hidden lg:flex lg:items-center lg:gap-3">
                @auth
                    <flux:button :href="route('dashboard')" variant="primary" wire:navigate>
                        Dashboard
                    </flux:button>
                @else
                    <flux:button :href="route('login')" variant="ghost" wire:navigate>
                        Log in
                    </flux:button>
                    <flux:button :href="route('register')" variant="primary" wire:navigate>
                        Get started
                    </flux:button>
                @endauth
            </div>

            <button type="button" x-on:click="mobileOpen = ! mobileOpen"
                class="flex size-10 items-center justify-center rounded-lg text-zinc-600 hover:bg-zinc-100 lg:hidden dark:text-zinc-300 dark:hover:bg-white/10"
                :aria-expanded="mobileOpen" aria-label="Toggle navigation menu">
                <flux:icon x-show="! mobileOpen" icon="bars-3" class="size-6" />
                <flux:icon x-show="mobileOpen" x-cloak icon="x-mark" class="size-6" />
            </button>
        </nav>
    </x-frontend.container>

    <div x-show="mobileOpen" x-cloak x-transition.opacity.duration.150ms
        class="border-t border-zinc-200 bg-white lg:hidden dark:border-white/10 dark:bg-zinc-950">
        <x-frontend.container class="flex flex-col gap-1 py-4">
            @foreach ($navLinks as $route => $label)
                <a href="{{ route($route) }}" wire:navigate x-on:click="mobileOpen = false"
                    @class([
                        'rounded-lg px-3 py-2.5 text-sm font-medium',
                        'bg-zinc-100 text-zinc-900 dark:bg-white/10 dark:text-white' => request()->routeIs($route),
                        'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-white/5' => ! request()->routeIs($route),
                    ])>
                    {{ $label }}
                </a>
            @endforeach

            <div class="mt-3 flex flex-col gap-2 border-t border-zinc-200 pt-3 dark:border-white/10">
                @auth
                    <flux:button :href="route('dashboard')" variant="primary" class="w-full" wire:navigate>
                        Dashboard
                    </flux:button>
                @else
                    <flux:button :href="route('login')" variant="outline" class="w-full" wire:navigate>
                        Log in
                    </flux:button>
                    <flux:button :href="route('register')" variant="primary" class="w-full" wire:navigate>
                        Get started
                    </flux:button>
                @endauth
            </div>
        </x-frontend.container>
    </div>
</header>
