<?php

use App\Models\Plan;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Institution\Actions\SaveInstitution;
use Modules\Institution\Models\Institution;

new #[Title('Institution')] class extends Component
{
    use WithFileUploads;

    public ?Institution $institution = null;

    // Basics
    public string $name = '';

    public string $code = '';

    public string $type = 'School';

    public string $education_level = '';

    public string $timezone = 'Africa/Nairobi';

    public $logo = null;

    // Contact
    public string $email = '';

    public string $phone = '';

    public string $alternate_phone = '';

    public string $website = '';

    // Address
    public string $country = 'Kenya';

    public string $county = '';

    public string $city = '';

    public string $postal_address = '';

    public string $physical_address = '';

    // Administration
    public string $principal_name = '';

    public string $principal_phone = '';

    // Academic
    public string $min_electives = '0';

    public string $max_electives = '';

    // Subscription / status
    public string $subscription_plan = '';

    public string $subscription_expires_at = '';

    public string $notes = '';

    public bool $is_active = true;

    public function mount(?int $institutionId = null): void
    {
        if ($institutionId === null) {
            // Registering a school is the self-service onboarding flow: any
            // authenticated user may reach it (this is how a Director
            // account comes into being). Admins own the platform, not a
            // school, and never create one.
            abort_if(isAdmin(), 403);

            return;
        }

        $this->institution = Institution::findOrFail($institutionId);

        abort_unless(
            auth()->user()->can('edit institution') && (isAdmin() || $this->institution->user_id === auth()->id()),
            403
        );

        $this->fill([
            'name' => (string) $this->institution->name,
            'code' => (string) $this->institution->code,
            'type' => (string) ($this->institution->type ?: 'School'),
            'education_level' => (string) $this->institution->education_level,
            'timezone' => (string) ($this->institution->timezone ?: 'Africa/Nairobi'),
            'email' => (string) $this->institution->email,
            'phone' => (string) $this->institution->phone,
            'alternate_phone' => (string) $this->institution->alternate_phone,
            'website' => (string) $this->institution->website,
            'country' => (string) $this->institution->country,
            'county' => (string) $this->institution->county,
            'city' => (string) $this->institution->city,
            'postal_address' => (string) $this->institution->postal_address,
            'physical_address' => (string) $this->institution->physical_address,
            'principal_name' => (string) $this->institution->principal_name,
            'principal_phone' => (string) $this->institution->principal_phone,
            'min_electives' => (string) ($this->institution->min_electives ?? 0),
            'max_electives' => (string) ($this->institution->max_electives ?? ''),
            'subscription_plan' => (string) ($this->institution->subscription_plan ?? ''),
            'subscription_expires_at' => $this->institution->subscription_expires_at?->format('Y-m-d') ?? '',
            'notes' => (string) $this->institution->notes,
            'is_active' => (bool) $this->institution->is_active,
        ]);
    }

    public function save(): void
    {
        $saver = app(SaveInstitution::class);

        if ($this->institution) {
            abort_unless(
                auth()->user()->can('update institution') && (isAdmin() || $this->institution->user_id === auth()->id()),
                403
            );

            $validated = $this->validate(SaveInstitution::updateRules($this->institution));

            $saver->update($this->institution, $validated, $this->logo);

            session()->flash('success', 'Institution "'.$this->institution->name.'" has been updated successfully.');

            $this->redirectRoute('institution.edit', $this->institution->id, navigate: true);

            return;
        }

        abort_if(isAdmin(), 403);

        $validated = $this->validate(SaveInstitution::createRules());

        $saver->create($validated, auth()->user(), $this->logo);

        session()->flash('success', 'Institution created successfully! It will be reviewed by an Admin before it becomes fully active.');

        $this->redirectRoute('institution.index', navigate: true);
    }

    protected function prepareForValidation($attributes)
    {
        foreach ($attributes as $key => $value) {
            if ($value === '' && ! in_array($key, ['name', 'code', 'type', 'email', 'phone'], true)) {
                $attributes[$key] = null;
            }
        }

        // The upload itself is validated through the $logo property, not as
        // part of the persisted attributes.
        $attributes['logo'] = $this->logo;

        return $attributes;
    }

    /**
     * @return Collection<int, Plan>
     */
    #[Computed]
    public function plans(): Collection
    {
        return Plan::where('is_active', true)->orderBy('name')->get();
    }
}; ?>

