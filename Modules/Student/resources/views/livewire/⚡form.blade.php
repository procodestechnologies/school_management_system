<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Student\Actions\SaveStudent;
use Modules\Student\Models\StudentDetails;

new #[Title('Student')] class extends Component
{
    use WithFileUploads;

    public ?User $student = null;

    // Account
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public $profile_image = null;

    // Personal
    public string $phone = '';

    public string $date_of_birth = '';

    public string $gender = '';

    public string $admission_number = '';

    public string $student_number = '';

    public string $enrollment_status = 'active';

    public bool $is_active = true;

    // Address
    public string $address = '';

    public string $city = '';

    public string $state = '';

    public string $country = '';

    // Parent
    public string $parent_id = '';

    public string $parent_name = '';

    public string $parent_phone = '';

    public string $parent_email = '';

    public string $parent_occupation = '';

    // Guardian
    public string $guardian_name = '';

    public string $guardian_phone = '';

    public string $guardian_email = '';

    public string $guardian_relationship = '';

    // Additional
    public string $medical_conditions = '';

    public string $allergies = '';

    public string $special_needs = '';

    public string $notes = '';

    public function mount(?int $studentId = null): void
    {
        if ($studentId === null) {
            abort_unless(auth()->user()->can('create student'), 403);

            return;
        }

        abort_unless(auth()->user()->can('edit student'), 403);

        $this->student = User::with(['studentUserDetails', 'studentParent'])->findOrFail($studentId);
        $this->authorizeStudentAccess($this->student);

        $details = $this->student->studentUserDetails;
        $parent = $details?->parent_id ? User::find($details->parent_id) : null;

        $this->fill([
            'name' => (string) $this->student->name,
            'email' => (string) $this->student->email,
            'phone' => (string) $details?->phone,
            'date_of_birth' => $details?->date_of_birth ? substr((string) $details->date_of_birth, 0, 10) : '',
            'gender' => (string) $details?->gender,
            'admission_number' => (string) $details?->admission_number,
            'student_number' => (string) $details?->student_number,
            'enrollment_status' => (string) ($details?->enrollment_status ?: 'active'),
            'is_active' => (bool) $details?->is_active,
            'address' => (string) $details?->address,
            'city' => (string) $details?->city,
            'state' => (string) $details?->state,
            'country' => (string) $details?->country,
            'parent_name' => (string) ($parent?->name ?? $details?->parent_name),
            'parent_phone' => (string) $details?->parent_phone,
            'parent_email' => (string) ($parent?->email ?? $details?->parent_email),
            'parent_occupation' => (string) $details?->parent_occupation,
            'guardian_name' => (string) $details?->guardian_name,
            'guardian_phone' => (string) $details?->guardian_phone,
            'guardian_email' => (string) $details?->guardian_email,
            'guardian_relationship' => (string) $details?->guardian_relationship,
            'medical_conditions' => (string) $details?->medical_conditions,
            'allergies' => (string) $details?->allergies,
            'special_needs' => (string) $details?->special_needs,
            'notes' => (string) $details?->notes,
        ]);
    }

    public function save(): void
    {
        $saver = app(SaveStudent::class);

        if ($this->student) {
            abort_unless(auth()->user()->can('update student'), 403);
            $this->authorizeStudentAccess($this->student);

            $validated = $this->validate(SaveStudent::updateRules());

            $saver->update($this->student, $validated, $this->profile_image);

            session()->flash('success', 'Student updated successfully!');

            $this->redirectRoute('student.show', $this->student->id, navigate: true);

            return;
        }

        abort_unless(auth()->user()->can('create student'), 403);

        $validated = $this->validate(SaveStudent::createRules());

        $saver->create($validated, $this->institutionId(), $this->profile_image);

        session()->flash('success', 'Student created successfully!');

        $this->redirectRoute('student.index', navigate: true);
    }

    /**
     * Optional text fields post an empty string when untouched; the rules
     * and columns behind them expect null.
     */
    protected function prepareForValidation($attributes)
    {
        foreach ($attributes as $key => $value) {
            if ($value === '' && ! in_array($key, ['name', 'email', 'password'], true)) {
                $attributes[$key] = null;
            }
        }

        return $attributes;
    }

    /**
     * A parent can have more than one student, so existing parents are
     * offered for linking rather than making a duplicate account each time.
     */
    #[Computed]
    public function existingParents(): Collection
    {
        return User::role('Parent')->orderBy('name')->get();
    }

    private function institutionId(): int
    {
        $institutionId = currentInstitution()?->id;

        abort_unless($institutionId, 422, 'No institution selected.');

        return $institutionId;
    }

    /**
     * A Parent may only reach their own children, a Student only
     * themselves, and everyone else only students in whichever institution
     * is currently active for them. Admin is unrestricted.
     */
    private function authorizeStudentAccess(User $student): void
    {
        if (isAdmin()) {
            return;
        }

        $user = auth()->user();

        if ($user->hasRole('Parent')) {
            abort_unless(
                StudentDetails::where('student_id', $student->id)->where('parent_id', $user->id)->exists(),
                403
            );

            return;
        }

        if ($user->hasRole('Student')) {
            abort_unless($student->id === $user->id, 403);

            return;
        }

        $studentInstitutionId = StudentDetails::where('student_id', $student->id)->value('institution_id');

        abort_unless($studentInstitutionId && $studentInstitutionId === currentInstitution()?->id, 403);
    }
}; ?>

