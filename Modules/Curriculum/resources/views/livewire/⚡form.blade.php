<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\Curriculum\Actions\SaveCurriculum;
use Modules\Curriculum\Models\Curriculum;

new #[Title('Curriculum')] class extends Component
{
    public ?Curriculum $curriculum = null;

    public string $name = '';

    public string $system = '844';

    /**
     * Which CBC scale the curriculum is marked on. Only asked for, and only
     * saved, when the system is CBC.
     */
    public string $grading_scheme = Curriculum::SCHEME_RUBRIC;

    public string $status = 'active';

    public function mount(?int $curriculumId = null): void
    {
        if ($curriculumId === null) {
            abort_unless(auth()->user()->can('create curriculum'), 403);

            return;
        }

        abort_unless(auth()->user()->can('edit curriculum'), 403);

        $this->curriculum = $this->scoped()->findOrFail($curriculumId);

        $this->fill([
            'name' => (string) $this->curriculum->name,
            'system' => (string) $this->curriculum->system,
            'grading_scheme' => $this->curriculum->gradingScheme() ?? Curriculum::SCHEME_RUBRIC,
            'status' => (string) $this->curriculum->status,
        ]);
    }

    public function save(): void
    {
        abort_unless(
            auth()->user()->can($this->curriculum ? 'edit curriculum' : 'create curriculum'),
            403
        );

        $validated = $this->validate(SaveCurriculum::rules());

        SaveCurriculum::handle($validated, $this->institutionId(), $this->curriculum);

        session()->flash('success', $this->curriculum ? 'Curriculum updated!' : 'Curriculum created successfully!');

        $this->redirectRoute('curriculum.index', navigate: true);
    }

    private function institutionId(): int
    {
        $institutionId = $this->curriculum?->institution_id ?? currentInstitution()?->id;

        abort_unless($institutionId, 422, 'No institution selected.');

        return $institutionId;
    }

    private function scoped()
    {
        $query = Curriculum::query();

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
                {{ $curriculum ? 'Edit Curriculum' : 'Create Curriculum' }}
            </h4>
        </div>

        <form wire:submit="save">
            <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-2">
                <flux:input label="Name" wire:model="name" placeholder="e.g CBC/8.4.4" />

                <flux:select label="Curriculum System" wire:model.live="system"
                    description="8-4-4 grades A-E; CBC grades against expectations.">
                    @foreach (\Modules\Curriculum\Models\Curriculum::SYSTEMS as $value => $label)
                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>

                @if ($system === 'cbc')
                    <flux:select label="CBC Grading Scale" wire:model="grading_scheme"
                        description="The rubric is what classwork is marked on through the term. KJSEA is the eight-level scale junior school reports against from 2025.">
                        @foreach (\Modules\Curriculum\Models\Curriculum::SCHEMES as $value => $label)
                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>
                @endif

                <flux:select label="Status" wire:model="status">
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="dismissed">Dismissed</flux:select.option>
                </flux:select>
            </div>

            <div
                class="flex justify-end gap-3 rounded-b-lg border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:button href="{{ route('curriculum.index') }}" variant="ghost" wire:navigate>Cancel</flux:button>
                <flux:button variant="primary" type="submit" icon="plus">
                    <span wire:loading.remove wire:target="save">{{ $curriculum ? 'Update' : 'Create' }}</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </flux:button>
            </div>
        </form>
    </div>
</div>
