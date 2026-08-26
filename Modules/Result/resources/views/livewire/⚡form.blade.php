<?php

use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\Examinations\Models\Examination;
use Modules\Result\Actions\SaveResult;
use Modules\Result\Models\Result;
use Modules\Result\Services\ResultAccessService;

new #[Title('Add Result')] class extends Component
{
    public ?Result $result = null;

    public string $class_id = '';

    public string $student_id = '';

    public string $examination_id = '';

    public string $marks_obtained = '';

    public string $remarks = '';

    /**
     * One component behind both /create and /edit - the only difference is
     * whether a result was handed in to start from.
     */
    public function mount(?int $resultId = null): void
    {
        if ($resultId === null) {
            abort_unless(auth()->user()->can('create result'), 403);

            return;
        }

        abort_unless(auth()->user()->can('edit result'), 403);

        $this->result = $this->scoped()->findOrFail($resultId);

        $this->fill([
            'class_id' => (string) $this->result->class_id,
            'student_id' => (string) $this->result->student_id,
            'examination_id' => (string) $this->result->examination_id,
            'marks_obtained' => (string) $this->result->marks_obtained,
            'remarks' => (string) $this->result->remarks,
        ]);
    }

    /**
     * Picking a different class changes who and what can be graded, so the
     * choices below it are cleared rather than left pointing elsewhere.
     */
    public function updatedClassId(): void
    {
        $this->student_id = '';
        $this->examination_id = '';
    }

    public function save(): void
    {
        abort_unless(
            auth()->user()->can($this->result ? 'edit result' : 'create result'),
            403
        );

        $validated = $this->validate(SaveResult::rules());

        // Re-verified server-side rather than trusted from the narrowed
        // dropdowns: a crafted request could otherwise grade a subject this
        // teacher doesn't teach.
        $this->assertCanGrade((int) $validated['class_id'], (int) $validated['examination_id']);

        if (SaveResult::duplicateExists((int) $validated['examination_id'], (int) $validated['student_id'], $this->result?->id)) {
            $this->addError('student_id', 'A result for this student in this examination already exists.');

            return;
        }

        $ceiling = Examination::find($validated['examination_id'])?->total_marks;

        if ($ceiling !== null && $validated['marks_obtained'] > $ceiling) {
            $this->addError('marks_obtained', "Marks can't exceed the examination's total of {$ceiling}.");

            return;
        }

        $saved = SaveResult::handle(
            $validated,
            $this->institutionId(),
            $this->result,
            recordedBy: auth()->id(),
        );

        session()->flash('success', $this->result ? 'Result updated!' : 'Result recorded successfully!');

        $this->redirectRoute('result.show', $saved->id, navigate: true);
    }

    #[Computed]
    public function classes(): Collection
    {
        return ResultAccessService::selectableClasses(auth()->user());
    }

    #[Computed]
    public function examinations(): Collection
    {
        return ResultAccessService::selectableExaminations(
            auth()->user(),
            $this->class_id !== '' ? (int) $this->class_id : null,
        );
    }

    #[Computed]
    public function students(): Collection
    {
        return ResultAccessService::selectableStudents(
            auth()->user(),
            $this->class_id !== '' ? (int) $this->class_id : null,
        );
    }

    private function assertCanGrade(int $classId, int $examinationId): void
    {
        if (! auth()->user()->hasRole('Teacher')) {
            return;
        }

        $subjectId = Examination::find($examinationId)?->subject_id;

        abort_unless(ResultAccessService::canGrade(auth()->user(), $classId, $subjectId), 403);
    }

    private function institutionId(): int
    {
        $institutionId = $this->result?->institution_id
            ?? currentInstitution()?->id
            ?? auth()->user()->teacherUserDetails?->institution_id;

        abort_unless($institutionId, 422, 'No institution selected.');

        return $institutionId;
    }

    private function scoped()
    {
        $query = Result::query();

        ResultAccessService::scopeVisibleResults($query, auth()->user());

        return $query;
    }
}; ?>

<div class="p-4">
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div
            class="rounded-t-lg border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
            <h4 class="mb-0 text-lg font-semibold text-gray-900 dark:text-white">
                {{ $result ? 'Edit Result' : 'Add Result' }}
            </h4>
        </div>

        <form wire:submit="save">
            <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-3">
                <flux:select label="Class" wire:model.live="class_id">
                    <flux:select.option value="">Select Class</flux:select.option>
                    @foreach ($this->classes as $schoolClass)
                        <flux:select.option value="{{ $schoolClass->id }}">{{ $schoolClass->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select label="Student" wire:model="student_id">
                    <flux:select.option value="">
                        {{ $class_id === '' ? 'Select a class first' : 'Select Student' }}
                    </flux:select.option>
                    @foreach ($this->students as $studentDetail)
                        <flux:select.option value="{{ $studentDetail->student_id }}">
                            {{ $studentDetail->student?->name }}
                            @if ($studentDetail->admission_number)
                                ({{ $studentDetail->admission_number }})
                            @endif
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select label="Examination" wire:model="examination_id">
                    <flux:select.option value="">
                        {{ $class_id === '' ? 'Select a class first' : 'Select Examination' }}
                    </flux:select.option>
                    @foreach ($this->examinations as $examination)
                        <flux:select.option value="{{ $examination->id }}">
                            {{ $examination->title }}
                            ({{ $examination->subject?->name ?? $examination->subject_name }})
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input type="number" step="0.01" min="0" label="Marks Obtained"
                    wire:model="marks_obtained" />

                <div class="md:col-span-3 -mt-2">
                    <flux:text class="text-xs text-zinc-500">
                        Grade is computed automatically from your school's grading scale for this class's
                        curriculum.
                        @can('edit reportcard')
                            <a href="{{ route('reportcard.settings') }}" class="underline" wire:navigate>Manage grading
                                scale</a>.
                        @endcan
                    </flux:text>
                </div>

                <div class="md:col-span-3">
                    <flux:textarea label="Remarks" rows="2" wire:model="remarks" />
                </div>
            </div>

            <div
                class="flex justify-end gap-3 rounded-b-lg border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:button href="{{ route('result.index') }}" variant="ghost" wire:navigate>Cancel</flux:button>
                <flux:button variant="primary" type="submit">
                    <span wire:loading.remove wire:target="save">{{ $result ? 'Update Result' : 'Save Result' }}</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </flux:button>
            </div>
        </form>
    </div>
</div>
