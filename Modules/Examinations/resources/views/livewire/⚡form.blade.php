<?php

use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\Classes\Models\SchoolClass;
use Modules\Examinations\Actions\SaveExamination;
use Modules\Examinations\Models\Examination;
use Modules\Subject\Models\Subject;

new #[Title('Examination')] class extends Component
{
    public ?Examination $examination = null;

    public string $class_id = '';

    public string $subject_id = '';

    public string $title = '';

    public string $term = '';

    public string $exam_type = '';

    public string $exam_date = '';

    public string $start_time = '';

    public string $end_time = '';

    public string $total_marks = '100';

    public string $passing_marks = '';

    public string $notes = '';

    public function mount(?int $examinationId = null): void
    {
        if ($examinationId === null) {
            abort_unless(auth()->user()->can('create examination'), 403);

            return;
        }

        abort_unless(auth()->user()->can('edit examination'), 403);

        $this->examination = $this->scoped()->findOrFail($examinationId);

        $this->fill([
            'class_id' => (string) $this->examination->class_id,
            'subject_id' => (string) $this->examination->subject_id,
            'title' => (string) $this->examination->title,
            'term' => (string) $this->examination->term,
            'exam_type' => (string) $this->examination->exam_type,
            'exam_date' => $this->examination->exam_date?->format('Y-m-d') ?? '',
            'start_time' => $this->examination->start_time?->format('H:i') ?? '',
            'end_time' => $this->examination->end_time?->format('H:i') ?? '',
            'total_marks' => (string) $this->examination->total_marks,
            'passing_marks' => (string) ($this->examination->passing_marks ?? ''),
            'notes' => (string) $this->examination->notes,
        ]);
    }

    public function save(): void
    {
        abort_unless(
            auth()->user()->can($this->examination ? 'edit examination' : 'create examination'),
            403
        );

        $validated = $this->validate(SaveExamination::rules());

        $saved = SaveExamination::handle($validated, $this->institutionId(), $this->examination);

        session()->flash('success', $this->examination ? 'Examination updated!' : 'Examination scheduled successfully!');

        $this->redirectRoute('examinations.show', $saved->id, navigate: true);
    }

    /**
     * Optional time and mark fields post an empty string when untouched;
     * the rules behind them expect null.
     */
    protected function prepareForValidation($attributes)
    {
        foreach (['term', 'exam_type', 'start_time', 'end_time', 'passing_marks', 'notes'] as $field) {
            if (($attributes[$field] ?? '') === '') {
                $attributes[$field] = null;
            }
        }

        return $attributes;
    }

    #[Computed]
    public function classes(): Collection
    {
        $query = SchoolClass::query();

        if (! isAdmin()) {
            $query->where('institution_id', currentInstitution()?->id ?? 0);
        }

        return $query->orderBy('name')->get();
    }

    #[Computed]
    public function subjects(): Collection
    {
        $query = Subject::where('is_active', true);

        if (! isAdmin()) {
            $query->where('institution_id', currentInstitution()?->id ?? 0);
        }

        return $query->orderBy('name')->get();
    }

    private function institutionId(): int
    {
        $institutionId = $this->examination?->institution_id ?? currentInstitution()?->id;

        abort_unless($institutionId, 422, 'No institution selected.');

        return $institutionId;
    }

    private function scoped()
    {
        $query = Examination::query();

        if (! isAdmin()) {
            $query->where('institution_id', currentInstitution()?->id ?? 0);
        }

        return $query;
    }
}; ?>

<div class="p-4">
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div
            class="rounded-t-lg border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
            <h4 class="mb-0 text-lg font-semibold text-gray-900 dark:text-white">
                {{ $examination ? 'Edit Examination' : 'Add Examination' }}
            </h4>
        </div>

        <form wire:submit="save">
            <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-3">
                <flux:input label="Title" wire:model="title" placeholder="e.g. End of Term 2 Exam" />

                <flux:select label="Class" wire:model="class_id">
                    <flux:select.option value="">Select Class</flux:select.option>
                    @foreach ($this->classes as $schoolClass)
                        <flux:select.option value="{{ $schoolClass->id }}">{{ $schoolClass->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select label="Subject" wire:model="subject_id">
                    <flux:select.option value="">Select Subject</flux:select.option>
                    @foreach ($this->subjects as $subject)
                        <flux:select.option value="{{ $subject->id }}">{{ $subject->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input label="Term" wire:model="term" placeholder="e.g. Second Term"
                    description="The academic year is taken from the exam date." />

                <flux:select label="Sitting" wire:model="exam_type"
                    description="Which round of papers this belongs to - it's what the exam timetable groups by.">
                    <flux:select.option value="">Not specified</flux:select.option>
                    @foreach (\Modules\Examinations\Models\Examination::EXAM_TYPES as $value => $label)
                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input type="date" label="Exam Date" wire:model="exam_date" />

                <flux:input type="number" min="1" label="Total Marks" wire:model="total_marks" />

                <flux:input type="time" label="Start Time" wire:model="start_time" />
                <flux:input type="time" label="End Time" wire:model="end_time" />
                <flux:input type="number" min="0" label="Passing Marks" wire:model="passing_marks" />

                <div class="md:col-span-3">
                    <flux:textarea label="Notes" rows="2" wire:model="notes" />
                </div>
            </div>

            <div
                class="flex justify-end gap-3 rounded-b-lg border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:button href="{{ route('examinations.index') }}" variant="ghost" wire:navigate>Cancel
                </flux:button>
                <flux:button variant="primary" type="submit">
                    <span wire:loading.remove wire:target="save">
                        {{ $examination ? 'Update Examination' : 'Save Examination' }}
                    </span>
                    <span wire:loading wire:target="save">Saving…</span>
                </flux:button>
            </div>
        </form>
    </div>
</div>
