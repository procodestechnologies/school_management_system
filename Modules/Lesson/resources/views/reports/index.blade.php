<x-layouts::app :title="__('Lesson Reports')">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('lesson.reports.index') }}" class="flex items-center gap-2">
                <flux:select name="class_id" onchange="this.form.submit()">
                    <flux:select.option value="">Select a class&hellip;</flux:select.option>
                    @foreach ($classes as $schoolClass)
                        <flux:select.option value="{{ $schoolClass->id }}"
                            :selected="$selectedClass?->id === $schoolClass->id">
                            {{ $schoolClass->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </form>

            <flux:button href="{{ route('lesson.index', ['class_id' => $selectedClass?->id]) }}" icon="clock"
                variant="ghost">Mark Attendance</flux:button>
        </div>

        @if ($classes->isEmpty())
            <flux:card class="text-center py-10">
                <flux:text class="text-zinc-500">No classes available yet.</flux:text>
            </flux:card>
        @elseif (! $selectedClass)
            <flux:card class="text-center py-10">
                <flux:text class="text-zinc-500">Select a class above to view its lesson attendance reports.</flux:text>
            </flux:card>
        @else
            @can('export lesson')
                <flux:card class="mb-6">
                    <flux:heading size="lg" class="mb-4">Generate Report</flux:heading>
                    <form action="{{ route('lesson.reports.generate') }}" method="POST"
                        class="flex flex-wrap items-end gap-3">
                        @csrf
                        <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">

                        <div>
                            <flux:select name="type" label="Type">
                                <flux:select.option value="daily">Daily</flux:select.option>
                                <flux:select.option value="weekly">Weekly</flux:select.option>
                            </flux:select>
                        </div>

                        <div>
                            <flux:input type="date" name="date" label="Date (or any day in the target week)"
                                value="{{ now()->format('Y-m-d') }}" />
                        </div>

                        <flux:button type="submit" variant="primary">Generate</flux:button>
                    </form>
                </flux:card>
            @endcan

            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ $selectedClass->name }} &mdash; Reports</flux:heading>

                @if ($reports->isEmpty())
                    <flux:text class="text-zinc-500">No reports generated yet for this class.</flux:text>
                @else
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Period</flux:table.column>
                            <flux:table.column>Total</flux:table.column>
                            <flux:table.column>Attended</flux:table.column>
                            <flux:table.column>Not Attended</flux:table.column>
                            <flux:table.column>Recovered</flux:table.column>
                            <flux:table.column>Actions</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($reports as $report)
                                <flux:table.row>
                                    <flux:table.cell>
                                        <flux:badge :color="$report->isWeekly() ? 'indigo' : 'zinc'">
                                            {{ ucfirst($report->type) }}
                                        </flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        {{ $report->period_start->format('d M Y') }}
                                        @if ($report->isWeekly())
                                            &ndash; {{ $report->period_end->format('d M Y') }}
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell>{{ $report->total_lessons }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge size="sm" color="emerald">{{ $report->attended_count }}</flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge size="sm" color="red">{{ $report->not_attended_count }}</flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge size="sm" color="amber">{{ $report->recovered_count }}</flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:button href="{{ route('lesson.reports.show', $report->id) }}" size="sm"
                                            icon="eye" variant="ghost">view</flux:button>
                                        <flux:button href="{{ route('lesson.reports.download', $report->id) }}"
                                            size="sm" icon="arrow-down-tray" variant="ghost">pdf</flux:button>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                @endif
            </flux:card>
        @endif
    </div>
</x-layouts::app>
