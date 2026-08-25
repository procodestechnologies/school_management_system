<?php

use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\FeeManagement\Actions\SaveFee;
use Modules\FeeManagement\Models\Fee;
use Modules\Student\Models\StudentDetails;

new #[Title('Fee')] class extends Component
{
    public ?Fee $fee = null;

    public string $student_id = '';

    public string $title = '';

    public string $fee_type = 'tuition';

    public string $amount = '';

    public string $amount_paid = '0';

    public string $due_date = '';

    public string $notes = '';

    public function mount(?int $feeId = null): void
    {
        if ($feeId === null) {
            abort_unless(auth()->user()->can('create feemanagement'), 403);

            return;
        }

        abort_unless(auth()->user()->can('edit feemanagement'), 403);

        $this->fee = $this->scoped()->findOrFail($feeId);

        $this->fill([
            'student_id' => (string) $this->fee->student_id,
            'title' => (string) $this->fee->title,
            'fee_type' => (string) $this->fee->fee_type,
            'amount' => (string) $this->fee->amount,
            'amount_paid' => (string) $this->fee->amount_paid,
            'due_date' => $this->fee->due_date?->format('Y-m-d') ?? '',
            'notes' => (string) $this->fee->notes,
        ]);
    }

    public function save(): void
    {
        abort_unless(
            auth()->user()->can($this->fee ? 'edit feemanagement' : 'create feemanagement'),
            403
        );

        $validated = $this->validate(SaveFee::rules(withStudent: $this->fee === null));

        // A fee can only be raised against a student the viewer can actually
        // see - 'exists' alone would reach another school's roll.
        if (! $this->fee) {
            abort_unless(
                $this->students->contains(fn ($details) => (string) $details->student_id === $this->student_id),
                403
            );
        }

        $saved = SaveFee::handle($validated, $this->fee);

        session()->flash('success', $this->fee ? 'Fee updated successfully!' : 'Fee created successfully!');

        $this->redirectRoute('feemanagement.show', $saved->id, navigate: true);
    }

    protected function prepareForValidation($attributes)
    {
        foreach (['due_date', 'notes'] as $field) {
            if (($attributes[$field] ?? '') === '') {
                $attributes[$field] = null;
            }
        }

        return $attributes;
    }

    /**
     * @return Collection<int, StudentDetails>
     */
    #[Computed]
    public function students(): Collection
    {
        $query = StudentDetails::with(['student', 'institution']);

        if (! isAdmin()) {
            $query->where('institution_id', currentInstitution()?->id ?? 0);
        }

        return $query->get()->sortBy(fn ($details) => $details->student?->name)->values();
    }

    private function scoped()
    {
        $query = Fee::query();

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
                {{ $fee ? 'Edit Fee' : 'Add Fee' }}
            </h4>
        </div>

        <form wire:submit="save">
            <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-3">
                @if ($fee)
                    <div class="md:col-span-3">
                        <flux:text class="text-zinc-500">
                            Billed to <span class="font-medium">{{ $fee->student?->name }}</span>. A fee stays with
                            the student it was raised against.
                        </flux:text>
                    </div>
                @else
                    <div class="md:col-span-3">
                        <flux:select label="Student" wire:model="student_id">
                            <flux:select.option value="">Select Student</flux:select.option>
                            @foreach ($this->students as $details)
                                <flux:select.option value="{{ $details->student_id }}">
                                    {{ $details->student?->name }}
                                    @if ($details->admission_number)
                                        ({{ $details->admission_number }})
                                    @endif
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                @endif

                <flux:input label="Title" wire:model="title" placeholder="e.g. Term 2 Tuition" />

                <flux:select label="Fee Type" wire:model="fee_type">
                    @foreach (['tuition', 'boarding', 'transport', 'lunch', 'activity', 'exam', 'uniform', 'other'] as $type)
                        <flux:select.option value="{{ $type }}">{{ ucfirst($type) }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input type="date" label="Due Date" wire:model="due_date" />

                <flux:input type="number" step="0.01" min="0" label="Amount" wire:model="amount" />
                <flux:input type="number" step="0.01" min="0" label="Amount Paid" wire:model="amount_paid"
                    description="Balance and status are worked out from these two." />

                <div class="md:col-span-3">
                    <flux:textarea label="Notes" rows="2" wire:model="notes" />
                </div>
            </div>

            <div
                class="flex justify-end gap-3 rounded-b-lg border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:button href="{{ route('feemanagement.index') }}" variant="ghost" wire:navigate>Cancel
                </flux:button>
                <flux:button variant="primary" type="submit">
                    <span wire:loading.remove wire:target="save">{{ $fee ? 'Update Fee' : 'Save Fee' }}</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </flux:button>
            </div>
        </form>
    </div>
</div>
