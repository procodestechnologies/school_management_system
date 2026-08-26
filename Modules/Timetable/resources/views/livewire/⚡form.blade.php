<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\Classes\Models\SchoolClass;
use Modules\Subject\Models\Subject;
use Modules\Timetable\Actions\SaveTimetableEntry;
use Modules\Timetable\Models\TimetableEntry;

new #[Title('Timetable Entry')] class extends Component
{
    public ?TimetableEntry $entry = null;

    public string $class_id = '';

    public string $teacher_id = '';

    public string $subject = '';

    public string $day_of_week = 'Monday';

    public string $start_time = '';

    public string $end_time = '';

    public string $room = '';

    public string $notes = '';

    public function mount(?int $entryId = null): void
    {
        if ($entryId === null) {
            abort_unless(auth()->user()->can('create timetable'), 403);

            return;
        }

        abort_unless(auth()->user()->can('edit timetable'), 403);

        $this->entry = $this->scoped()->findOrFail($entryId);

        $this->fill([
            'class_id' => (string) $this->entry->class_id,
            'teacher_id' => (string) ($this->entry->teacher_id ?? ''),
            'subject' => (string) $this->entry->subject,
            'day_of_week' => (string) $this->entry->day_of_week,
            'start_time' => $this->entry->start_time?->format('H:i') ?? '',
            'end_time' => $this->entry->end_time?->format('H:i') ?? '',
            'room' => (string) $this->entry->room,
            'notes' => (string) $this->entry->notes,
        ]);
    }

    public function save(): void
    {
        abort_unless(
            auth()->user()->can($this->entry ? 'edit timetable' : 'create timetable'),
            403
        );

        $validated = $this->validate(SaveTimetableEntry::rules());

        if (SaveTimetableEntry::teacherIsDoubleBooked($validated, $this->entry?->id)) {
            $this->addError('teacher_id', 'That teacher is already scheduled elsewhere in this slot.');

            return;
        }

        $saved = SaveTimetableEntry::handle($validated, $this->institutionId(), $this->entry);

        session()->flash('success', $this->entry ? 'Timetable entry updated!' : 'Timetable entry created successfully!');

        $this->redirectRoute('timetable.show', $saved->id, navigate: true);
    }

    protected function prepareForValidation($attributes)
    {
        foreach (['teacher_id', 'room', 'notes'] as $field) {
            if (($attributes[$field] ?? '') === '') {
                $attributes[$field] = null;
            }
        }

        return $attributes;
    }

    /**
     * @return string[]
     */
    public function days(): array
    {
        return SaveTimetableEntry::DAYS;
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
    public function teachers(): Collection
    {
        $query = User::role('Teacher')->with('teacherUserDetails');

        if (! isAdmin()) {
            $institutionId = currentInstitution()?->id ?? 0;
            $query->whereHas('teacherUserDetails', fn ($q) => $q->where('institution_id', $institutionId));
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Offered as suggestions - the column is free text, since not every
     * subject taught has a catalog entry.
     */
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
        $institutionId = $this->entry?->institution_id ?? currentInstitution()?->id;

        abort_unless($institutionId, 422, 'No institution selected.');

        return $institutionId;
    }

    private function scoped()
    {
        $query = TimetableEntry::query();

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
                {{ $entry ? 'Edit Timetable Entry' : 'Add Timetable Entry' }}
            </h4>
        </div>

        <form wire:submit="save">
            <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-3">
                <flux:select label="Class" wire:model="class_id">
                    <flux:select.option value="">Select Class</flux:select.option>
                    @foreach ($this->classes as $schoolClass)
                        <flux:select.option value="{{ $schoolClass->id }}">{{ $schoolClass->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input label="Subject" wire:model="subject" list="timetable-subjects"
                    placeholder="e.g. Mathematics" />
                <datalist id="timetable-subjects">
                    @foreach ($this->subjects as $subject)
                        <option value="{{ $subject->name }}"></option>
                    @endforeach
                </datalist>

                <flux:select label="Teacher" wire:model="teacher_id">
                    <flux:select.option value="">Unassigned</flux:select.option>
                    @foreach ($this->teachers as $teacher)
                        <flux:select.option value="{{ $teacher->id }}">{{ $teacher->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select label="Day" wire:model="day_of_week">
                    @foreach ($this->days() as $day)
                        <flux:select.option value="{{ $day }}">{{ $day }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input type="time" label="Start Time" wire:model="start_time" />
                <flux:input type="time" label="End Time" wire:model="end_time" />

                <flux:input label="Room" wire:model="room" placeholder="e.g. Lab 2" />

                <div class="md:col-span-3">
                    <flux:textarea label="Notes" rows="2" wire:model="notes" />
                </div>
            </div>

            <div
                class="flex justify-end gap-3 rounded-b-lg border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:button href="{{ route('timetable.index') }}" variant="ghost" wire:navigate>Cancel</flux:button>
                <flux:button variant="primary" type="submit">
                    <span wire:loading.remove wire:target="save">{{ $entry ? 'Update Entry' : 'Save Entry' }}</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </flux:button>
            </div>
        </form>
    </div>
</div>
