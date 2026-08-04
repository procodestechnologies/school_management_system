<x-layouts::app :title="__(config('lesson.name'))">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('lesson.index') }}" class="flex flex-wrap items-center gap-2">
                <flux:select name="class_id" onchange="this.form.submit()">
                    <flux:select.option value="">Select a class&hellip;</flux:select.option>
                    @foreach ($classes as $schoolClass)
                        <flux:select.option value="{{ $schoolClass->id }}"
                            :selected="$selectedClass?->id === $schoolClass->id">
                            {{ $schoolClass->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input type="date" name="date" value="{{ $date->format('Y-m-d') }}"
                    onchange="this.form.submit()" />

                <div class="flex gap-1">
                    <flux:button size="sm" variant="ghost" icon="chevron-left"
                        href="{{ route('lesson.index', ['class_id' => $selectedClass?->id, 'date' => $date->copy()->subDay()->format('Y-m-d')]) }}" />
                    <flux:button size="sm" variant="ghost"
                        href="{{ route('lesson.index', ['class_id' => $selectedClass?->id, 'date' => now()->format('Y-m-d')]) }}">
                        Today</flux:button>
                    <flux:button size="sm" variant="ghost" icon="chevron-right"
                        href="{{ route('lesson.index', ['class_id' => $selectedClass?->id, 'date' => $date->copy()->addDay()->format('Y-m-d')]) }}" />
                </div>
            </form>

            <flux:button href="{{ route('lesson.reports.index', ['class_id' => $selectedClass?->id]) }}"
                icon="document-chart-bar" variant="ghost">Reports</flux:button>
        </div>

        @if ($classes->isEmpty())
            <flux:card class="text-center py-10">
                <flux:text class="text-zinc-500">No classes available yet.</flux:text>
            </flux:card>
        @elseif (!$selectedClass)
            <flux:card class="text-center py-10">
                <flux:text class="text-zinc-500">Select a class above to view its lessons.</flux:text>
            </flux:card>
        @else
            <flux:card class="mb-6">
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="lg">
                        {{ $selectedClass->name }} &mdash; {{ $date->format('l, d M Y') }}
                    </flux:heading>
                </div>

                @if ($rows->isEmpty())
                    <flux:text class="text-zinc-500">
                        No timetable periods for {{ $selectedClass->name }} on {{ $date->format('l') }}.
                    </flux:text>
                @else
                    <form action="{{ route('lesson.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                        <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">

                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse text-sm">
                                <thead>
                                    <tr>
                                        <th class="border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-left">
                                            Time</th>
                                        <th class="border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-left">
                                            Subject</th>
                                        <th class="border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-left">
                                            Teacher</th>
                                        <th class="border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-left">
                                            Status</th>
                                        <th class="border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-left">
                                            Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rows as $i => $row)
                                        @php [$entry, $lesson] = [$row['entry'], $row['lesson']]; @endphp
                                        <tr>
                                            <td class="border border-zinc-200 dark:border-zinc-700 px-3 py-2 whitespace-nowrap">
                                                {{ $entry->start_time?->format('H:i') }}&ndash;{{ $entry->end_time?->format('H:i') }}
                                            </td>
                                            <td class="border border-zinc-200 dark:border-zinc-700 px-3 py-2 font-medium">
                                                {{ $entry->subject }}
                                            </td>
                                            <td class="border border-zinc-200 dark:border-zinc-700 px-3 py-2">
                                                {{ $entry->teacher?->name ?? '—' }}
                                            </td>
                                            <td class="border border-zinc-200 dark:border-zinc-700 px-3 py-2">
                                                <input type="hidden" name="statuses[{{ $i }}][timetable_entry_id]"
                                                    value="{{ $entry->id }}">
                                                @can('edit lesson')
                                                    <flux:select name="statuses[{{ $i }}][status]" size="sm">
                                                        <flux:select.option value="attended"
                                                            :selected="($lesson->status ?? 'not_attended') === 'attended'">
                                                            Attended</flux:select.option>
                                                        <flux:select.option value="not_attended"
                                                            :selected="($lesson->status ?? 'not_attended') === 'not_attended'">
                                                            Not Attended</flux:select.option>
                                                        <flux:select.option value="recovered"
                                                            :selected="($lesson->status ?? 'not_attended') === 'recovered'">
                                                            Recovered</flux:select.option>
                                                    </flux:select>
                                                @else
                                                    <flux:badge :color="$lesson ? $lesson->statusColor() : 'zinc'">
                                                        {{ $lesson ? $lesson->statusLabel() : 'Not marked' }}
                                                    </flux:badge>
                                                @endcan
                                            </td>
                                            <td class="border border-zinc-200 dark:border-zinc-700 px-3 py-2">
                                                @can('edit lesson')
                                                    <flux:input name="statuses[{{ $i }}][remarks]"
                                                        value="{{ $lesson->remarks ?? '' }}" size="sm"
                                                        placeholder="Optional" />
                                                @else
                                                    {{ $lesson->remarks ?? '—' }}
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @can('edit lesson')
                            <div class="mt-4 flex justify-end">
                                <flux:button type="submit" variant="primary">Save Attendance</flux:button>
                            </div>
                        @endcan
                    </form>
                @endif
            </flux:card>

            @if ($recent->isNotEmpty())
                <flux:card>
                    <flux:heading size="lg" class="mb-4">Recent Activity</flux:heading>
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Date</flux:table.column>
                            <flux:table.column>Subject</flux:table.column>
                            <flux:table.column>Status</flux:table.column>
                            <flux:table.column>Actions</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($recent as $item)
                                <flux:table.row>
                                    <flux:table.cell>{{ $item->lesson_date->format('d M Y') }}</flux:table.cell>
                                    <flux:table.cell>{{ $item->timetableEntry?->subject }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge :color="$item->statusColor()">
                                            {{ $item->statusLabel() }}
                                        </flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:button href="{{ route('lesson.show', $item->id) }}" size="sm"
                                            icon="eye" variant="ghost">view</flux:button>
                                        @can('edit lesson')
                                            <flux:button href="{{ route('lesson.edit', $item->id) }}" size="sm"
                                                icon="pencil" variant="ghost">edit</flux:button>
                                        @endcan
                                        @can('delete lesson')
                                            <form action="{{ route('lesson.destroy', $item->id) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Remove this lesson record?');">
                                                @csrf
                                                @method('DELETE')
                                                <flux:button type="submit" size="sm" icon="trash"
                                                    variant="ghost">delete</flux:button>
                                            </form>
                                        @endcan
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </flux:card>
            @endif
        @endif
    </div>
</x-layouts::app>
