{{-- Shared by the create and edit screens: $expenditure is null when recording a new spend. --}}
<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="p-6">
        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
            <flux:input type="text" name="title" label="Item" value="{{ old('title', $expenditure?->title) }}"
                placeholder="e.g. Term 2 electricity bill" required />

            <flux:select name="expenditure_category_id" label="Category">
                <flux:select.option value="">Uncategorised</flux:select.option>
                @foreach ($categories as $category)
                    <flux:select.option value="{{ $category->id }}"
                        :selected="old('expenditure_category_id', $expenditure?->expenditure_category_id) == $category->id">
                        {{ $category->name }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:input type="text" name="payee" label="Paid To" value="{{ old('payee', $expenditure?->payee) }}"
                placeholder="e.g. Kenya Power" />

            <flux:input type="number" step="0.01" min="0" name="amount" label="Amount"
                value="{{ old('amount', $expenditure?->amount) }}" required />

            <flux:input type="date" name="spent_on" label="Date"
                value="{{ old('spent_on', $expenditure?->spent_on?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                required />

            <flux:select name="payment_method" label="Payment Method">
                @foreach (\Modules\Expenditure\Models\Expenditure::PAYMENT_METHODS as $paymentMethod)
                    <flux:select.option value="{{ $paymentMethod }}"
                        :selected="old('payment_method', $expenditure?->payment_method ?? 'cash') === $paymentMethod">
                        {{ ucfirst(str_replace('_', ' ', $paymentMethod)) }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:input type="text" name="reference" label="Reference"
                value="{{ old('reference', $expenditure?->reference) }}"
                description="Receipt, cheque or transaction number." />

            <flux:select name="status" label="Status">
                @foreach (\Modules\Expenditure\Models\Expenditure::STATUSES as $status)
                    <flux:select.option value="{{ $status }}"
                        :selected="old('status', $expenditure?->status ?? 'pending') === $status">
                        {{ ucfirst($status) }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <div class="mb-2 grid grid-cols-1 gap-4">
            <flux:textarea name="notes" rows="2" label="Notes">{{ old('notes', $expenditure?->notes) }}</flux:textarea>
        </div>

        <p class="text-sm text-gray-500">
            Marking a spend as paid stamps the date the money left the school.
        </p>
    </div>

    <div class="flex justify-end gap-3 rounded-b-lg border-t border-gray-200 bg-gray-50 px-6 py-4">
        <a href="{{ route('expenditure.index') }}"
            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Cancel
        </a>
        <flux:button variant="primary" type="submit">{{ $submitLabel }}</flux:button>
    </div>
</form>
