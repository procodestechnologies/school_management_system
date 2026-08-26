<x-layouts::app :title="__('Staff Details')">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-0">{{ $staff->name }}</h4>
                    <small class="text-sm text-gray-500">{{ $staff->job_title ?? 'Staff' }}</small>
                </div>
                <flux:badge :color="$staff->is_active ? 'emerald' : 'red'">
                    {{ ucfirst(str_replace('_', ' ', $staff->status)) }}
                </flux:badge>
            </div>

            <div class="p-6">
                <h5 class="text-md font-semibold text-gray-800 mb-3">Employment Details</h5>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Institution</p>
                        <p class="text-sm text-gray-900">{{ $staff->institution?->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Staff Number</p>
                        <p class="text-sm text-gray-900">{{ $staff->staff_number ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Department</p>
                        <p class="text-sm text-gray-900">{{ $staff->department ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Employment Type</p>
                        <p class="text-sm text-gray-900">
                            {{ ucfirst(str_replace('_', ' ', $staff->employment_type)) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Hire Date</p>
                        <p class="text-sm text-gray-900">{{ $staff->hire_date?->format('d M Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Monthly Salary</p>
                        <p class="text-sm text-gray-900">
                            {{ $staff->salary ? number_format($staff->salary, 2) : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Phone</p>
                        <p class="text-sm text-gray-900">{{ $staff->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Email</p>
                        <p class="text-sm text-gray-900">{{ $staff->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">System Access</p>
                        <p class="text-sm text-gray-900">
                            {{ $staff->user ? $staff->user->getRoleNames()->first() ?? 'Login, no role' : 'No login' }}
                        </p>
                    </div>
                </div>

                @if ($staff->address)
                    <h5 class="text-md font-semibold text-gray-800 mb-2">Address</h5>
                    <p class="text-sm text-gray-700 mb-6">{{ $staff->address }}</p>
                @endif

                @if ($staff->notes)
                    <h5 class="text-md font-semibold text-gray-800 mb-2">Notes</h5>
                    <p class="text-sm text-gray-700 whitespace-pre-line mb-6">{{ $staff->notes }}</p>
                @endif

                @can('view payroll')
                    <div class="flex items-center justify-between mb-3">
                        <h5 class="text-md font-semibold text-gray-800 mb-0">Recent Payments</h5>
                        @can('create payroll')
                            <flux:button href="{{ route('staff.payments.create') }}" icon="plus" size="sm"
                                variant="ghost" wire:navigate>
                                Record Payment
                            </flux:button>
                        @endcan
                    </div>
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Period</flux:table.column>
                            <flux:table.column>Gross</flux:table.column>
                            <flux:table.column>Net</flux:table.column>
                            <flux:table.column>Status</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @forelse ($payments as $payment)
                                <flux:table.row>
                                    <flux:table.cell>{{ $payment->period?->format('F Y') }}</flux:table.cell>
                                    <flux:table.cell>{{ number_format($payment->gross_amount, 2) }}</flux:table.cell>
                                    <flux:table.cell>{{ number_format($payment->net_amount, 2) }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge size="sm"
                                            :color="match ($payment->status) {
                                                'paid' => 'emerald',
                                                'cancelled' => 'red',
                                                default => 'amber',
                                            }">
                                            {{ ucfirst($payment->status) }}
                                        </flux:badge>
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="4" class="text-center text-gray-500">
                                        No payments recorded yet.
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                @endcan
            </div>

            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                <a href="{{ route('staff.index') }}"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50" wire:navigate>
                    Back to List
                </a>
                @can('edit staff')
                    <a href="{{ route('staff.edit', $staff) }}"
                        class="px-4 py-2 bg-yellow-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-yellow-600" wire:navigate>
                        Edit
                    </a>
                @endcan
                @can('delete staff')
                    <form action="{{ route('staff.destroy', $staff) }}" method="POST"
                        onsubmit="return confirm('Remove this staff member?');">
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
