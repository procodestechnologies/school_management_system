<x-layouts::app :title="__(config('timetable.name'))">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            @if ($isTeacher)
                <flux:heading size="lg">My Timetable</flux:heading>
            @elseif ($showPicker)
                <form method="GET" action="{{ route('timetable.index') }}" class="flex items-center gap-2">
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
            @elseif ($isStudent)
                <flux:heading size="lg">My Timetable</flux:heading>
            @elseif ($isParent)
                <flux:heading size="lg">My Child's Timetable</flux:heading>
            @endif

            <div class="flex gap-2">
                @can('create timetable')
                    <flux:button href="{{ route('timetable.import') }}" icon="arrow-up-tray" variant="ghost">
                        Import Timetable</flux:button>
                    <flux:button href="{{ route('timetable.create') }}">Add Timetable Entry</flux:button>
                @endcan
            </div>
        </div>

        @if ($isTeacher)
            @if ($periods->isEmpty())
                <flux:card class="text-center py-10">
                    <flux:text class="text-zinc-500">
                        You have no timetable entries assigned yet. Contact your school administrator.
                    </flux:text>
                </flux:card>
            @else
                <flux:card>
                    <div class="mb-4">
                        <flux:heading size="lg">Weekly Timetable</flux:heading>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-sm">
                            <thead>
                                <tr>
                                    <th class="border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-left">
                                        Day
                                    </th>
                                    @foreach ($periods as $period)
                                        <th class="border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-center whitespace-nowrap">
                                            {{ $period }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($days as $day)
                                    @continue(!isset($grid[$day]))
                                    <tr>
                                        <td class="border border-zinc-200 dark:border-zinc-700 px-3 py-2 font-medium bg-zinc-50 dark:bg-zinc-800">
                                            {{ $day }}
                                        </td>
                                        @foreach ($periods as $period)
                                            @php $entry = $grid[$day][$period] ?? null; @endphp
                                            <td class="border border-zinc-200 dark:border-zinc-700 px-3 py-2 text-center">
                                                @if ($entry)
                                                    <a href="{{ route('timetable.show', $entry->id) }}"
                                                        class="block hover:underline">
                                                        <span class="font-medium">{{ $entry->subject }}</span>
                                                        <span class="block text-xs text-zinc-500">
                                                            {{ $entry->schoolClass?->name ?? $entry->class_name }}
                                                        </span>
                                                        @if ($entry->room)
                                                            <span class="block text-xs text-zinc-500">
                                                                {{ $entry->room }}
                                                            </span>
                                                        @endif
                                                    </a>
                                                @else
                                                    <span class="text-zinc-300 dark:text-zinc-600">&mdash;</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </flux:card>
            @endif
        @elseif (($isStudent || $isParent) && $classes->isEmpty())
            <flux:card class="text-center py-10">
                <flux:text class="text-zinc-500">
                    @if ($isStudent)
                        You are not assigned to a class yet. Contact your school administrator.
                    @else
                        No child is assigned to a class yet. Contact your school administrator.
                    @endif
                </flux:text>
            </flux:card>
        @elseif (! $isStudent && ! $isParent && $classes->isEmpty())
            <flux:card class="text-center py-10">
                <flux:text class="text-zinc-500">No classes available yet.</flux:text>
            </flux:card>
        @elseif (!$selectedClass)
            <flux:card class="text-center py-10">
                <flux:text class="text-zinc-500">Select a class above to view its timetable.</flux:text>
            </flux:card>
        @else
            <flux:card>
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="lg">{{ $selectedClass->name }} Timetable</flux:heading>
                    @if ($selectedClass->classTeacher)
                        <flux:text class="text-zinc-500">Class Teacher: {{ $selectedClass->classTeacher->name }}
                        </flux:text>
                    @endif
                </div>

                @if ($periods->isEmpty())
                    <flux:text class="text-zinc-500">
                        No timetable entries for this class yet.
                        @can('create timetable')
                            Use "Add Timetable Entry" or "Import Timetable" above to get started.
                        @endcan
                    </flux:text>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-sm">
                            <thead>
                                <tr>
                                    <th class="border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-left">
                                        Day
                                    </th>
                                    @foreach ($periods as $period)
                                        <th class="border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-center whitespace-nowrap">
                                            {{ $period }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($days as $day)
                                    @continue(!isset($grid[$day]))
                                    <tr>
                                        <td class="border border-zinc-200 dark:border-zinc-700 px-3 py-2 font-medium bg-zinc-50 dark:bg-zinc-800">
                                            {{ $day }}
                                        </td>
                                        @foreach ($periods as $period)
                                            @php $entry = $grid[$day][$period] ?? null; @endphp
                                            <td class="border border-zinc-200 dark:border-zinc-700 px-3 py-2 text-center">
                                                @if ($entry)
                                                    <a href="{{ route('timetable.show', $entry->id) }}"
                                                        class="block hover:underline">
                                                        <span class="font-medium">{{ $entry->subject }}</span>
                                                        @if ($entry->teacher)
                                                            <span class="block text-xs text-zinc-500">
                                                                {{ $entry->teacher->name }}
                                                            </span>
                                                        @endif
                                                        @if ($entry->room)
                                                            <span class="block text-xs text-zinc-500">
                                                                {{ $entry->room }}
                                                            </span>
                                                        @endif
                                                    </a>
                                                @else
                                                    <span class="text-zinc-300 dark:text-zinc-600">&mdash;</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </flux:card>
        @endif
    </div>
</x-layouts::app>
