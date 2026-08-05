<x-layouts::app :title="__(config('attendance.name'))">
    <div class="p-4">
        <flux:card>
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <flux:heading size="lg">Attendance</flux:heading>
                    @if ($logs)
                        <flux:text class="text-zinc-500">
                            Showing {{ $logs->count() }} of {{ $logs->total() }} record(s)
                        </flux:text>
                    @endif
                </div>

                @if ($logs)
                    <form method="GET" class="flex flex-wrap items-center gap-2">
                        <flux:input type="search" name="search" placeholder="Search attendance…"
                            value="{{ request('search') }}" class="w-56" />
                        <flux:input type="date" name="from" value="{{ request('from') }}" />
                        <flux:input type="date" name="to" value="{{ request('to') }}" />
                        <flux:button type="submit" icon="magnifying-glass">Filter</flux:button>
                    </form>
                @endif
            </div>

            @if (! $institution)
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
                            <flux:table.column>Student</flux:table.column>
                            <flux:table.column>Admission No.</flux:table.column>
                            <flux:table.column>Device</flux:table.column>
                            <x-sortable-column column="status">Status</x-sortable-column>
                            <flux:table.column>Verify Mode</flux:table.column>
                            <x-sortable-column column="occurred_at">Date/Time</x-sortable-column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse ($logs as $log)
                                <flux:table.row>
                                    <flux:table.cell>{{ $log['student_name'] }}</flux:table.cell>
                                    <flux:table.cell>{{ $log['admission_number'] }}</flux:table.cell>
                                    <flux:table.cell>{{ $log['device_name'] }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge size="sm" color="{{ $log['status_color'] }}">
                                            {{ $log['status_label'] }}
                                        </flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell>{{ $log['verify_mode_label'] }}</flux:table.cell>
                                    <flux:table.cell>{{ $log['occurred_at'] }}</flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="6" class="text-center text-zinc-500">
                                        No attendance records found.
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </div>

                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            @endif
        </flux:card>
    </div>
</x-layouts::app>
