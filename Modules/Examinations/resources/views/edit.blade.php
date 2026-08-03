<x-layouts::app :title="__('Edit Examination')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">Edit Examination</h4>
                <small class="text-sm text-gray-500">{{ $examination->title }}</small>
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

            <form action="{{ route('examinations.update', $examination->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select id="institution_id" name="institution_id" label="Institution">
                        @foreach ($institutions as $institution)
                            <flux:select.option value="{{ $institution->id }}"
                                :selected="old('institution_id', $examination->institution_id) == $institution->id">
                                {{ $institution->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input type="text" name="title" label="Title"
                        value="{{ old('title', $examination->title) }}" required />

                    <flux:select id="subject_id" name="subject_id" label="Subject">
                        <flux:select.option value="">Select Subject</flux:select.option>
                        @foreach ($subjects as $subject)
                            <flux:select.option value="{{ $subject->id }}"
                                data-institution-id="{{ $subject->institution_id }}"
                                :selected="old('subject_id', $examination->subject_id) == $subject->id">
                                {{ $subject->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select id="class_id" name="class_id" label="Class">
                        <flux:select.option value="">Select Class</flux:select.option>
                        @foreach ($classes as $schoolClass)
                            <flux:select.option value="{{ $schoolClass->id }}"
                                data-institution-id="{{ $schoolClass->institution_id }}"
                                :selected="old('class_id', $examination->class_id) == $schoolClass->id">
                                {{ $schoolClass->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input type="text" name="term" label="Term"
                        value="{{ old('term', $examination->term) }}" />

                    <flux:input type="date" name="exam_date" label="Exam Date"
                        value="{{ old('exam_date', $examination->exam_date?->format('Y-m-d')) }}" required />

                    <flux:input type="time" name="start_time" label="Start Time"
                        value="{{ old('start_time', $examination->start_time?->format('H:i')) }}" />

                    <flux:input type="time" name="end_time" label="End Time"
                        value="{{ old('end_time', $examination->end_time?->format('H:i')) }}" />

                    <flux:input type="number" name="total_marks" label="Total Marks"
                        value="{{ old('total_marks', $examination->total_marks) }}" min="1" required />

                    <flux:input type="number" name="passing_marks" label="Passing Marks"
                        value="{{ old('passing_marks', $examination->passing_marks) }}" min="0" />

                    <div class="md:col-span-3">
                        <flux:textarea name="notes" rows="2" label="Notes">{{ old('notes', $examination->notes) }}</flux:textarea>
                    </div>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                    <a href="{{ route('examinations.show', $examination->id) }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <flux:button variant="primary" type="submit">Save Changes</flux:button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const institutionSelect = document.getElementById('institution_id');
            const classSelect = document.getElementById('class_id');
            const subjectSelect = document.getElementById('subject_id');

            function filterByInstitution(select) {
                const institutionId = institutionSelect.value;
                const currentValue = select.value;

                Array.from(select.options).forEach(function(option) {
                    if (!option.value || option.value === currentValue) {
                        option.hidden = false;
                        return;
                    }

                    option.hidden = !!institutionId && option.dataset.institutionId !== String(institutionId);
                });
            }

            function applyFilters() {
                filterByInstitution(classSelect);
                filterByInstitution(subjectSelect);
            }

            institutionSelect.addEventListener('change', applyFilters);
            applyFilters();
        });
    </script>
</x-layouts::app>
