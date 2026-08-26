<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\Subject\Actions\SaveSubject;
use Modules\Subject\Models\Subject;

new #[Title('Subject')] class extends Component
{
    public ?Subject $subject = null;

    public string $name = '';

    public string $code = '';

    public bool $is_compulsory = false;

    public bool $is_active = true;

    public function mount(?int $subjectId = null): void
    {
        if ($subjectId === null) {
            abort_unless(auth()->user()->can('create subject'), 403);

            return;
        }

        abort_unless(auth()->user()->can('edit subject'), 403);

        $this->subject = $this->scoped()->findOrFail($subjectId);

        $this->fill([
            'name' => (string) $this->subject->name,
            'code' => (string) $this->subject->code,
            'is_compulsory' => (bool) $this->subject->is_compulsory,
            'is_active' => (bool) $this->subject->is_active,
        ]);
    }

    public function save(): void
    {
        abort_unless(
            auth()->user()->can($this->subject ? 'edit subject' : 'create subject'),
            403
        );

        $validated = $this->validate(SaveSubject::rules());

        SaveSubject::handle(
            $validated + ['is_compulsory' => $this->is_compulsory, 'is_active' => $this->is_active],
            $this->institutionId(),
            $this->subject,
        );

        session()->flash('success', $this->subject ? 'Subject updated!' : 'Subject created successfully!');

        $this->redirectRoute('subject.index', navigate: true);
    }

    private function institutionId(): int
    {
        $institutionId = $this->subject?->institution_id ?? currentInstitution()?->id;

        abort_unless($institutionId, 422, 'No institution selected.');

        return $institutionId;
    }

    private function scoped()
    {
        $query = Subject::query();

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
                {{ $subject ? 'Edit Subject' : 'Create Subject' }}
            </h4>
        </div>

        <form wire:submit="save">
            <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-2">
                <flux:input label="Subject Name" wire:model="name" placeholder="e.g. Mathematics" />
                <flux:input label="Code" wire:model="code" placeholder="e.g. MATH" />

                <flux:checkbox wire:model="is_compulsory" label="Compulsory"
                    description="Every student takes this subject automatically" />
                <flux:checkbox wire:model="is_active" label="Active" />
            </div>

            <div
                class="flex justify-end gap-3 rounded-b-lg border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:button href="{{ route('subject.index') }}" variant="ghost" wire:navigate>Cancel</flux:button>
                <flux:button variant="primary" type="submit">
                    <span wire:loading.remove wire:target="save">{{ $subject ? 'Update Subject' : 'Save Subject' }}</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </flux:button>
            </div>
        </form>
    </div>
</div>
