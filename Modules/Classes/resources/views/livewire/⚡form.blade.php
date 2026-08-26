<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\Classes\Actions\SaveSchoolClass;
use Modules\Classes\Models\SchoolClass;
use Modules\Curriculum\Models\Curriculum;

new #[Title('Class')] class extends Component
{
    public ?SchoolClass $schoolClass = null;

    public string $name = '';

    public string $level = '';

    public string $curriculum_id = '';

    public string $class_teacher_id = '';

    public string $capacity = '';

    public function mount(?int $classId = null): void
    {
        if ($classId === null) {
            abort_unless(auth()->user()->can('create classes'), 403);

            return;
        }

        abort_unless(auth()->user()->can('edit classes'), 403);

        $this->schoolClass = $this->scoped()->findOrFail($classId);

        $this->fill([
            'name' => (string) $this->schoolClass->name,
            'level' => (string) $this->schoolClass->level,
            'curriculum_id' => (string) ($this->schoolClass->curriculum_id ?? ''),
            'class_teacher_id' => (string) ($this->schoolClass->class_teacher_id ?? ''),
            'capacity' => (string) ($this->schoolClass->capacity ?? ''),
        ]);
    }

    public function save(): void
    {
        abort_unless(
            auth()->user()->can($this->schoolClass ? 'edit classes' : 'create classes'),
            403
        );

        $validated = $this->validate(SaveSchoolClass::rules());

        $saved = SaveSchoolClass::handle($validated, $this->institutionId(), $this->schoolClass);

        session()->flash('success', $this->schoolClass ? 'Class updated!' : 'Class created successfully!');

        $this->redirectRoute('classes.show', $saved->id, navigate: true);
    }

    /**
     * An unpicked select posts an empty string; the columns behind these
     * are nullable integers, so blank has to mean null before it ever
     * reaches validation or the database.
     */
    protected function prepareForValidation($attributes)
    {
        foreach (['level', 'curriculum_id', 'class_teacher_id', 'capacity'] as $field) {
            if (($attributes[$field] ?? '') === '') {
                $attributes[$field] = null;
            }
        }

        return $attributes;
    }

    #[Computed]
    public function curricula(): Collection
    {
        $query = Curriculum::where('status', 'active');

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

    private function institutionId(): int
    {
        $institutionId = $this->schoolClass?->institution_id ?? currentInstitution()?->id;

        abort_unless($institutionId, 422, 'No institution selected.');

        return $institutionId;
    }

    private function scoped()
    {
        $query = SchoolClass::query();

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
                {{ $schoolClass ? 'Edit Class' : 'Create Class' }}
            </h4>
        </div>

        <form wire:submit="save">
            <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-2">
                <flux:input label="Class Name" wire:model="name" placeholder="e.g. Grade 8 East" />
                <flux:input label="Level / Grade" wire:model="level" placeholder="e.g. Grade 8" />

                <flux:select label="Curriculum" wire:model="curriculum_id"
                    description="Decides which grading scale this class's results are marked against.">
                    <flux:select.option value="">School default</flux:select.option>
                    @foreach ($this->curricula as $curriculum)
                        <flux:select.option value="{{ $curriculum->id }}">
                            {{ $curriculum->name }} ({{ $curriculum->systemLabel() }})
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select label="Class Teacher" wire:model="class_teacher_id"
                    description="A class teacher can enter results for every subject in this class.">
                    <flux:select.option value="">Unassigned</flux:select.option>
                    @foreach ($this->teachers as $teacher)
                        <flux:select.option value="{{ $teacher->id }}">{{ $teacher->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input type="number" min="1" label="Capacity" wire:model="capacity" />
            </div>

            <div
                class="flex justify-end gap-3 rounded-b-lg border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:button href="{{ route('classes.index') }}" variant="ghost" wire:navigate>Cancel</flux:button>
                <flux:button variant="primary" type="submit">
                    <span wire:loading.remove wire:target="save">{{ $schoolClass ? 'Update Class' : 'Save Class' }}</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </flux:button>
            </div>
        </form>
    </div>
</div>
