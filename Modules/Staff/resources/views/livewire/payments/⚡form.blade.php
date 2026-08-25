<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\Staff\Actions\SaveStaffPayment;
use Modules\Staff\Models\StaffDetails;
use Modules\Staff\Models\StaffPayment;

new #[Title('Staff Payment')] class extends Component
{
    public ?StaffPayment $payment = null;

    public string $staff_details_id = '';

    public string $period = '';

    public string $gross_amount = '';

    public string $allowances = '0';

    public string $deductions = '0';

    public string $payment_method = 'bank_transfer';

    public string $reference = '';

    public string $status = 'pending';

    public string $notes = '';

    public function mount(?int $paymentId = null): void
    {
        if ($paymentId === null) {
            abort_unless(auth()->user()->can('create payroll'), 403);

            $this->period = now()->format('Y-m');

            return;
        }

        abort_unless(auth()->user()->can('edit payroll'), 403);

        $this->payment = $this->scoped()->findOrFail($paymentId);

        $this->fill([
            'staff_details_id' => (string) $this->payment->staff_details_id,
            'period' => $this->payment->period?->format('Y-m') ?? now()->format('Y-m'),
            'gross_amount' => (string) $this->payment->gross_amount,
            'allowances' => (string) $this->payment->allowances,
            'deductions' => (string) $this->payment->deductions,
            'payment_method' => (string) $this->payment->payment_method,
            'reference' => (string) $this->payment->reference,
            'status' => (string) $this->payment->status,
            'notes' => (string) $this->payment->notes,
        ]);
    }

    /**
     * Take-home pay, shown as it's typed rather than only after saving.
     */
    #[Computed]
    public function net(): float
    {
        return StaffPayment::calculateNet(
            (float) ($this->gross_amount ?: 0),
            (float) ($this->allowances ?: 0),
            (float) ($this->deductions ?: 0),
        );
    }

    public function save(): void
    {
        abort_unless(
            auth()->user()->can($this->payment ? 'edit payroll' : 'create payroll'),
            403
        );

        $validated = $this->validate(SaveStaffPayment::rules());

        // 'exists' alone would let a crafted request pay another school's
        // staff.
        $staff = StaffDetails::findOrFail($validated['staff_details_id']);

        if (! isAdmin()) {
            abort_unless($staff->institution_id === currentInstitution()?->id, 403);
        }

        $period = Carbon::parse($validated['period'].'-01')->startOfMonth();

        if (SaveStaffPayment::periodAlreadyRecorded($staff, $period, $this->payment)) {
            $this->addError('period', 'A payment for '.$staff->name.' in '.$period->format('F Y').' already exists.');

            return;
        }

        $saved = SaveStaffPayment::handle($validated, $staff, $this->payment, recordedBy: auth()->id());

        session()->flash('success', $this->payment ? 'Staff payment updated successfully!' : 'Staff payment recorded successfully!');

        $this->redirectRoute('staff.payments.show', $saved->id, navigate: true);
    }

    protected function prepareForValidation($attributes)
    {
        foreach (['reference', 'notes'] as $field) {
            if (($attributes[$field] ?? '') === '') {
                $attributes[$field] = null;
            }
        }

        return $attributes;
    }

    /**
     * @return Collection<int, StaffDetails>
     */
    #[Computed]
    public function staffMembers(): Collection
    {
        $query = StaffDetails::query()->orderBy('name');

        if (! isAdmin()) {
            $query->where('institution_id', currentInstitution()?->id ?? 0);
        }

        return $query->get();
    }

    private function scoped()
    {
        $query = StaffPayment::query();

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
                {{ $payment ? 'Edit Staff Payment' : 'Record Staff Payment' }}
            </h4>
        </div>

        <form wire:submit="save">
            <div class="p-6">
                <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <flux:select label="Staff Member" wire:model="staff_details_id">
                        <flux:select.option value="">Select Staff Member</flux:select.option>
                        @foreach ($this->staffMembers as $member)
                            <flux:select.option value="{{ $member->id }}">
                                {{ $member->name }}{{ $member->job_title ? ' — '.$member->job_title : '' }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input type="month" label="Period" wire:model="period" />
                    <flux:input type="number" step="0.01" min="0" label="Gross Amount"
                        wire:model.live.debounce.500ms="gross_amount" />
                    <flux:input type="number" step="0.01" min="0" label="Allowances"
                        wire:model.live.debounce.500ms="allowances" />
                    <flux:input type="number" step="0.01" min="0" label="Deductions"
                        wire:model.live.debounce.500ms="deductions" description="Tax, loans, advances." />

                    <flux:select label="Payment Method" wire:model="payment_method">
                        @foreach (['bank_transfer', 'cash', 'mobile_money', 'cheque'] as $method)
                            <flux:select.option value="{{ $method }}">
                                {{ ucfirst(str_replace('_', ' ', $method)) }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input label="Reference" wire:model="reference"
                        description="Cheque or transaction number." />

                    <flux:select label="Status" wire:model="status">
                        @foreach (['pending', 'paid', 'cancelled'] as $option)
                            <flux:select.option value="{{ $option }}">{{ ucfirst($option) }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="mb-6 grid grid-cols-1 gap-4">
                    <flux:textarea label="Notes" rows="2" wire:model="notes" />
                </div>

                <div class="rounded-md border border-zinc-200 px-4 py-3 dark:border-zinc-700">
                    <flux:text class="text-zinc-500">Net pay (gross + allowances − deductions)</flux:text>
                    <flux:heading size="lg">{{ number_format($this->net, 2) }}</flux:heading>
                </div>
            </div>

            <div
                class="flex justify-end gap-3 rounded-b-lg border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800">
                <flux:button href="{{ route('staff.payments.index') }}" variant="ghost" wire:navigate>Cancel
                </flux:button>
                <flux:button variant="primary" type="submit">
                    <span wire:loading.remove wire:target="save">
                        {{ $payment ? 'Update Payment' : 'Save Payment' }}
                    </span>
                    <span wire:loading wire:target="save">Saving…</span>
                </flux:button>
            </div>
        </form>
    </div>
</div>
