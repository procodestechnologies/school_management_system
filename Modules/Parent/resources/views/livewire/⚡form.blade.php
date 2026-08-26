<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\Parent\Actions\SaveParent;
use Modules\Student\Models\StudentDetails;

new #[Title('Parent')] class extends Component
{
    public ?User $parent = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $parent_phone = '';

    public string $parent_occupation = '';

    /** @var array<int, string> student ids to link to this parent */
    public array $children = [];

    public function mount(?int $parentId = null): void
    {
        if ($parentId === null) {
            abort_unless(auth()->user()->can('create parent'), 403);

            return;
        }

        abort_unless(auth()->user()->can('edit parent'), 403);

        $this->parent = User::role('Parent')->with('parent')->findOrFail($parentId);
        $this->authorizeAccessTo($this->parent);

        $this->fill([
            'name' => (string) $this->parent->name,
            'email' => (string) $this->parent->email,
            'parent_phone' => (string) $this->parent->parent?->parent_phone,
            'parent_occupation' => (string) $this->parent->parent?->parent_occupation,
        ]);

        $this->children = StudentDetails::where('parent_id', $this->parent->id)
            ->pluck('student_id')
            ->map(fn ($id) => (string) $id)
            ->all();
    }

    public function save(): void
    {
        if ($this->parent) {
            abort_unless(auth()->user()->can('update parent'), 403);
            $this->authorizeAccessTo($this->parent);

            $validated = $this->validate(SaveParent::updateRules($this->parent));

            SaveParent::update($this->parent, $validated, $this->linkableStudentsQuery());

            session()->flash('success', 'Parent updated successfully!');

            $this->redirectRoute('parent.show', $this->parent->id, navigate: true);

            return;
        }

        abort_unless(auth()->user()->can('create parent'), 403);

        $validated = $this->validate(SaveParent::createRules());

        SaveParent::create($validated, $this->linkableStudentsQuery());

        session()->flash('success', 'Parent created successfully!');

        $this->redirectRoute('parent.index', navigate: true);
    }

    protected function prepareForValidation($attributes)
    {
        foreach (['parent_phone', 'parent_occupation'] as $field) {
            if (($attributes[$field] ?? '') === '') {
                $attributes[$field] = null;
            }
        }

        return $attributes;
    }

    /**
     * Students this parent can be linked to: the ones with no parent yet,
     * plus the ones already theirs so they stay tickable.
     *
     * @return Collection<int, StudentDetails>
     */
    #[Computed]
    public function selectableStudents(): Collection
    {
        $query = StudentDetails::with(['student', 'institution'])
            ->where(function ($q) {
                $q->whereNull('parent_id');

                if ($this->parent) {
                    $q->orWhere('parent_id', $this->parent->id);
                }
            });

        if (! isAdmin()) {
            $query->where('institution_id', currentInstitution()?->id ?? 0);
        }

        return $query->get()->sortBy(fn ($details) => $details->student?->name)->values();
    }

    /**
     * Only students with no parent may be linked - that's what stops a link
     * from quietly stealing a student from another parent.
     */
    private function linkableStudentsQuery()
    {
        $query = StudentDetails::whereNull('parent_id');

        if (! isAdmin()) {
            $query->where('institution_id', currentInstitution()?->id ?? 0);
        }

        return $query;
    }

    /**
     * A parent with no children at all isn't claimed by any institution yet,
     * so any Director can manage them. Otherwise at least one child must be
     * in the viewer's active institution.
     */
    private function authorizeAccessTo(User $parent): void
    {
        if (isAdmin()) {
            return;
        }

        $children = StudentDetails::where('parent_id', $parent->id)->get();

        $accessible = $children->isEmpty()
            || $children->where('institution_id', currentInstitution()?->id)->isNotEmpty();

        abort_unless($accessible, 403);
    }
}; ?>

<div class="p-4">
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div
            class="rounded-t-lg border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
            <h4 class="mb-0 text-lg font-semibold text-gray-900 dark:text-white">
                {{ $parent ? 'Edit Parent' : 'Add Parent' }}
            </h4>
        </div>

        <form wire:submit="save">
            <div class="space-y-8 p-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <flux:input label="Full Name" wire:model="name" />
                    <flux:input type="email" label="Email" wire:model="email" />
                    @if (! $parent)
                        <flux:input type="password" label="Password" wire:model="password" viewable />
                    @endif
                    <flux:input label="Phone" wire:model="parent_phone" />
                    <flux:input label="Occupation" wire:model="parent_occupation" />
                </div>

                <div>
                    <h5 class="text-md mb-1 font-semibold text-gray-800 dark:text-zinc-200">Children</h5>
                    <flux:text class="mb-3 text-zinc-500">
                        Only students without a parent on file are listed - linking here never takes a student
                        from another parent.
                    </flux:text>

                    @if ($this->selectableStudents->isEmpty())
                        <flux:text class="text-zinc-500">No unlinked students available.</flux:text>
                    @else
                        <div class="grid grid-cols-1 gap-2 md:grid-cols-3">
                            @foreach ($this->selectableStudents as $details)
                                <flux:checkbox wire:model="children" value="{{ $details->student_id }}"
                                    wire:key="student-{{ $details->student_id }}"
                                    label="{{ $details->student?->name }}{{ $details->admission_number ? ' ('.$details->admission_number.')' : '' }}" />
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div
                class="flex justify-end gap-3 rounded-b-lg border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:button href="{{ route('parent.index') }}" variant="ghost" wire:navigate>Cancel</flux:button>
                <flux:button variant="primary" type="submit">
                    <span wire:loading.remove wire:target="save">{{ $parent ? 'Update Parent' : 'Save Parent' }}</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </flux:button>
            </div>
        </form>
    </div>
</div>
