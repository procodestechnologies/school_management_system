<x-layouts::app :title="__('Edit Timetable Entry')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">Edit Timetable Entry</h4>
                <small class="text-sm text-gray-500">{{ $entry->schoolClass?->name ?? $entry->class_name }}
                    &mdash; {{ $entry->subject }}</small>
            </div>

            @if ($errors->any())
                <div class="mx-6 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('timetable.update', $entry->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select name="institution_id" label="Institution">
                        @foreach ($institutions as $institution)
                            <flux:select.option value="{{ $institution->id }}"
                                :selected="old('institution_id', $entry->institution_id) == $institution->id">
                                {{ $institution->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select name="class_id" label="Class">
                        @foreach ($classes as $schoolClass)
                            <flux:select.option value="{{ $schoolClass->id }}"
                                :selected="old('class_id', $entry->class_id) == $schoolClass->id">
                                {{ $schoolClass->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input type="text" name="subject" label="Subject"
                        value="{{ old('subject', $entry->subject) }}" required />

                    <flux:select name="day_of_week" label="Day of Week">
                        @foreach ($days as $day)
                            <flux:select.option value="{{ $day }}"
                                :selected="old('day_of_week', $entry->day_of_week) === $day">
                                {{ $day }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input type="time" name="start_time" label="Start Time"
                        value="{{ old('start_time', $entry->start_time?->format('H:i')) }}" required />

                    <flux:input type="time" name="end_time" label="End Time"
                        value="{{ old('end_time', $entry->end_time?->format('H:i')) }}" required />

                    <flux:input type="text" name="room" label="Room" value="{{ old('room', $entry->room) }}" />

                    <flux:select name="teacher_id" label="Teacher">
                        <flux:select.option value="">Unassigned</flux:select.option>
                        @foreach ($teachers as $teacher)
                            <flux:select.option value="{{ $teacher->id }}"
                                :selected="old('teacher_id', $entry->teacher_id) == $teacher->id">
                                {{ $teacher->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <div class="md:col-span-3">
                        <flux:textarea name="notes" rows="2" label="Notes">{{ old('notes', $entry->notes) }}</flux:textarea>
                    </div>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                    <a href="{{ route('timetable.show', $entry->id) }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <flux:button variant="primary" type="submit">Save Changes</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
