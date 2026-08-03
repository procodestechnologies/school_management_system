@php
    use Modules\Selections\Models\SubjectSelection;
    use Modules\Student\Models\StudentDetails;
    use Modules\Subject\Models\Subject;

    $studentDetails = StudentDetails::where('student_id', $student->id)->first();

    $compulsorySubjects = collect();
    $electiveSubjects = collect();

    if ($studentDetails) {
        $compulsorySubjects = Subject::where('institution_id', $studentDetails->institution_id)
            ->where('is_compulsory', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $electiveSubjects = Subject::whereIn(
            'id',
            SubjectSelection::where('student_id', $student->id)->pluck('subject_id')
        )->orderBy('name')->get();
    }
@endphp

@if ($studentDetails && ($compulsorySubjects->isNotEmpty() || $electiveSubjects->isNotEmpty()))
    <div class="mb-6">
        <h5 class="text-md font-semibold text-gray-800 mb-3">Selected Subjects</h5>
        <div class="bg-gray-50 p-4 rounded-lg">
            <div class="mb-3">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                    Compulsory
                </label>
                @if ($compulsorySubjects->isEmpty())
                    <p class="text-sm text-gray-400">None set up yet.</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach ($compulsorySubjects as $subject)
                            <flux:badge color="amber">{{ $subject->name }}</flux:badge>
                        @endforeach
                    </div>
                @endif
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                    Electives
                </label>
                @if ($electiveSubjects->isEmpty())
                    <p class="text-sm text-gray-400">No electives selected yet.</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach ($electiveSubjects as $subject)
                            <flux:badge color="zinc">{{ $subject->name }}</flux:badge>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
