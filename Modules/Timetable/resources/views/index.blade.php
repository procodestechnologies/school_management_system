@php
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $byDay = $entries->groupBy('day_of_week');
@endphp

<x-layouts::app :title="__(config('timetable.name'))">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 flex flex-row justify-between">
            @can('create timetable')
                <flux:button href="{{ route('timetable.create') }}">Add Timetable Entry</flux:button>
            @endcan
        </div>

        @if ($entries->isEmpty())
            <flux:card class="text-center py-10">
                <flux:text class="text-zinc-500">No timetable entries yet.</flux:text>
            </flux:card>
        @else
            <div class="space-y-6">
                @foreach ($days as $day)
                    @continue($byDay->get($day, collect())->isEmpty())
                    <flux:card>
                        <flux:heading size="lg" class="mb-3">{{ $day }}</flux:heading>
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>Time</flux:table.column>
                                <flux:table.column>Class</flux:table.column>
                                <flux:table.column>Subject</flux:table.column>
                                <flux:table.column>Teacher</flux:table.column>
                                <flux:table.column>Room</flux:table.column>
                                <flux:table.column>Actions</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach ($byDay->get($day) as $entry)
                                    <flux:table.row>
                                        <flux:table.cell>
                                            {{ $entry->start_time?->format('H:i') }} &ndash;
                                            {{ $entry->end_time?->format('H:i') }}
                                        </flux:table.cell>
                                        <flux:table.cell>{{ $entry->schoolClass?->name ?? $entry->class_name }}</flux:table.cell>
                                        <flux:table.cell>{{ $entry->subject }}</flux:table.cell>
                                        <flux:table.cell>{{ $entry->teacher?->name ?? '—' }}</flux:table.cell>
                                        <flux:table.cell>{{ $entry->room ?? '—' }}</flux:table.cell>
                                        <flux:table.cell>
                                            <flux:button href="{{ route('timetable.show', $entry->id) }}"
                                                icon="eye" variant="primary" color="emerald">view</flux:button>
                                            @can('edit timetable')
                                                <flux:button href="{{ route('timetable.edit', $entry->id) }}"
                                                    icon="pencil" variant="primary" color="yellow">edit</flux:button>
                                            @endcan
                                            @can('delete timetable')
                                                <form action="{{ route('timetable.destroy', $entry->id) }}"
                                                    method="POST" class="inline"
                                                    onsubmit="return confirm('Remove this timetable entry?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <flux:button type="submit" icon="trash" variant="primary"
                                                        color="red">delete</flux:button>
                                                </form>
                                            @endcan
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </flux:card>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts::app>
