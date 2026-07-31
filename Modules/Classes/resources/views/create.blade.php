<x-layouts::app :title="__('Create Class')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">Create Class</h4>
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

            <form action="{{ route('classes.store') }}" method="POST">
                @csrf

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select name="institution_id" label="Institution">
                        <flux:select.option value="">Select Institution</flux:select.option>
                        @foreach ($institutions as $institution)
                            <flux:select.option value="{{ $institution->id }}"
                                :selected="old('institution_id') == $institution->id">
                                {{ $institution->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input type="text" name="name" label="Class Name" value="{{ old('name') }}"
                        placeholder="e.g. Grade 8 East" required />

                    <flux:input type="text" name="level" label="Level / Grade" value="{{ old('level') }}"
                        placeholder="e.g. Grade 8" />

                    <flux:input type="number" name="capacity" label="Capacity" value="{{ old('capacity') }}"
                        min="1" />

                    <flux:select name="class_teacher_id" label="Class Teacher">
                        <flux:select.option value="">Unassigned</flux:select.option>
                        @foreach ($teachers as $teacher)
                            <flux:select.option value="{{ $teacher->id }}"
                                :selected="old('class_teacher_id') == $teacher->id">
                                {{ $teacher->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                    <a href="{{ route('classes.index') }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <flux:button variant="primary" type="submit">Save Class</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
