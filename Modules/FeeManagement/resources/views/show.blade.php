<x-layouts::app :title="__('Fee Details')">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-0">{{ $fee->title }}</h4>
                    <small class="text-sm text-gray-500">{{ ucfirst($fee->fee_type) }} fee</small>
                </div>

                <flux:badge :color="match ($fee->status) {
                    'paid' => 'emerald',
                    'partial' => 'amber',
                    'overdue' => 'red',
                    default => 'zinc',
                }">
                    {{ ucfirst($fee->status) }}
                </flux:badge>
            </div>

            <div class="p-6">

                <h5 class="text-md font-semibold text-gray-800 mb-3">Associations</h5>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Student</p>
                        <p class="text-sm text-gray-900">{{ $fee->student?->name ?? '—' }}</p>
                        @if ($fee->student?->studentUserDetails?->admission_number)
                            <p class="text-xs text-gray-500">
                                Admission No. {{ $fee->student->studentUserDetails->admission_number }}
                            </p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Institution</p>
                        <p class="text-sm text-gray-900">{{ $fee->institution?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Parent/Guardian</p>
                        <p class="text-sm text-gray-900">{{ $fee->parent?->name ?? 'Not linked' }}</p>
                        @if ($fee->parent?->email)
                            <p class="text-xs text-gray-500">{{ $fee->parent->email }}</p>
                        @endif
                    </div>
                </div>

                <h5 class="text-md font-semibold text-gray-800 mb-3">Fee Details</h5>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Amount</p>
                        <p class="text-sm text-gray-900">{{ number_format($fee->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Amount Paid</p>
                        <p class="text-sm text-gray-900">{{ number_format($fee->amount_paid, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Balance</p>
                        <p class="text-sm text-gray-900">{{ number_format($fee->balance, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Due Date</p>
                        <p class="text-sm text-gray-900">{{ $fee->due_date?->format('d M Y') ?? '—' }}</p>
                    </div>
                </div>

                @if ($fee->notes)
                    <h5 class="text-md font-semibold text-gray-800 mb-3">Notes</h5>
                    <p class="text-sm text-gray-700 whitespace-pre-line mb-6">{{ $fee->notes }}</p>
                @endif

            </div>

            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">

                <a href="{{ route('feemanagement.index') }}"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" wire:navigate>
                    Back to List
                </a>

                @can('edit feemanagement')
                    <a href="{{ route('feemanagement.edit', $fee->id) }}"
                        class="px-4 py-2 bg-yellow-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" wire:navigate>
                        Edit
                    </a>
                @endcan

                @can('delete feemanagement')
                    <form action="{{ route('feemanagement.destroy', $fee->id) }}" method="POST"
                        onsubmit="return confirm('Remove this fee record?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Delete
                        </button>
                    </form>
                @endcan

            </div>

        </div>

    </div>
</x-layouts::app>