<div class="p-4">
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div
            class="rounded-t-lg border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
            <h4 class="mb-0 text-lg font-semibold text-gray-900 dark:text-white">
                {{ $institution ? 'Institution Settings' : 'Register Your School' }}
            </h4>
        </div>

        <form wire:submit="save">
            <div class="space-y-8 p-6">
                <div>
                    <h5 class="text-md mb-3 font-semibold text-gray-800 dark:text-zinc-200">Basics</h5>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <flux:input label="Name" wire:model="name" />
                        <flux:input label="Code" wire:model="code" placeholder="e.g. SCH-001" />
                        <flux:select label="Type" wire:model="type">
                            @foreach (['School', 'College', 'University', 'Training Centre'] as $option)
                                <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        @if ($institution)
                            <flux:input label="Education Level" wire:model="education_level"
                                placeholder="e.g. Secondary" />
                            <flux:input label="Timezone" wire:model="timezone"
                                description="Used when stamping device clocks." />
                        @endif

                        <div>
                            <flux:input type="file" label="Logo" wire:model="logo" accept="image/*"
                                description="{{ $institution ? 'Leave empty to keep the current logo.' : 'Optional.' }}" />
                            <div wire:loading wire:target="logo" class="mt-1 text-xs text-zinc-500">Uploading…</div>
                            @if ($logo)
                                <img src="{{ $logo->temporaryUrl() }}" alt="Preview"
                                    class="mt-2 size-20 rounded-md object-contain" />
                            @elseif ($institution?->logo)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($institution->logo) }}"
                                    alt="{{ $institution->name }}" class="mt-2 size-20 rounded-md object-contain" />
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <h5 class="text-md mb-3 font-semibold text-gray-800 dark:text-zinc-200">Contact</h5>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <flux:input type="email" label="Email" wire:model="email" />
                        <flux:input label="Phone" wire:model="phone" />
                        <flux:input label="Alternate Phone" wire:model="alternate_phone" />
                        <flux:input label="Website" wire:model="website" placeholder="https://…" />
                    </div>
                </div>

                <div>
                    <h5 class="text-md mb-3 font-semibold text-gray-800 dark:text-zinc-200">Address</h5>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <flux:input label="Country" wire:model="country" />
                        <flux:input label="County" wire:model="county" />
                        <flux:input label="City / Town" wire:model="city" />
                        <flux:input label="Postal Address" wire:model="postal_address" />
                        <div class="md:col-span-2">
                            <flux:textarea label="Physical Address" rows="2" wire:model="physical_address" />
                        </div>
                    </div>
                </div>

                @if ($institution)
                    <div>
                        <h5 class="text-md mb-3 font-semibold text-gray-800 dark:text-zinc-200">Administration</h5>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <flux:input label="Principal Name" wire:model="principal_name" />
                            <flux:input label="Principal Phone" wire:model="principal_phone" />
                            <flux:input type="number" min="0" label="Min Electives" wire:model="min_electives"
                                description="How many electives a student must pick." />
                            <flux:input type="number" min="0" label="Max Electives" wire:model="max_electives"
                                description="Leave empty for no upper limit." />
                        </div>
                    </div>

                    <div>
                        <h5 class="text-md mb-3 font-semibold text-gray-800 dark:text-zinc-200">Subscription</h5>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <flux:select label="Plan" wire:model="subscription_plan">
                                <flux:select.option value="">No plan</flux:select.option>
                                @foreach ($this->plans as $plan)
                                    <flux:select.option value="{{ $plan->id }}">{{ $plan->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:input type="date" label="Expires At" wire:model="subscription_expires_at" />
                            <flux:checkbox wire:model="is_active" label="Active" />
                        </div>
                    </div>

                    <flux:textarea label="Notes" rows="2" wire:model="notes" />
                @endif
            </div>

            <div
                class="flex justify-end gap-3 rounded-b-lg border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:button href="{{ route('institution.index') }}" variant="ghost" wire:navigate>Cancel</flux:button>
                <flux:button variant="primary" type="submit">
                    <span wire:loading.remove wire:target="save">
                        {{ $institution ? 'Save Changes' : 'Register School' }}
                    </span>
                    <span wire:loading wire:target="save">Saving…</span>
                </flux:button>
            </div>
        </form>
    </div>
</div>
