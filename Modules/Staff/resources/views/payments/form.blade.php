{{-- Shared by the create and edit screens: $payment is null when recording a new payment. --}}
<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="p-6">
        <h5 class="text-md font-semibold text-gray-800 mb-3">Payslip</h5>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <flux:select name="staff_details_id" label="Staff Member" required>
                <flux:select.option value="">Select Staff Member</flux:select.option>
                @foreach ($staffMembers as $member)
                    <flux:select.option value="{{ $member->id }}"
                        :selected="old('staff_details_id', $payment?->staff_details_id) == $member->id">
                        {{ $member->name }}{{ $member->job_title ? ' — '.$member->job_title : '' }}
                    </flux:select.option>
                @endforeach
            </flux:select>
            <flux:input type="month" name="period" label="Period"
                value="{{ old('period', $payment?->period?->format('Y-m') ?? now()->format('Y-m')) }}" required />
            <flux:input type="number" step="0.01" min="0" name="gross_amount" label="Gross Amount"
                value="{{ old('gross_amount', $payment?->gross_amount) }}" required />
            <flux:input type="number" step="0.01" min="0" name="allowances" label="Allowances"
                value="{{ old('allowances', $payment?->allowances ?? '0.00') }}" />
            <flux:input type="number" step="0.01" min="0" name="deductions" label="Deductions"
                value="{{ old('deductions', $payment?->deductions ?? '0.00') }}"
                description="Tax, loans, advances." />
            <flux:select name="payment_method" label="Payment Method">
                @foreach (['bank_transfer', 'cash', 'mobile_money', 'cheque'] as $paymentMethod)
                    <flux:select.option value="{{ $paymentMethod }}"
                        :selected="old('payment_method', $payment?->payment_method ?? 'bank_transfer') === $paymentMethod">
                        {{ ucfirst(str_replace('_', ' ', $paymentMethod)) }}
                    </flux:select.option>
                @endforeach
            </flux:select>
            <flux:input type="text" name="reference" label="Reference"
                value="{{ old('reference', $payment?->reference) }}" description="Cheque or transaction number." />
            <flux:select name="status" label="Status">
                @foreach (['pending', 'paid', 'cancelled'] as $status)
                    <flux:select.option value="{{ $status }}"
                        :selected="old('status', $payment?->status ?? 'pending') === $status">
                        {{ ucfirst($status) }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <div class="grid grid-cols-1 gap-4 mb-6">
            <flux:textarea name="notes" rows="2"
                label="Notes">{{ old('notes', $payment?->notes) }}</flux:textarea>
        </div>

        <p class="text-sm text-gray-500">
            Net pay is worked out for you: gross + allowances − deductions.
        </p>
    </div>

    <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
        <a href="{{ route('staff.payments.index') }}"
            class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
            Cancel
        </a>
        <flux:button variant="primary" type="submit">{{ $submitLabel }}</flux:button>
    </div>
</form>
