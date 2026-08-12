<x-layouts::app :title="__('Staff Payment')">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-0">{{ $payment->staff?->name }}</h4>
                    <small class="text-sm text-gray-500">{{ $payment->period?->format('F Y') }} payroll</small>
                </div>
                <flux:badge
                    :color="match ($payment->status) {
                        'paid' => 'emerald',
                        'cancelled' => 'red',
                        default => 'amber',
                    }">
                    {{ ucfirst($payment->status) }}
                </flux:badge>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Gross Amount</p>
                        <p class="text-sm text-gray-900">{{ number_format($payment->gross_amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Allowances</p>
                        <p class="text-sm text-gray-900">{{ number_format($payment->allowances, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Deductions</p>
                        <p class="text-sm text-gray-900">{{ number_format($payment->deductions, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Net Pay</p>
                        <p class="text-sm font-semibold text-gray-900">{{ number_format($payment->net_amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Payment Method</p>
                        <p class="text-sm text-gray-900">
                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Reference</p>
                        <p class="text-sm text-gray-900">{{ $payment->reference ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Paid At</p>
                        <p class="text-sm text-gray-900">{{ $payment->paid_at?->format('d M Y H:i') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Recorded By</p>
                        <p class="text-sm text-gray-900">{{ $payment->recordedBy?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Institution</p>
                        <p class="text-sm text-gray-900">{{ $payment->institution?->name }}</p>
                    </div>
                </div>

                @if ($payment->notes)
                    <h5 class="text-md font-semibold text-gray-800 mb-2">Notes</h5>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $payment->notes }}</p>
                @endif
            </div>

            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                <a href="{{ route('staff.payments.index') }}"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Back to Payroll
                </a>
                @can('edit payroll')
                    <a href="{{ route('staff.payments.edit', $payment) }}"
                        class="px-4 py-2 bg-yellow-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-yellow-600">
                        Edit
                    </a>
                @endcan
                @can('delete payroll')
                    <form action="{{ route('staff.payments.destroy', $payment) }}" method="POST"
                        onsubmit="return confirm('Remove this payment record?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</x-layouts::app>
