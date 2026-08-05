<x-layouts::app :title="__('Edit Result')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">Edit Result</h4>
                <small class="text-sm text-gray-500">{{ $result->student?->name }}</small>
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

            <form action="{{ route('result.update', $result->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select id="institution_id" name="institution_id" label="Institution">
                        @foreach ($institutions as $institution)
                            <flux:select.option value="{{ $institution->id }}"
                                :selected="old('institution_id', $result->institution_id) == $institution->id">
                                {{ $institution->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select id="class_id" name="class_id" label="Class">
                        <flux:select.option value="">Select Class</flux:select.option>
                        @foreach ($classes as $schoolClass)
                            <flux:select.option value="{{ $schoolClass->id }}"
                                data-institution-id="{{ $schoolClass->institution_id }}"
                                :selected="old('class_id', $result->class_id) == $schoolClass->id">
                                {{ $schoolClass->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select id="student_id" name="student_id" label="Student">
                        <flux:select.option value="">Select Student</flux:select.option>
                        @foreach ($students as $studentDetail)
                            <flux:select.option value="{{ $studentDetail->student_id }}"
                                data-class-id="{{ $studentDetail->class_id }}"
                                :selected="old('student_id', $result->student_id) == $studentDetail->student_id">
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
                                :selected="old('examination_id', $result->examination_id) == $examination->id">
                                {{ $examination->title }} ({{ $examination->subject?->name ?? $examination->subject_name }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input type="number" step="0.01" name="marks_obtained" label="Marks Obtained"
                        value="{{ old('marks_obtained', $result->marks_obtained) }}" min="0" required />

                    <div class="md:col-span-3 -mt-2">
                        <flux:text class="text-xs text-zinc-500">
                            Current grade: <strong>{{ $result->grade ?? 'not computed yet' }}</strong> &mdash;
                            recalculated automatically from the institution's grading scale when you save.
                            @can('edit reportcard')
                                <a href="{{ route('reportcard.settings') }}" class="underline">Manage grading
                                    scale</a>.
                            @endcan
                        </flux:text>
                    </div>

                    <div class="md:col-span-3">
                        <flux:textarea name="remarks" rows="2" label="Remarks">{{ old('remarks', $result->remarks) }}</flux:textarea>
                    </div>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                    <a href="{{ route('result.show', $result->id) }}"
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
            const studentSelect = document.getElementById('student_id');
            const examinationSelect = document.getElementById('examination_id');

            // Hides every real option unless it matches the chosen parent
            // (institution for Class; class for Student/Examination), so
            // each field stays empty until its parent is picked. The
            // placeholder text explains why.
            function filterOptions(select, parentValue, datasetKey, lockedText, readyText) {
                const currentValue = select.value;
                const placeholder = select.querySelector('option[value=""]');
                if (placeholder) {
                    placeholder.textContent = parentValue ? readyText : lockedText;
                }

                Array.from(select.options).forEach(function(option) {
                    if (!option.value || option.value === currentValue) {
                        option.hidden = false;
                        return;
                    }

                    option.hidden = !parentValue || option.dataset[datasetKey] !== String(parentValue);
                });
            }

            // When a field has exactly one real (visible) choice, there's
            // nothing meaningful left to pick - select it automatically so
            // the cascade doesn't stay locked behind a redundant click.
            function autoSelectIfOnlyChoice(select) {
                if (select.value) {
                    return false;
                }

                const visible = Array.from(select.options).filter(o => o.value && !o.hidden);
                if (visible.length === 1) {
                    select.value = visible[0].value;
                    return true;
                }

                return false;
            }

            function applyClassFilters() {
                filterOptions(studentSelect, classSelect.value, 'classId', 'Select a class first', 'Select Student');
                filterOptions(examinationSelect, classSelect.value, 'classId', 'Select a class first',
                    'Select Examination');
                autoSelectIfOnlyChoice(studentSelect);
                autoSelectIfOnlyChoice(examinationSelect);
            }

            function applyInstitutionFilter() {
                filterOptions(classSelect, institutionSelect.value, 'institutionId', 'Select an institution first',
                    'Select Class');
                autoSelectIfOnlyChoice(classSelect);
            }

            // Institution -> Class -> {Student, Examination}. Changing a
            // parent resets and re-filters everything below it so stale,
            // out-of-scope picks can't linger.
            institutionSelect.addEventListener('change', function() {
                classSelect.value = '';
                studentSelect.value = '';
                examinationSelect.value = '';
                applyInstitutionFilter();
                applyClassFilters();
            });

            classSelect.addEventListener('change', function() {
                studentSelect.value = '';
                examinationSelect.value = '';
                applyClassFilters();
            });

            autoSelectIfOnlyChoice(institutionSelect);
            applyInstitutionFilter();
            applyClassFilters();
        });
    </script>
</x-layouts::app>
