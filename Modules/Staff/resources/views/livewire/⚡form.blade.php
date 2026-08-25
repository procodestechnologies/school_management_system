<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\Staff\Actions\SaveStaff;
use Modules\Staff\Models\StaffDetails;

new #[Title('Staff')] class extends Component
{
    public ?StaffDetails $staff = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $staff_number = '';

    public string $job_title = '';

    public string $department = '';

    public string $employment_type = 'full_time';

    public string $hire_date = '';

    public string $salary = '';

    public string $address = '';

    public string $status = 'active';

    public string $notes = '';

    public bool $is_active = true;

    // System access
    public bool $create_account = false;

    public string $password = '';

    public string $system_role = 'Accountant';

    public function mount(?int $staffId = null): void
    {
        if ($staffId === null) {
            abort_unless(auth()->user()->can('create staff'), 403);

            return;
        }

        abort_unless(auth()->user()->can('edit staff'), 403);

        $this->staff = StaffDetails::with('user')->findOrFail($staffId);
        $this->authorizeAccessTo($this->staff);

        $this->fill([
            'name' => (string) $this->staff->name,
            'email' => (string) $this->staff->email,
            'phone' => (string) $this->staff->phone,
            'staff_number' => (string) $this->staff->staff_number,
            'job_title' => (string) $this->staff->job_title,
            'department' => (string) $this->staff->department,
            'employment_type' => (string) ($this->staff->employment_type ?: 'full_time'),
            'hire_date' => $this->staff->hire_date?->format('Y-m-d') ?? '',
            'salary' => (string) ($this->staff->salary ?? ''),
            'address' => (string) $this->staff->address,
            'status' => (string) ($this->staff->status ?: 'active'),
            'notes' => (string) $this->staff->notes,
            'is_active' => (bool) $this->staff->is_active,
            'system_role' => $this->staff->user?->roles->first()?->name ?? 'Accountant',
        ]);
    }

    public function save(): void
    {
        $wantsAccount = $this->create_account;

        if ($this->staff) {
            abort_unless(auth()->user()->can('update staff'), 403);
            $this->authorizeAccessTo($this->staff);

            $validated = $this->validate(SaveStaff::rules($wantsAccount, $this->staff));

            SaveStaff::update($this->staff, $validated, $this->institutionId(), $wantsAccount);

            session()->flash('success', 'Staff member updated successfully!');

            $this->redirectRoute('staff.show', $this->staff->id, navigate: true);

            return;
        }

        abort_unless(auth()->user()->can('create staff'), 403);

        $validated = $this->validate(SaveStaff::rules($wantsAccount));

        SaveStaff::create($validated, $this->institutionId(), $wantsAccount);

        session()->flash('success', 'Staff member created successfully!');

        $this->redirectRoute('staff.index', navigate: true);
    }

    protected function prepareForValidation($attributes)
    {
        foreach ($attributes as $key => $value) {
            if ($value === '' && ! in_array($key, ['name'], true)) {
                $attributes[$key] = null;
            }
        }

        return $attributes;
    }

    private function institutionId(): int
    {
        $institutionId = $this->staff?->institution_id ?? currentInstitution()?->id;

        abort_unless($institutionId, 422, 'No institution selected.');

        return $institutionId;
    }

    private function authorizeAccessTo(StaffDetails $staff): void
    {
        if (isAdmin()) {
            return;
        }

        abort_unless($staff->institution_id === currentInstitution()?->id, 403);
    }
}; ?>

<div class="p-4">
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div
            class="rounded-t-lg border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
            <h4 class="mb-0 text-lg font-semibold text-gray-900 dark:text-white">
                {{ $staff ? 'Edit Staff Member' : 'Add Staff Member' }}
            </h4>
        </div>

        <form wire:submit="save">
            <div class="space-y-8 p-6">
                <div>
                    <h5 class="text-md mb-3 font-semibold text-gray-800 dark:text-zinc-200">Details</h5>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <flux:input label="Full Name" wire:model="name" />
                        <flux:input type="email" label="Email" wire:model="email"
                            description="Required only if they get a login." />
                        <flux:input label="Phone" wire:model="phone" />
                        <flux:input label="Staff Number" wire:model="staff_number" />
                        <flux:input label="Job Title" wire:model="job_title" placeholder="e.g. Bursar" />
                        <flux:input label="Department" wire:model="department" />
                        <flux:select label="Employment Type" wire:model="employment_type">
                            @foreach (['full_time', 'part_time', 'contract', 'volunteer'] as $type)
                                <flux:select.option value="{{ $type }}">
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:input type="date" label="Hire Date" wire:model="hire_date" />
                        <flux:input type="number" step="0.01" min="0" label="Salary" wire:model="salary" />
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

                <div>
                    <h5 class="text-md mb-3 font-semibold text-gray-800 dark:text-zinc-200">System Access</h5>

                    @if ($staff?->user)
                        <flux:text class="mb-3 block text-zinc-500">
                            Signs in as <span class="font-medium">{{ $staff->user->email }}</span>. Fill in a
                            password below only to change it.
                        </flux:text>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <flux:input type="password" label="New Password" wire:model="password" viewable />
                            <flux:select label="Role" wire:model="system_role">
                                @foreach (\Modules\Staff\Actions\SaveStaff::SYSTEM_ROLES as $role)
                                    <flux:select.option value="{{ $role }}">{{ $role }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                    @else
                        <flux:checkbox wire:model.live="create_account" label="Give this staff member a login"
                            description="They'll be able to sign in with the email above." />

                        @if ($create_account)
                            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                                <flux:input type="password" label="Password" wire:model="password" viewable />
                                <flux:select label="Role" wire:model="system_role">
                                    @foreach (\Modules\Staff\Actions\SaveStaff::SYSTEM_ROLES as $role)
                                        <flux:select.option value="{{ $role }}">{{ $role }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <flux:textarea label="Address" rows="2" wire:model="address" />
                    <flux:textarea label="Notes" rows="2" wire:model="notes" />
                </div>
            </div>

            <div
                class="flex justify-end gap-3 rounded-b-lg border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:button href="{{ route('staff.index') }}" variant="ghost" wire:navigate>Cancel</flux:button>
                <flux:button variant="primary" type="submit">
                    <span wire:loading.remove wire:target="save">{{ $staff ? 'Update Staff' : 'Save Staff' }}</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </flux:button>
            </div>
        </form>
    </div>
</div>
