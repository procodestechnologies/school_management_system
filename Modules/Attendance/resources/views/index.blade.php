@php
    // Attendance is scoped to an institution. Resolve it from the
    // authenticated user: institution owners/staff via institution(), falling
    // back to a student's own institution via studentInstitution().
$attendanceInstitutionId = auth()->user()?->institution()->value('id') ?? auth()->user()?->studentInstitution?->id;
@endphp

<x-layouts::app :title="__(config('attendance.name'))">
    <div class="p-4">
        <flux:card x-data="attendanceTable()" x-cloak>
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <flux:heading size="lg">Attendance</flux:heading>
                    <flux:text class="text-zinc-500">
                        <template x-if="!loading">
                            <span>
                                Showing <span x-text="sorted.length"></span> of <span x-text="meta.total"></span>
                                record(s)
                            </span>
                        </template>
                        <template x-if="loading">
                            <span>Loading attendance records…</span>
                        </template>
                    </flux:text>
                </div>

                <div class="flex items-center gap-2">
                    <flux:input icon="magnifying-glass" placeholder="Search attendance…" x-model="search" clearable
                        class="w-64" />
                    <flux:button icon="arrow-path" variant="ghost" x-on:click="fetchRecords()"
                        x-bind:disabled="loading">
                        Refresh
                    </flux:button>
                </div>
            </div>

            <div x-show="error" x-cloak class="mb-4">
                <flux:callout variant="danger" icon="exclamation-triangle">
                    <flux:callout.heading>Could not load attendance</flux:callout.heading>
                    <flux:callout.text x-text="error"></flux:callout.text>
                </flux:callout>
            </div>

            @if (!$attendanceInstitutionId)
                <flux:callout variant="warning" icon="information-circle">
                    <flux:callout.heading>No institution found</flux:callout.heading>
                    <flux:callout.text>
                        Attendance records could not be loaded because no institution is linked to your account.
                    </flux:callout.text>
                </flux:callout>
            @else
                <div class="overflow-x-auto">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>
                                <button type="button" class="group/sortable flex items-center gap-1"
                                    x-on:click="sortBy('student')">
                                    <span>Student</span>
                                    <flux:icon.chevron-up variant="micro"
                                        x-show="sortKey === 'student' && sortDir === 'asc'" x-cloak />
                                    <flux:icon.chevron-down variant="micro"
                                        x-show="sortKey !== 'student' || sortDir === 'desc'"
                                        class="opacity-0 group-hover/sortable:opacity-100"
                                        x-bind:class="sortKey === 'student' && 'opacity-100'" x-cloak />
                                </button>
                            </flux:table.column>
                            <flux:table.column>
                                <button type="button" class="group/sortable flex items-center gap-1"
                                    x-on:click="sortBy('admission_number')">
                                    <span>Admission No.</span>
                                    <flux:icon.chevron-up variant="micro"
                                        x-show="sortKey === 'admission_number' && sortDir === 'asc'" x-cloak />
                                    <flux:icon.chevron-down variant="micro"
                                        x-show="sortKey !== 'admission_number' || sortDir === 'desc'"
                                        class="opacity-0 group-hover/sortable:opacity-100"
                                        x-bind:class="sortKey === 'admission_number' && 'opacity-100'" x-cloak />
                                </button>
                            </flux:table.column>
                            <flux:table.column>
                                <button type="button" class="group/sortable flex items-center gap-1"
                                    x-on:click="sortBy('device')">
                                    <span>Device</span>
                                    <flux:icon.chevron-up variant="micro"
                                        x-show="sortKey === 'device' && sortDir === 'asc'" x-cloak />
                                    <flux:icon.chevron-down variant="micro"
                                        x-show="sortKey !== 'device' || sortDir === 'desc'"
                                        class="opacity-0 group-hover/sortable:opacity-100"
                                        x-bind:class="sortKey === 'device' && 'opacity-100'" x-cloak />
                                </button>
                            </flux:table.column>
                            <flux:table.column>
                                <button type="button" class="group/sortable flex items-center gap-1"
                                    x-on:click="sortBy('status')">
                                    <span>Status</span>
                                    <flux:icon.chevron-up variant="micro"
                                        x-show="sortKey === 'status' && sortDir === 'asc'" x-cloak />
                                    <flux:icon.chevron-down variant="micro"
                                        x-show="sortKey !== 'status' || sortDir === 'desc'"
                                        class="opacity-0 group-hover/sortable:opacity-100"
                                        x-bind:class="sortKey === 'status' && 'opacity-100'" x-cloak />
                                </button>
                            </flux:table.column>
                            <flux:table.column>
                                <button type="button" class="group/sortable flex items-center gap-1"
                                    x-on:click="sortBy('verify_mode')">
                                    <span>Verify Mode</span>
                                    <flux:icon.chevron-up variant="micro"
                                        x-show="sortKey === 'verify_mode' && sortDir === 'asc'" x-cloak />
                                    <flux:icon.chevron-down variant="micro"
                                        x-show="sortKey !== 'verify_mode' || sortDir === 'desc'"
                                        class="opacity-0 group-hover/sortable:opacity-100"
                                        x-bind:class="sortKey === 'verify_mode' && 'opacity-100'" x-cloak />
                                </button>
                            </flux:table.column>
                            <flux:table.column>
                                <button type="button" class="group/sortable flex items-center gap-1"
                                    x-on:click="sortBy('occurred_at')">
                                    <span>Date/Time</span>
                                    <flux:icon.chevron-up variant="micro"
                                        x-show="sortKey === 'occurred_at' && sortDir === 'asc'" x-cloak />
                                    <flux:icon.chevron-down variant="micro"
                                        x-show="sortKey !== 'occurred_at' || sortDir === 'desc'"
                                        class="opacity-0 group-hover/sortable:opacity-100"
                                        x-bind:class="sortKey === 'occurred_at' && 'opacity-100'" x-cloak />
                                </button>
                            </flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            <template x-if="loading">
                                <flux:table.row>
                                    <flux:table.cell colspan="6" class="text-center text-zinc-500">
                                        Loading attendance…
                                    </flux:table.cell>
                                </flux:table.row>
                            </template>

                            <template x-if="!loading && !error && sorted.length === 0">
                                <flux:table.row>
                                    <flux:table.cell colspan="6" class="text-center text-zinc-500">
                                        No attendance records found.
                                    </flux:table.cell>
                                </flux:table.row>
                            </template>

                            <template x-for="record in sorted" :key="record.id">
                                <flux:table.row>
                                    <flux:table.cell>
                                        <span
                                            x-text="record.student?.name ?? ('Unknown (PIN ' + record.pin + ')')"></span>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <span x-text="record.student?.admission_number ?? '—'"></span>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <span
                                            x-text="record.device?.name ?? record.device?.serial_number ?? '—'"></span>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <template x-if="record.status === 0">
                                            <flux:badge size="sm" color="emerald">Check In</flux:badge>
                                        </template>
                                        <template x-if="record.status === 1">
                                            <flux:badge size="sm" color="red">Check Out</flux:badge>
                                        </template>
                                        <template x-if="record.status === 2">
                                            <flux:badge size="sm" color="amber">Break Out</flux:badge>
                                        </template>
                                        <template x-if="record.status === 3">
                                            <flux:badge size="sm" color="sky">Break In</flux:badge>
                                        </template>
                                        <template x-if="record.status === 4">
                                            <flux:badge size="sm" color="indigo">Overtime In</flux:badge>
                                        </template>
                                        <template x-if="record.status === 5">
                                            <flux:badge size="sm" color="zinc">Overtime Out</flux:badge>
                                        </template>
                                        <template x-if="![0, 1, 2, 3, 4, 5].includes(record.status)">
                                            <flux:badge size="sm" color="zinc">
                                                <span x-text="'Unknown (' + record.status + ')'"></span>
                                            </flux:badge>
                                        </template>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <span x-text="verifyModeLabel(record.verify_mode)"></span>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <span x-text="formatDateTime(record.occurred_at)"></span>
                                    </flux:table.cell>
                                </flux:table.row>
                            </template>
                        </flux:table.rows>
                    </flux:table>
                </div>
            @endif
        </flux:card>
    </div>
    <x-attendance-script :attendanceInstitutionId="$attendanceInstitutionId" />

</x-layouts::app>
