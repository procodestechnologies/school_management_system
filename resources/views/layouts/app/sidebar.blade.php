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
            @if (currentInstitution())
                <div class="px-2 pt-1 pb-2">
                    <flux:text size="sm" class="font-semibold text-zinc-700 dark:text-zinc-200 truncate block">
                        {{ currentInstitution()->name }}
                    </flux:text>
                </div>
            @endif
            <flux:sidebar.group :heading="__('Platform')" class="grid">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
            @hasrole('Admin')
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
                <flux:sidebar.item icon="home-modern" :href="route('institution.index')"
                    :current="request()->routeIs('institution.index')" wire:navigate>
                    {{ __('Institutions') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="credit-card" :href="route('admin.plans.index')"
                    :current="request()->routeIs('admin.plans.*')" wire:navigate>
                    {{ __('Plans') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="envelope" :href="route('messages.index')"
                    :current="request()->routeIs('messages.*')" wire:navigate>
                    {{ __('Messages') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="adjustments-horizontal" :href="route('admin.settings.edit')"
                    :current="request()->routeIs('admin.settings.*')" wire:navigate>
                    {{ __('Site Settings') }}
                </flux:sidebar.item>
            @endhasrole
            @if (isDirector())
                @if (! currentInstitution())
                    {{-- Nothing chosen yet (first login, or owns several
                    schools) - the institutions index, where choosing
                    happens, is the only thing to show until then. --}}
                    <flux:sidebar.item icon="building-library" :href="route('institution.index')"
                        :current="request()->routeIs('institution.*')" wire:navigate>
                        {{ __('Institutions') }}
                    </flux:sidebar.item>
                @else
                    <flux:sidebar.item icon="device-phone-mobile" :href="route('devices.index')"
                        :current="request()->routeIs('devices.index')" wire:navigate>
                        {{ __('Devices') }}
                    </flux:sidebar.item>
                    @if (institutionCanUpgrade())
                        <flux:sidebar.item icon="arrow-trending-up" :href="route('billing.show')"
                            :current="request()->routeIs('billing.*')" wire:navigate>
                            {{ currentInstitution()->hasActiveSubscription() ? __('Upgrade Plan') : __('Choose a Plan') }}
                        </flux:sidebar.item>
                    @endif
                @endif
            @endif
            @if (hasInstitutions() && !isAdmin() && institutionApproved() && currentInstitution())
               
                @hasrole('Student')
                    <flux:sidebar.item icon="clipboard-document-list" :href="route('selections.index')"
                        :current="request()->routeIs('selections.*')" wire:navigate>
                        {{ __('Selections') }}
                    </flux:sidebar.item>
                @endhasrole
                @foreach (Module::allEnabled() as $module)
                    @php
                        $moduleName = strtolower($module->getName());
                        $icon = '';
                        $canAccess = false;

                        switch ($module->getName()) {
                            case 'Curriculum':
                                $icon = 'queue-list';
                                $canAccess =
                                    auth()->user()->can('view curriculum') ||
                                    auth()->user()->can('edit curriculum') ||
                                    auth()->user()->can('create curriculum');
                                break;
                            case 'Attendance':
                                $icon = 'clock';
                                $canAccess =
                                    auth()->user()->can('view attendance') ||
                                    auth()->user()->can('edit attendance') ||
                                    auth()->user()->can('create attendance');
                                break;
                            case 'Examinations':
                                $icon = 'book-open';
                                $canAccess =
                                    auth()->user()->can('view examination') ||
                                    auth()->user()->can('edit examination') ||
                                    auth()->user()->can('create examination');
                                break;
                            case 'FeeManagement':
                                $icon = 'document-currency-dollar';
                                $canAccess =
                                    auth()->user()->can('view feemanagement') ||
                                    auth()->user()->can('edit feemanagement') ||
                                    auth()->user()->can('create feemanagement');
                                break;
                            case 'Institution':
                                $icon = 'building-library';
                                $canAccess =
                                    auth()->user()->can('view institution') ||
                                    auth()->user()->can('edit institution') ||
                                    auth()->user()->can('create institution');
                                break;
                            case 'Parent':
                                $icon = 'user';
                                $canAccess =
                                    auth()->user()->can('view parent') ||
                                    auth()->user()->can('edit parent') ||
                                    auth()->user()->can('create parent');
                                break;
                            case 'Teacher':
                                $icon = 'users';
                                $canAccess =
                                    auth()->user()->can('view teacher') ||
                                    auth()->user()->can('edit teacher') ||
                                    auth()->user()->can('create teacher');
                                break;
                            case 'Staff':
                                $icon = 'identification';
                                $canAccess =
                                    auth()->user()->can('view staff') ||
                                    auth()->user()->can('edit staff') ||
                                    auth()->user()->can('create staff');
                                break;
                            case 'Student':
                                $icon = 'user-group';
                                $canAccess =
                                    auth()->user()->can('view student') ||
                                    auth()->user()->can('edit student') ||
                                    auth()->user()->can('create student');
                                break;
                            case 'Timetable':
                                $icon = 'calendar-days';
                                $canAccess =
                                    auth()->user()->can('view timetable') ||
                                    auth()->user()->can('edit timetable') ||
                                    auth()->user()->can('create timetable');
                                break;
                            case 'Report':
                                $icon = 'chart-bar';
                                $canAccess =
                                    auth()->user()->can('view report') ||
                                    auth()->user()->can('create report') ||
                                    auth()->user()->can('export report');
                                break;
                            case 'Classes':
                                $icon = 'rectangle-group';
                                $canAccess =
                                    auth()->user()->can('view classes') ||
                                    auth()->user()->can('edit classes') ||
                                    auth()->user()->can('create classes');
                                break;
                            case 'Lesson':
                                $icon = 'clipboard-document-check';
                                $canAccess =
                                    auth()->user()->can('view lesson') ||
                                    auth()->user()->can('edit lesson') ||
                                    auth()->user()->can('create lesson');
                                break;
                            case 'Subject':
                                $icon = 'book-open';
                                $canAccess =
                                    auth()->user()->can('view subject') ||
                                    auth()->user()->can('edit subject') ||
                                    auth()->user()->can('create subject');
                                break;
                            case 'Result':
                                $icon = 'academic-cap';
                                $canAccess =
                                    auth()->user()->can('view result') ||
                                    auth()->user()->can('edit result') ||
                                    auth()->user()->can('create result');
                                break;
                            default:
                                $icon = 'folder';
                                $canAccess =
                                    auth()
                                        ->user()
                                        ->can('view ' . $moduleName) ||
                                    auth()
                                        ->user()
                                        ->can('edit ' . $moduleName) ||
                                    auth()
                                        ->user()
                                        ->can('create ' . $moduleName);
                                break;
                        }
                    @endphp

                    @if ($canAccess && institutionHasModule($module->getName()))
                        <flux:sidebar.item icon="{{ $icon }}"
                            :href="route($moduleName.
                                '.index')"
                            :current="request()->routeIs($moduleName . '.index')" wire:navigate>
                            {{ __($module->getName()) }}
                        </flux:sidebar.item>
                    @endif
                @endforeach

                {{-- Payroll lives inside the Staff module but is a section of
                its own - it's the Accountant's half of that module, so it
                needs its own entry rather than the auto-generated one. --}}
                @if (auth()->user()->can('view payroll') && institutionHasModule('Staff'))
                    <flux:sidebar.item icon="banknotes" :href="route('staff.payments.index')"
                        :current="request()->routeIs('staff.payments.*')" wire:navigate>
                        {{ __('Payroll') }}
                    </flux:sidebar.item>
                @endif
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
