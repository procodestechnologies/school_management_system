<x-layouts::app :title="__('Enter Marks')">
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

        @if ($errors->any())
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <flux:card class="mb-4">
            <flux:heading size="lg" class="mb-2">Enter Marks</flux:heading>
            <flux:text class="mb-4 text-zinc-500">
                Pick an examination to mark the whole class in one go. You'll only see the examinations for
                subjects you teach - and, if you're a class teacher, every subject in your own class.
            </flux:text>

            <form action="{{ route('result.entry.create') }}" method="GET" class="flex flex-wrap items-end gap-2">
                <flux:select name="examination_id" label="Examination" class="min-w-80">
                    <flux:select.option value="">Select an examination</flux:select.option>
                    @foreach ($examinations as $option)
                        <flux:select.option value="{{ $option->id }}" :selected="$examination?->id === $option->id">
                            {{ $option->schoolClass?->name ?? $option->class_name }} —
                            {{ $option->subject?->name ?? $option->subject_name }} —
                            {{ $option->title }}
                            @if ($option->exam_date)
                                ({{ $option->exam_date->format('d M Y') }})
                            @endif
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:button type="submit" variant="primary" icon="arrow-right">Open Marks Sheet</flux:button>
            </form>
        </flux:card>

        @if ($examination)
            <flux:card>
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <flux:heading size="lg">
                            {{ $examination->subject?->name ?? $examination->subject_name }} —
                            {{ $examination->schoolClass?->name ?? $examination->class_name }}
                        </flux:heading>
                        <flux:text class="text-zinc-500">
                            {{ $examination->title }} · out of {{ $examination->total_marks }}
                            @if ($examination->term)
                                · {{ $examination->term }} {{ $examination->academic_year }}
                            @endif
                        </flux:text>
                    </div>
                    <flux:badge color="zinc">{{ $students->count() }} student(s)</flux:badge>
                </div>

                @if ($students->isEmpty())
                    <flux:text class="text-zinc-500">
                        No students are enrolled in this class yet, so there's nothing to mark.
                    </flux:text>
                @else
                    <form action="{{ route('result.entry.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="examination_id" value="{{ $examination->id }}">

                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>#</flux:table.column>
                                <flux:table.column>Admission No.</flux:table.column>
                                <flux:table.column>Student</flux:table.column>
                                <flux:table.column>Marks (out of {{ $examination->total_marks }})
                                </flux:table.column>
                                <flux:table.column>Current Grade</flux:table.column>
                                <flux:table.column>Remarks</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach ($students as $index => $details)
                                    @php($result = $existing->get($details->student_id))
                                    <flux:table.row>
                                        <flux:table.cell>{{ $index + 1 }}</flux:table.cell>
                                        <flux:table.cell>{{ $details->admission_number ?? '—' }}</flux:table.cell>
                                        <flux:table.cell>{{ $details->student?->name ?? '—' }}</flux:table.cell>
                                        <flux:table.cell>
                                            <flux:input type="number" step="0.01" min="0"
                                                max="{{ $examination->total_marks }}"
                                                name="marks[{{ $details->student_id }}]"
                                                value="{{ old('marks.' . $details->student_id, $result?->marks_obtained) }}"
                                                size="sm" />
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            @if ($result?->grade)
                                                <flux:badge color="emerald">{{ $result->grade }}</flux:badge>
                                            @else
                                                <flux:text class="text-zinc-500">—</flux:text>
                                            @endif
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:input type="text"
                                                name="remarks[{{ $details->student_id }}]"
                                                value="{{ old('remarks.' . $details->student_id, $result?->remarks) }}"
                                                size="sm" />
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>

                        <div class="mt-4 flex items-center justify-between gap-3">
                            <flux:text class="text-xs text-zinc-500">
                                Leave a row blank to skip it - blanks never overwrite a mark that's already
                                recorded. Grades are worked out from your school's grading scale for this class's
                                curriculum.
                            </flux:text>
                            <flux:button type="submit" variant="primary" icon="check">Save Marks</flux:button>
                        </div>
                    </form>
                @endif
            </flux:card>
        @endif
    </div>
</x-layouts::app>
