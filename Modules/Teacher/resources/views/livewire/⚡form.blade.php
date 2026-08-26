<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\Teacher\Actions\SaveTeacher;
use Modules\Teacher\Models\TeacherDetails;

new #[Title('Teacher')] class extends Component
{
    public ?TeacherDetails $details = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $phone = '';

    public string $employee_number = '';

    public string $department = '';

    public string $qualification = '';

    public string $hire_date = '';

    public string $address = '';

    public string $status = 'active';

    public string $notes = '';

    public bool $is_active = true;

    public function mount(?int $teacherId = null): void
    {
        if ($teacherId === null) {
            abort_unless(auth()->user()->can('create teacher'), 403);

            return;
        }

        abort_unless(auth()->user()->can('edit teacher'), 403);

        $this->details = TeacherDetails::with('teacher')->where('teacher_id', $teacherId)->firstOrFail();
        $this->authorizeAccessTo($this->details);

        $this->fill([
            'name' => (string) $this->details->teacher?->name,
            'email' => (string) $this->details->teacher?->email,
            'phone' => (string) $this->details->phone,
            'employee_number' => (string) $this->details->employee_number,
            'department' => (string) $this->details->department,
            'qualification' => (string) $this->details->qualification,
            'hire_date' => $this->details->hire_date?->format('Y-m-d') ?? '',
            'address' => (string) $this->details->address,
            'status' => (string) ($this->details->status ?: 'active'),
            'notes' => (string) $this->details->notes,
            'is_active' => (bool) $this->details->is_active,
        ]);
    }

    public function save(): void
    {
        if ($this->details) {
            abort_unless(auth()->user()->can('update teacher'), 403);
            $this->authorizeAccessTo($this->details);

            $validated = $this->validate(SaveTeacher::updateRules($this->details->teacher, $this->details));

            SaveTeacher::update($this->details, $validated, $this->institutionId());

            session()->flash('success', 'Teacher updated successfully!');

            $this->redirectRoute('teacher.show', $this->details->teacher_id, navigate: true);

            return;
        }

        abort_unless(auth()->user()->can('create teacher'), 403);

        $validated = $this->validate(SaveTeacher::createRules());

        SaveTeacher::create($validated, $this->institutionId());

        session()->flash('success', 'Teacher created successfully!');

        $this->redirectRoute('teacher.index', navigate: true);
    }

    protected function prepareForValidation($attributes)
    {
        foreach ($attributes as $key => $value) {
            if ($value === '' && ! in_array($key, ['name', 'email', 'password'], true)) {
                $attributes[$key] = null;
            }
        }

        return $attributes;
    }

    private function institutionId(): int
    {
        $institutionId = $this->details?->institution_id ?? currentInstitution()?->id;

        abort_unless($institutionId, 422, 'No institution selected.');

        return $institutionId;
    }

    /**
     * A non-admin only manages teachers from their currently active
     * institution.
     */
    private function authorizeAccessTo(TeacherDetails $details): void
    {
        if (isAdmin()) {
            return;
        }

        abort_unless($details->institution_id === currentInstitution()?->id, 403);
    }
}; ?>

<div class="p-4">
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div
            class="rounded-t-lg border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
            <h4 class="mb-0 text-lg font-semibold text-gray-900 dark:text-white">
                {{ $details ? 'Edit Teacher' : 'Add Teacher' }}
            </h4>
        </div>

        <form wire:submit="save">
            <div class="space-y-8 p-6">
                <div>
                    <h5 class="text-md mb-3 font-semibold text-gray-800 dark:text-zinc-200">Account</h5>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <flux:input label="Full Name" wire:model="name" />
                        <flux:input type="email" label="Email" wire:model="email" />
                        @if (! $details)
                            <flux:input type="password" label="Password" wire:model="password" viewable />
                        @endif
                    </div>
                </div>

                <div>
                    <h5 class="text-md mb-3 font-semibold text-gray-800 dark:text-zinc-200">Employment</h5>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <flux:input label="Staff Number" wire:model="employee_number" />
                        <flux:input label="Department" wire:model="department" placeholder="e.g. Sciences" />
                        <flux:input label="Qualification" wire:model="qualification"
                            placeholder="e.g. BEd Mathematics" />
                        <flux:input type="date" label="Hire Date" wire:model="hire_date" />
                        <flux:input label="Phone" wire:model="phone" />
                        <flux:select label="Status" wire:model="status">
                            @foreach (['active', 'on_leave', 'suspended', 'resigned', 'terminated'] as $option)
                                <flux:select.option value="{{ $option }}">
                                    {{ ucfirst(str_replace('_', ' ', $option)) }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:checkbox wire:model="is_active" label="Active" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <flux:textarea label="Address" rows="2" wire:model="address" />
                    <flux:textarea label="Notes" rows="2" wire:model="notes" />
                </div>
            </div>

            <div
                class="flex justify-end gap-3 rounded-b-lg border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:button href="{{ route('teacher.index') }}" variant="ghost" wire:navigate>Cancel</flux:button>
                <flux:button variant="primary" type="submit">
                    <span wire:loading.remove wire:target="save">
                        {{ $details ? 'Update Teacher' : 'Save Teacher' }}
                    </span>
                    <span wire:loading wire:target="save">Saving…</span>
                </flux:button>
            </div>
        </form>
    </div>
</div>
