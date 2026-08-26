@php
    $currency = fn($v) => number_format((float) $v, 2);
    $pct = fn($num, $den) => $den > 0 ? min(100, round(($num / $den) * 100)) : 0;
    $scope = $stats['scope'] ?? null;
    $roleName = auth()->user()->getRoleNames()->first();
@endphp

<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">

        <x-billing-banner />

        <div class="flex flex-col gap-1">
            <flux:heading size="xl">Welcome back, {{ explode(' ', auth()->user()->name)[0] }}</flux:heading>
            <flux:text class="text-zinc-500">
                @if ($roleName)
                    You're signed in as <span class="font-medium">{{ $roleName }}</span>.
                @endif
                @can('view report')
                    <a href="{{ route('report.index') }}" class="text-blue-600 hover:underline" wire:navigate>View full reports
                        &rarr;</a>
                @endcan
            </flux:text>
        </div>

        {{-- Director/Accountant without a school yet --}}
        @if ($scope === 'institution' && !$stats['institution'])
            @can('create institution')
                <flux:card class="text-center py-10">
                    <flux:heading size="lg" class="mb-2">Set up your school</flux:heading>
                    <flux:text class="text-zinc-500 mb-4">
                        You don't have an institution yet. Create one to start managing students, parents, fees and
                        attendance.
                    </flux:text>
                    <flux:button href="{{ route('institution.create') }}" variant="primary" wire:navigate>
                        Add an Institution
                    </flux:button>
                </flux:card>
            @else
                {{-- An Accountant is attached to a school by a Director; they
                can't create one themselves. --}}
                <flux:callout variant="warning" icon="information-circle">
                    <flux:callout.heading>Not yet assigned to a school</flux:callout.heading>
                    <flux:callout.text>Ask your Director to link your account to a staff record.</flux:callout.text>
                </flux:callout>
            @endcan

            {{-- ===================== DIRECTOR / ACCOUNTANT ===================== --}}
        @elseif ($scope === 'institution')
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @if (isset($stats['students_count']))
                    <flux:card class="space-y-1">
                        <flux:text class="text-zinc-500">Students</flux:text>
                        <flux:heading size="xl">{{ $stats['students_count'] }}</flux:heading>
                    </flux:card>
                    <flux:card class="space-y-1">
                        <flux:text class="text-zinc-500">Parents</flux:text>
                        <flux:heading size="xl">{{ $stats['parents_count'] }}</flux:heading>
                    </flux:card>
                    <flux:card class="space-y-1">
                        <flux:text class="text-zinc-500">Attendance Today</flux:text>
                        <flux:heading size="xl">{{ $stats['attendance_today_count'] }}</flux:heading>
                    </flux:card>
                @endif
                <flux:card class="space-y-1">
                    <flux:text class="text-zinc-500">Fees Outstanding</flux:text>
                    <flux:heading size="xl">{{ $currency($stats['fees_outstanding']) }}</flux:heading>
                    <flux:text class="text-xs text-red-500">{{ $stats['overdue_fees_count'] }} overdue</flux:text>
                </flux:card>
                @can('view payroll')
                    <flux:card class="space-y-1">
                        <flux:text class="text-zinc-500">Payroll This Month</flux:text>
                        <flux:heading size="xl">{{ $currency($stats['payroll_month_total']) }}</flux:heading>
                        <flux:text class="text-xs text-zinc-500">
                            {{ $currency($stats['payroll_month_paid']) }} paid ·
                            {{ $stats['payroll_pending_count'] }} pending
                        </flux:text>
                    </flux:card>
                @endcan
                @can('view staff')
                    <flux:card class="space-y-1">
                        <flux:text class="text-zinc-500">Staff</flux:text>
                        <flux:heading size="xl">{{ $stats['staff_count'] }}</flux:heading>
                    </flux:card>
                @endcan
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <flux:card class="space-y-3">
                    <flux:heading size="lg">Fee Collection</flux:heading>
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">Collected {{ $currency($stats['fees_collected']) }}</span>
                        <span class="text-zinc-500">Billed {{ $currency($stats['fees_billed']) }}</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-zinc-200 dark:bg-zinc-700">
                        <div class="h-2 rounded-full bg-emerald-500"
                            style="width: {{ $pct($stats['fees_collected'], $stats['fees_billed']) }}%"></div>
                    </div>
                </flux:card>
                {{-- create a flux card with device information --}}
                @isset($stats['devices_count'])
                    <flux:card>
                        <flux:heading size="lg" class="mb-4">Biometric Devices</flux:heading>
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-500">Total Devices</span>
                            <span class="text-zinc-500">{{ $stats['devices_count'] }}</span>
                        </div>
                    </flux:card>
                @endisset
                <flux:card>
                    <flux:heading size="lg" class="mb-4">Recent Fees</flux:heading>
                    <div class="space-y-2">
                        @forelse ($stats['recent_fees'] as $fee)
                            <div class="flex justify-between text-sm">
                                <span>{{ $fee->student?->name }} — {{ $fee->title }}</span>
                                <flux:badge size="sm"
                                    :color="match ($fee->status) {
                                                                        'paid' => 'emerald',
                                                                        'partial' => 'amber',
                                                                        'overdue' => 'red',
                                                                        default => 'zinc',
                                                                    }">
                                    {{ ucfirst($fee->status) }}</flux:badge>
                            </div>
                        @empty
                            <flux:text class="text-zinc-500">No fee records yet.</flux:text>
                        @endforelse
                    </div>
                </flux:card>
            </div>

            <div class="flex flex-wrap gap-3">
                @can('create student')
                    <flux:button href="{{ route('student.create') }}" icon="user-plus" wire:navigate>Add Student</flux:button>
                @endcan
                @can('create feemanagement')
                    <flux:button href="{{ route('feemanagement.create') }}" icon="banknotes" variant="ghost" wire:navigate>Add
                        Fee</flux:button>
                @endcan
                @can('create payroll')
                    <flux:button href="{{ route('staff.payments.create') }}" icon="banknotes" variant="ghost" wire:navigate>Record
                        Staff Payment</flux:button>
                @endcan
                @can('view attendance')
                    <flux:button href="{{ route('attendance.index') }}" icon="clock" variant="ghost" wire:navigate>View
                        Attendance</flux:button>
                @endcan
            </div>

            {{-- ===================== PARENT ===================== --}}
        @elseif ($scope === 'parent')
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <flux:card class="space-y-1">
                    <flux:text class="text-zinc-500">Children</flux:text>
                    <flux:heading size="xl">{{ $stats['children_count'] }}</flux:heading>
                </flux:card>
                <flux:card class="space-y-1">
                    <flux:text class="text-zinc-500">Attendance Today</flux:text>
                    <flux:heading size="xl">{{ $stats['attendance_today_count'] }}</flux:heading>
                </flux:card>
                <flux:card class="space-y-1">
                    <flux:text class="text-zinc-500">Fees Outstanding</flux:text>
                    <flux:heading size="xl">{{ $currency($stats['fees_outstanding']) }}</flux:heading>
                </flux:card>
            </div>

            <flux:card>
                <flux:heading size="lg" class="mb-4">My Children</flux:heading>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @forelse ($stats['children'] as $child)
                        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-3">
                            <div class="font-medium">{{ $child->student?->name }}</div>
                            <div class="text-xs text-zinc-500">{{ $child->institution?->name }}</div>
                            <flux:badge size="sm" class="mt-2" :color="$child->is_active ? 'emerald' : 'zinc'">
                                {{ ucfirst($child->enrollment_status) }}
                            </flux:badge>
                        </div>
                    @empty
                        <flux:text class="text-zinc-500">No children linked to your account yet.</flux:text>
                    @endforelse
                </div>
            </flux:card>

            {{-- ===================== STUDENT ===================== --}}
        @elseif ($scope === 'student')
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <flux:card class="space-y-1">
                    <flux:text class="text-zinc-500">Attendance Today</flux:text>
                    <flux:heading size="xl">{{ $stats['attendance_today_count'] }}</flux:heading>
                </flux:card>
                <flux:card class="space-y-1">
                    <flux:text class="text-zinc-500">Attendance This Month</flux:text>
                    <flux:heading size="xl">{{ $stats['attendance_this_month_count'] }}</flux:heading>
                </flux:card>
                <flux:card class="space-y-1">
                    <flux:text class="text-zinc-500">Fees Outstanding</flux:text>
                    <flux:heading size="xl">{{ $currency($stats['fees_outstanding']) }}</flux:heading>
                </flux:card>
            </div>

            {{-- ===================== TEACHER ===================== --}}
        @elseif ($scope === 'teacher')
            @if (!$stats['institution'])
                <flux:callout variant="warning" icon="information-circle">
                    <flux:callout.heading>Not yet assigned to a school</flux:callout.heading>
                    <flux:callout.text>Ask your Director to add you as a teacher.</flux:callout.text>
                </flux:callout>
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <flux:card class="space-y-1 sm:col-span-1">
                        <flux:text class="text-zinc-500">School</flux:text>
                        <flux:heading size="lg">{{ $stats['institution']->name }}</flux:heading>
                    </flux:card>
                    <flux:card class="space-y-1">
                        <flux:text class="text-zinc-500">Students</flux:text>
                        <flux:heading size="xl">{{ $stats['students_count'] }}</flux:heading>
                    </flux:card>
                    <flux:card class="space-y-1">
                        <flux:text class="text-zinc-500">Attendance Today</flux:text>
                        <flux:heading size="xl">{{ $stats['attendance_today_count'] }}</flux:heading>
                    </flux:card>
                </div>
            @endif
        @else
            <flux:button href="{{ route('institution.create') }}" wire:navigate>Add an Institution to continue...</flux:button>
        @endif

    </div>
</x-layouts::app>
