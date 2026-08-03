<x-layouts::app :title="__('Add Result')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">Add Result</h4>
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

            <form action="{{ route('result.store') }}" method="POST">
                @csrf

                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select name="institution_id" label="Institution">
                        <flux:select.option value="">Select Institution</flux:select.option>
                        @foreach ($institutions as $institution)
                            <flux:select.option value="{{ $institution->id }}"
                                :selected="old('institution_id') == $institution->id">
                                {{ $institution->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select id="class_id" name="class_id" label="Class">
                        <flux:select.option value="">Select Class</flux:select.option>
                        @foreach ($classes as $schoolClass)
                            <flux:select.option value="{{ $schoolClass->id }}"
                                :selected="old('class_id') == $schoolClass->id">
                                {{ $schoolClass->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select id="student_id" name="student_id" label="Student">
                        <flux:select.option value="">Select Student</flux:select.option>
                        @foreach ($students as $studentDetail)
                            <flux:select.option value="{{ $studentDetail->student_id }}"
                                data-class-id="{{ $studentDetail->class_id }}"
                                :selected="old('student_id') == $studentDetail->student_id">
                                {{ $studentDetail->student?->name }}
                                @if ($studentDetail->admission_number)
                                    ({{ $studentDetail->admission_number }})
                                @endif
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select id="examination_id" name="examination_id" label="Examination">
                        <flux:select.option value="">Select Examination</flux:select.option>
                        @foreach ($examinations as $examination)
                            <flux:select.option value="{{ $examination->id }}"
                                data-class-id="{{ $examination->class_id }}"
                                :selected="old('examination_id') == $examination->id">
                                {{ $examination->title }} ({{ $examination->subject }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input type="number" step="0.01" name="marks_obtained" label="Marks Obtained"
                        value="{{ old('marks_obtained') }}" min="0" required />

                    <flux:input type="text" name="grade" label="Grade" value="{{ old('grade') }}"
                        placeholder="e.g. A, B+" />

                    <div class="md:col-span-3">
                        <flux:textarea name="remarks" rows="2" label="Remarks">{{ old('remarks') }}</flux:textarea>
                    </div>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                    <a href="{{ route('result.index') }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <flux:button variant="primary" type="submit">Save Result</flux:button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const classSelect = document.getElementById('class_id');
            const examinationSelect = document.getElementById('examination_id');
            const studentSelect = document.getElementById('student_id');

            function filterByClass(select, classId) {
                const currentValue = select.value;

                Array.from(select.options).forEach(function(option) {
                    if (!option.value || option.value === currentValue) {
                        option.hidden = false;
                        return;
                    }

                    option.hidden = !!classId && option.dataset.classId !== String(classId);
                });
            }

            function applyFilters() {
                filterByClass(examinationSelect, classSelect.value);
                filterByClass(studentSelect, classSelect.value);
            }

            classSelect.addEventListener('change', applyFilters);
            applyFilters();
        });
    </script>
</x-layouts::app>
