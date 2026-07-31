@php
    $currency = fn($v) => number_format((float) $v, 2);
    $pct = fn($num, $den) => $den > 0 ? min(100, round(($num / $den) * 100)) : 0;
@endphp

<x-layouts::app :title="__('Reports & Analytics')">
    <div class="p-4 space-y-6">

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">Reports &amp; Analytics</flux:heading>
                <flux:text class="text-zinc-500">
                    Showing data for your
                    <span class="font-medium">{{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'account') }}</span>
                    role.
                </flux:text>
            </div>

            @can('export report')
                <flux:button href="{{ route('report.export') }}" icon="arrow-down-tray" variant="primary">
                    Export Fees CSV
                </flux:button>
            @endcan
        </div>

        {{-- ===================== ADMIN: SYSTEM-WIDE ===================== --}}
        @if ($stats['scope'] === 'system')

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <flux:card class="space-y-1">
                    <flux:text class="text-zinc-500">Institutions</flux:text>
                    <flux:heading size="xl">{{ $stats['institutions_count'] }}</flux:heading>
                    <flux:text class="text-xs text-zinc-500">{{ $stats['active_institutions_count'] }} active</flux:text>
                </flux:card>
                <flux:card class="space-y-1">
                    <flux:text class="text-zinc-500">Students</flux:text>
                    <flux:heading size="xl">{{ $stats['students_count'] }}</flux:heading>
                </flux:card>
                <flux:card class="space-y-1">
                    <flux:text class="text-zinc-500">Devices</flux:text>
                    <flux:heading size="xl">{{ $stats['devices_count'] }}</flux:heading>
                </flux:card>
                <flux:card class="space-y-1">
                    <flux:text class="text-zinc-500">Attendance Today</flux:text>
                    <flux:heading size="xl">{{ $stats['attendance_today_count'] }}</flux:heading>
                </flux:card>
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
                    <flux:text class="text-sm text-amber-600">
                        Outstanding: {{ $currency($stats['fees_outstanding']) }}
                    </flux:text>
                </flux:card>

                <flux:card class="space-y-3">
                    <flux:heading size="lg">Users by Role</flux:heading>
                    @php $maxRole = $stats['users_by_role']->max() ?: 1; @endphp
                    <div class="space-y-2">
                        @forelse ($stats['users_by_role'] as $role => $count)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span>{{ $role }}</span>
                                    <span class="text-zinc-500">{{ $count }}</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-zinc-200 dark:bg-zinc-700">
                                    <div class="h-2 rounded-full bg-indigo-500"
                                        style="width: {{ $pct($count, $maxRole) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <flux:text class="text-zinc-500">No users yet.</flux:text>
                        @endforelse
                    </div>
                </flux:card>
            </div>

            <flux:card>
                <flux:heading size="lg" class="mb-4">Recent Institutions</flux:heading>
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Name</flux:table.column>
                        <flux:table.column>Owner</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column>Created</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($stats['recent_institutions'] as $institution)
                            <flux:table.row>
                                <flux:table.cell>{{ $institution->name }}</flux:table.cell>
                                <flux:table.cell>{{ $institution->owner?->name }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge :color="$institution->is_active ? 'emerald' : 'zinc'">
                                        {{ ucfirst($institution->status ?? 'unknown') }}
                                    </flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>{{ $institution->created_at?->format('d M Y') }}</flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="4" class="text-center text-zinc-500">No institutions yet.
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </flux:card>

            {{-- ===================== DIRECTOR / ACCOUNTANT: INSTITUTION-SCOPED ===================== --}}
        @elseif ($stats['scope'] === 'institution')

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
                        <flux:text class="text-zinc-500">Devices</flux:text>
                        <flux:heading size="xl">{{ $stats['devices_count'] }}</flux:heading>
                    </flux:card>
                    <flux:card class="space-y-1">
                        <flux:text class="text-zinc-500">Attendance Today</flux:text>
                        <flux:heading size="xl">{{ $stats['attendance_today_count'] }}</flux:heading>
                    </flux:card>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <flux:card class="space-y-3">
                    <flux:heading size="lg">Fee Collection — {{ $stats['institution']?->name }}</flux:heading>
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">Collected {{ $currency($stats['fees_collected']) }}</span>
                        <span class="text-zinc-500">Billed {{ $currency($stats['fees_billed']) }}</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-zinc-200 dark:bg-zinc-700">
                        <div class="h-2 rounded-full bg-emerald-500"
                            style="width: {{ $pct($stats['fees_collected'], $stats['fees_billed']) }}%"></div>
                    </div>
                    <div class="flex justify-between text-sm">
                        <flux:text class="text-amber-600">Outstanding: {{ $currency($stats['fees_outstanding']) }}
                        </flux:text>
                        <flux:text class="text-red-600">{{ $stats['overdue_fees_count'] }} overdue</flux:text>
                    </div>
                </flux:card>

                @if (isset($stats['enrollment_by_status']))
                    <flux:card class="space-y-3">
                        <flux:heading size="lg">Enrollment Status</flux:heading>
                        @php $maxEnroll = $stats['enrollment_by_status']->max() ?: 1; @endphp
                        <div class="space-y-2">
                            @forelse ($stats['enrollment_by_status'] as $status => $count)
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span>{{ ucfirst($status) }}</span>
                                        <span class="text-zinc-500">{{ $count }}</span>
                                    </div>
                                    <div class="h-2 w-full rounded-full bg-zinc-200 dark:bg-zinc-700">
                                        <div class="h-2 rounded-full bg-sky-500"
                                            style="width: {{ $pct($count, $maxEnroll) }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <flux:text class="text-zinc-500">No students yet.</flux:text>
                            @endforelse
                        </div>
                    </flux:card>
                @endif
            </div>

            <flux:card>
                <flux:heading size="lg" class="mb-4">Recent Fees</flux:heading>
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Student</flux:table.column>
                        <flux:table.column>Title</flux:table.column>
                        <flux:table.column>Amount</flux:table.column>
                        <flux:table.column>Balance</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($stats['recent_fees'] as $fee)
                            <flux:table.row>
                                <flux:table.cell>{{ $fee->student?->name }}</flux:table.cell>
                                <flux:table.cell>{{ $fee->title }}</flux:table.cell>
                                <flux:table.cell>{{ $currency($fee->amount) }}</flux:table.cell>
                                <flux:table.cell>{{ $currency($fee->balance) }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge :color="match ($fee->status) {
                                        'paid' => 'emerald',
                                        'partial' => 'amber',
                                        'overdue' => 'red',
                                        default => 'zinc',
                                    }">{{ ucfirst($fee->status) }}</flux:badge>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="5" class="text-center text-zinc-500">No fee records yet.
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </flux:card>

            {{-- ===================== PARENT ===================== --}}
        @elseif ($stats['scope'] === 'parent')

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
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Name</flux:table.column>
                        <flux:table.column>Admission No.</flux:table.column>
                        <flux:table.column>Institution</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($stats['children'] as $child)
                            <flux:table.row>
                                <flux:table.cell>{{ $child->student?->name }}</flux:table.cell>
                                <flux:table.cell>{{ $child->admission_number }}</flux:table.cell>
                                <flux:table.cell>{{ $child->institution?->name }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge :color="$child->is_active ? 'emerald' : 'zinc'">
                                        {{ ucfirst($child->enrollment_status) }}
                                    </flux:badge>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="4" class="text-center text-zinc-500">No children linked to
                                    your account.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </flux:card>

            <flux:card>
                <flux:heading size="lg" class="mb-4">Recent Fees</flux:heading>
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Student</flux:table.column>
                        <flux:table.column>Title</flux:table.column>
                        <flux:table.column>Amount</flux:table.column>
                        <flux:table.column>Balance</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($stats['recent_fees'] as $fee)
                            <flux:table.row>
                                <flux:table.cell>{{ $fee->student?->name }}</flux:table.cell>
                                <flux:table.cell>{{ $fee->title }}</flux:table.cell>
                                <flux:table.cell>{{ $currency($fee->amount) }}</flux:table.cell>
                                <flux:table.cell>{{ $currency($fee->balance) }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge :color="match ($fee->status) {
                                        'paid' => 'emerald',
                                        'partial' => 'amber',
                                        'overdue' => 'red',
                                        default => 'zinc',
                                    }">{{ ucfirst($fee->status) }}</flux:badge>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="5" class="text-center text-zinc-500">No fee records yet.
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </flux:card>

            {{-- ===================== STUDENT ===================== --}}
        @elseif ($stats['scope'] === 'student')

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

            <flux:card>
                <flux:heading size="lg" class="mb-4">My Fees</flux:heading>
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Title</flux:table.column>
                        <flux:table.column>Amount</flux:table.column>
                        <flux:table.column>Balance</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($stats['recent_fees'] as $fee)
                            <flux:table.row>
                                <flux:table.cell>{{ $fee->title }}</flux:table.cell>
                                <flux:table.cell>{{ $currency($fee->amount) }}</flux:table.cell>
                                <flux:table.cell>{{ $currency($fee->balance) }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge :color="match ($fee->status) {
                                        'paid' => 'emerald',
                                        'partial' => 'amber',
                                        'overdue' => 'red',
                                        default => 'zinc',
                                    }">{{ ucfirst($fee->status) }}</flux:badge>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="4" class="text-center text-zinc-500">No fee records yet.
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </flux:card>

            {{-- ===================== TEACHER ===================== --}}
        @elseif ($stats['scope'] === 'teacher')

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
            <flux:callout variant="warning" icon="information-circle">
                <flux:callout.heading>No analytics available</flux:callout.heading>
                <flux:callout.text>Your role does not have a configured analytics view yet.</flux:callout.text>
            </flux:callout>
        @endif

    </div>
</x-layouts::app>