<div class="p-4">
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div
            class="rounded-t-lg border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
            <h4 class="mb-0 text-lg font-semibold text-gray-900 dark:text-white">
                {{ $student ? 'Edit Student' : 'Add Student' }}
            </h4>
        </div>

        <form wire:submit="save">
            <div class="space-y-8 p-6">
                @if (! $student)
                    <div>
                        <h5 class="text-md mb-3 font-semibold text-gray-800 dark:text-zinc-200">Account</h5>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <flux:input label="Full Name" wire:model="name" />
                            <flux:input type="email" label="Email" wire:model="email" />
                            <flux:input type="password" label="Password" wire:model="password" viewable />
                        </div>
                    </div>
                @endif

                <div>
                    <h5 class="text-md mb-3 font-semibold text-gray-800 dark:text-zinc-200">Personal</h5>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <flux:input label="Phone" wire:model="phone" />
                        <flux:input type="date" label="Date of Birth" wire:model="date_of_birth" />
                        <flux:select label="Gender" wire:model="gender">
                            <flux:select.option value="">Not stated</flux:select.option>
                            <flux:select.option value="male">Male</flux:select.option>
                            <flux:select.option value="female">Female</flux:select.option>
                            <flux:select.option value="other">Other</flux:select.option>
                        </flux:select>

                        <flux:input label="Admission Number" wire:model="admission_number" />
                        <flux:input label="Student Number" wire:model="student_number" />
                        <flux:select label="Enrollment Status" wire:model="enrollment_status">
                            @foreach (['active', 'expelled', 'graduated', 'suspended', 'transferred', 'withdrawn', 'dropped'] as $option)
                                <flux:select.option value="{{ $option }}">{{ ucfirst($option) }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <div class="md:col-span-2">
                            <flux:input type="file" label="Profile Photo" wire:model="profile_image"
                                accept="image/*"
                                description="{{ $student ? 'Leave empty to keep the current photo.' : 'Used on the biometric device.' }}" />
                            <div wire:loading wire:target="profile_image" class="mt-1 text-xs text-zinc-500">
                                Uploading…
                            </div>
                            @if ($profile_image)
                                <img src="{{ $profile_image->temporaryUrl() }}" alt="Preview"
                                    class="mt-2 size-20 rounded-md object-cover" />
                            @endif
                        </div>

                        <flux:checkbox wire:model="is_active" label="Active" />
                    </div>
                </div>

                <div>
                    <h5 class="text-md mb-3 font-semibold text-gray-800 dark:text-zinc-200">Address</h5>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div class="md:col-span-4">
                            <flux:textarea label="Address" rows="2" wire:model="address" />
                        </div>
                        <flux:input label="City" wire:model="city" />
                        <flux:input label="County / State" wire:model="state" />
                        <flux:input label="Country" wire:model="country" />
                    </div>
                </div>

                <div>
                    <h5 class="text-md mb-3 font-semibold text-gray-800 dark:text-zinc-200">Parent</h5>
                    @if (! $student)
                        <flux:select label="Link an existing parent" wire:model.live="parent_id" class="mb-4"
                            description="A parent can have more than one student. Pick one here, or fill in the fields below to create a new parent.">
                            <flux:select.option value="">Create from the details below</flux:select.option>
                            @foreach ($this->existingParents as $parent)
                                <flux:select.option value="{{ $parent->id }}">
                                    {{ $parent->name }} ({{ $parent->email }})
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    @endif

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <flux:input label="Parent Name" wire:model="parent_name" :disabled="$parent_id !== ''" />
                        <flux:input label="Parent Phone" wire:model="parent_phone" :disabled="$parent_id !== ''" />
                        <flux:input type="email" label="Parent Email" wire:model="parent_email"
                            :disabled="$parent_id !== ''" />
                        <flux:input label="Occupation" wire:model="parent_occupation"
                            :disabled="$parent_id !== ''" />
                    </div>
                </div>

                <div>
                    <h5 class="text-md mb-3 font-semibold text-gray-800 dark:text-zinc-200">Guardian</h5>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <flux:input label="Guardian Name" wire:model="guardian_name" />
                        <flux:input label="Guardian Phone" wire:model="guardian_phone" />
                        <flux:input type="email" label="Guardian Email" wire:model="guardian_email" />
                        <flux:input label="Relationship" wire:model="guardian_relationship" />
                    </div>
                </div>

                <div>
                    <h5 class="text-md mb-3 font-semibold text-gray-800 dark:text-zinc-200">Additional</h5>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <flux:textarea label="Medical Conditions" rows="2" wire:model="medical_conditions" />
                        <flux:textarea label="Allergies" rows="2" wire:model="allergies" />
                        <flux:textarea label="Special Needs" rows="2" wire:model="special_needs" />
                        <flux:textarea label="Notes" rows="2" wire:model="notes" />
                    </div>
                </div>
            </div>

            <div
                class="flex justify-end gap-3 rounded-b-lg border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:button href="{{ route('student.index') }}" variant="ghost" wire:navigate>Cancel</flux:button>
                <flux:button variant="primary" type="submit">
                    <span wire:loading.remove wire:target="save">
                        {{ $student ? 'Update Student' : 'Save Student' }}
                    </span>
                    <span wire:loading wire:target="save">Saving…</span>
                </flux:button>
            </div>
        </form>
    </div>
</div>
