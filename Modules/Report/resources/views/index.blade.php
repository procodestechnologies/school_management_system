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

            {{-- KPI tiles --}}
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-6">
                <flux:card class="space-y-1">
                    <div class="flex items-center gap-2 text-zinc-500">
                        <flux:icon icon="building-office-2" class="size-4" />
                        <flux:text>Institutions</flux:text>
                    </div>
                    <flux:heading size="xl">{{ $stats['institutions_count'] }}</flux:heading>
                    <flux:text class="text-xs text-zinc-500">
                        {{ $stats['active_institutions_count'] }} active
                        @if (end($stats['institution_growth']))
                            &middot; +{{ end($stats['institution_growth']) }} this month
                        @endif
                    </flux:text>
                </flux:card>

                <flux:card class="space-y-1">
                    <div class="flex items-center gap-2 text-zinc-500">
                        <flux:icon icon="user-group" class="size-4" />
                        <flux:text>Students</flux:text>
                    </div>
                    <flux:heading size="xl">{{ $stats['students_count'] }}</flux:heading>
                    <flux:text class="text-xs text-zinc-500">
                        {{ $stats['active_students_count'] }} active
                        @if (end($stats['student_growth']))
                            &middot; +{{ end($stats['student_growth']) }} this month
                        @endif
                    </flux:text>
                </flux:card>

                <flux:card class="space-y-1">
                    <div class="flex items-center gap-2 text-zinc-500">
                        <flux:icon icon="banknotes" class="size-4" />
                        <flux:text>Fee Collection</flux:text>
                    </div>
                    <flux:heading size="xl">{{ $stats['fee_collection_rate'] }}%</flux:heading>
                    <flux:text class="text-xs text-amber-600 dark:text-amber-500">
                        {{ $currency($stats['fees_outstanding']) }} outstanding
                    </flux:text>
                </flux:card>

                <flux:card class="space-y-1">
                    <div class="flex items-center gap-2 text-zinc-500">
                        <flux:icon icon="clock" class="size-4" />
                        <flux:text>Attendance Today</flux:text>
                    </div>
                    <flux:heading size="xl">{{ $stats['attendance_today_count'] }}</flux:heading>
                    <flux:text class="text-xs text-zinc-500">check-ins across every school</flux:text>
                </flux:card>

                <flux:card class="space-y-1">
                    <div class="flex items-center gap-2 text-zinc-500">
                        <flux:icon icon="clipboard-document-check" class="size-4" />
                        <flux:text>Pending Approvals</flux:text>
                    </div>
                    <flux:heading size="xl"
                        class="{{ $stats['pending_approvals_count'] > 0 ? 'text-amber-600 dark:text-amber-500' : '' }}">
                        {{ $stats['pending_approvals_count'] }}
                    </flux:heading>
                    <flux:text class="text-xs text-zinc-500">institutions awaiting review</flux:text>
                </flux:card>

                <flux:card class="space-y-1">
                    <div class="flex items-center gap-2 text-zinc-500">
                        <flux:icon icon="device-phone-mobile" class="size-4" />
                        <flux:text>Devices Online</flux:text>
                    </div>
                    <flux:heading size="xl">{{ $stats['devices_online_count'] }}/{{ $stats['devices_count'] }}</flux:heading>
                    <flux:text class="text-xs text-zinc-500">biometric devices connected</flux:text>
                </flux:card>
            </div>

            {{-- Trends --}}
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <flux:card>
                    <flux:heading size="lg" class="mb-1">Growth</flux:heading>
                    <flux:text class="mb-4 text-sm text-zinc-500">New institutions and students, last 6 months</flux:text>
                    <div class="mb-3 flex items-center gap-4 text-xs text-zinc-500 dark:text-zinc-400">
                        <span class="flex items-center gap-1.5"><span class="size-2 rounded-full bg-indigo-500"></span> Institutions</span>
                        <span class="flex items-center gap-1.5"><span class="size-2 rounded-full bg-emerald-500"></span> Students</span>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <x-charts.trend-bars :data="$stats['institution_growth']" color="indigo" :height="96" />
                        <x-charts.trend-bars :data="$stats['student_growth']" color="emerald" :height="96" />
                    </div>
                </flux:card>

                <flux:card>
                    <flux:heading size="lg" class="mb-1">Fee Collection Trend</flux:heading>
                    <flux:text class="mb-4 text-sm text-zinc-500">Billed vs. collected, last 6 months</flux:text>
                    <x-charts.grouped-trend-bars :data="$stats['fee_collection_trend']"
                        :series="['billed' => 'Billed', 'collected' => 'Collected']" :height="112"
                        :formatter="fn ($v) => $currency($v)" />
                </flux:card>
            </div>

            {{-- Distributions --}}
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <flux:card>
                    <flux:heading size="lg" class="mb-4">Institutions by Type</flux:heading>
                    <x-charts.distribution-bars :data="collect($stats['institutions_by_type'])
                        ->map(fn ($count, $type) => ['label' => $type, 'value' => $count, 'color' => 'indigo'])
                        ->values()
                        ->all()" />
                </flux:card>

                <flux:card>
                    <flux:heading size="lg" class="mb-4">Fee Status</flux:heading>
                    @php
                        $feeStatusMeta = [
                            'paid' => ['label' => 'Paid', 'color' => 'emerald'],
                            'partial' => ['label' => 'Partial', 'color' => 'amber'],
                            'overdue' => ['label' => 'Overdue', 'color' => 'red'],
                            'pending' => ['label' => 'Pending', 'color' => 'zinc'],
                        ];
                    @endphp
                    <x-charts.distribution-bars :data="collect($feeStatusMeta)
                        ->map(fn ($meta, $key) => ['label' => $meta['label'], 'value' => $stats['fees_by_status'][$key] ?? 0, 'color' => $meta['color']])
                        ->values()
                        ->all()" />
                </flux:card>

                <flux:card>
                    <flux:heading size="lg" class="mb-4">Users by Role</flux:heading>
                    @php
                        $roleColors = ['indigo', 'sky', 'violet', 'amber', 'rose', 'emerald'];
                    @endphp
                    <x-charts.distribution-bars :data="collect($stats['users_by_role'])
                        ->map(fn ($count, $role) => ['label' => $role, 'value' => $count])
                        ->values()
                        ->map(fn ($item, $i) => $item + ['color' => $roleColors[$i % count($roleColors)]])
                        ->all()" />
                </flux:card>
            </div>

            {{-- Needs attention --}}
            @if ($stats['pending_approvals_count'] > 0)
                <flux:card>
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <flux:heading size="lg">Needs Your Attention</flux:heading>
                            <flux:text class="text-sm text-zinc-500">Institutions waiting to be reviewed, oldest first</flux:text>
                        </div>
                        <flux:badge color="amber">{{ $stats['pending_approvals_count'] }} pending</flux:badge>
                    </div>
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Name</flux:table.column>
                            <flux:table.column>Owner</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Submitted</flux:table.column>
                            <flux:table.column>Actions</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($stats['pending_institutions'] as $institution)
                                <flux:table.row>
                                    <flux:table.cell>{{ $institution->name }}</flux:table.cell>
                                    <flux:table.cell>{{ $institution->owner?->name }}</flux:table.cell>
                                    <flux:table.cell>{{ $institution->type }}</flux:table.cell>
                                    <flux:table.cell>{{ $institution->created_at?->diffForHumans() }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:button href="{{ route('institution.show', $institution->id) }}" icon="eye"
                                            variant="primary" color="emerald">review</flux:button>
                                        <form action="{{ route('institution.approve', $institution->id) }}" method="POST" class="inline">
                                            @csrf
                                            <flux:button type="submit" icon="check" variant="primary" color="blue">approve</flux:button>
                                        </form>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </flux:card>
            @endif

            {{-- Recent activity --}}
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <flux:card class="lg:col-span-2">
                    <flux:heading size="lg" class="mb-4">Recent Institutions</flux:heading>
                    <flux:table>
                        <flux:table.columns>
                            <x-sortable-column column="name">Name</x-sortable-column>
                            <flux:table.column>Owner</flux:table.column>
                            <flux:table.column>Status</flux:table.column>
                            <x-sortable-column column="created_at">Created</x-sortable-column>
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

                <flux:card>
                    <div class="mb-4 flex items-center justify-between">
                        <flux:heading size="lg">Recent Messages</flux:heading>
                        <flux:button href="{{ route('messages.index') }}" size="sm" variant="ghost">View all</flux:button>
                    </div>
                    <div class="space-y-3">
                        @forelse ($stats['recent_contact_messages'] as $message)
                            <a href="{{ route('messages.show', $message) }}"
                                class="block rounded-lg border border-zinc-100 p-3 text-sm hover:bg-zinc-50 dark:border-white/5 dark:hover:bg-white/5">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ $message->name }}</span>
                                    <span class="text-xs text-zinc-400">{{ $message->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-1 truncate text-zinc-500 dark:text-zinc-400">{{ $message->message }}</p>
                            </a>
                        @empty
                            <flux:text class="text-zinc-500">No messages yet.</flux:text>
                        @endforelse
                    </div>
                </flux:card>
            </div>

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
                        <x-sortable-column column="title">Title</x-sortable-column>
                        <x-sortable-column column="amount">Amount</x-sortable-column>
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
                        <x-sortable-column column="admission_number">Admission No.</x-sortable-column>
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
                        <x-sortable-column column="title">Title</x-sortable-column>
                        <x-sortable-column column="amount">Amount</x-sortable-column>
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
                        <x-sortable-column column="title">Title</x-sortable-column>
                        <x-sortable-column column="amount">Amount</x-sortable-column>
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
