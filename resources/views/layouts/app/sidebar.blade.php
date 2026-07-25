<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>
@php
    use Nwidart\Modules\Facades\Module;
@endphp

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Platform')" class="grid">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
            <flux:sidebar.group icon="rectangle-stack" :heading="__('Modules')" class="grid" expandable
                :expanded="request()->routeIs('admin.modules*')">
                <flux:sidebar.item icon="eye" :href="route('admin.modules.index')"
                    :current="request()->routeIs('admin.modules.index')" wire:navigate>
                    {{ __('View All') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="plus" :href="route('admin.modules.create')"
                    :current="request()->routeIs('admin.modules.create')" wire:navigate>
                    {{ __('Create Module') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
            @if (auth()->user()->role('Director'))
                @foreach (Module::allEnabled() as $module)
                    @php
                        $icon = '';
                        if ($module->getName() === '') {
                        }
                        switch ($module->getName()) {
                            case 'Curriculum':
                                $icon = 'queue-list';
                                break;
                            case 'Attendance':
                                $icon = 'clock';
                                break;
                            case 'Examinations':
                                $icon = 'book-open';
                                break;
                            case 'FeeManagement':
                                $icon = 'document-currency-dollar';
                                break;
                            case 'Institution':
                                $icon = 'building-library';
                                break;
                            case 'Parent':
                                $icon = 'user';
                                break;
                            case 'Teacher':
                                $icon = 'users';
                                break;
                            case 'Student':
                                $icon = 'user-group';
                                break;
                            case 'Timetable':
                                $icon = 'calendar-days';
                                break;

                            default:
                                # code...
                                break;
                        }
                    @endphp
                    <flux:sidebar.item icon="{{ $icon }}"
                        :href="route(strtolower($module->getName()) . '.index')"
                        :current="request()->routeIs(strtolower($module->getName()).'.index')" wire:navigate>
                        {{ __($module->getName()) }}
                    </flux:sidebar.item>
                @endforeach

            @endif
        </flux:sidebar.nav>

        <flux:spacer />



        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
