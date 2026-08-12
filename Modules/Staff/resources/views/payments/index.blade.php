<x-layouts::app :title="__('Payroll')">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <flux:card class="space-y-1">
                <flux:text class="text-zinc-500">Total Payroll</flux:text>
                <flux:heading size="xl">{{ number_format($totalPayroll, 2) }}</flux:heading>
            </flux:card>
            <flux:card class="space-y-1">
                <flux:text class="text-zinc-500">Paid Out</flux:text>
                <flux:heading size="xl">{{ number_format($totalPaid, 2) }}</flux:heading>
            </flux:card>
            <flux:card class="space-y-1">
                <flux:text class="text-zinc-500">Outstanding</flux:text>
                <flux:heading size="xl">{{ number_format($totalPayroll - $totalPaid, 2) }}</flux:heading>
            </flux:card>
        </div>

        <div class="mb-2 flex flex-row justify-between gap-3">
            @can('create payroll')
                <flux:button href="{{ route('staff.payments.create') }}">Record Payment</flux:button>
            @endcan

            <form action="{{ route('staff.payments.index') }}" method="GET" class="flex items-end gap-2">
                <flux:input type="month" name="period" label="Period" value="{{ request('period') }}" />
                <flux:select name="status" label="Status">
                    <flux:select.option value="">All</flux:select.option>
                    @foreach (['pending', 'paid', 'cancelled'] as $status)
                        <flux:select.option value="{{ $status }}" :selected="request('status') === $status">
                            {{ ucfirst($status) }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:button type="submit" icon="funnel" variant="ghost">Filter</flux:button>
            </form>
        </div>

        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Staff</flux:table.column>
                    <x-sortable-column column="period">Period</x-sortable-column>
                    <flux:table.column>Gross</flux:table.column>
                    <flux:table.column>Allowances</flux:table.column>
                    <flux:table.column>Deductions</flux:table.column>
                    <x-sortable-column column="net_amount">Net</x-sortable-column>
                    <flux:table.column>Method</flux:table.column>
                    <x-sortable-column column="status">Status</x-sortable-column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($payments as $payment)
                        <flux:table.row>
                            <flux:table.cell>
                                {{ $payment->staff?->name }}
                                @if ($payment->staff?->job_title)
                                    <flux:text class="text-zinc-500 text-xs">{{ $payment->staff->job_title }}
                                    </flux:text>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>{{ $payment->period?->format('M Y') }}</flux:table.cell>
                            <flux:table.cell>{{ number_format($payment->gross_amount, 2) }}</flux:table.cell>
                            <flux:table.cell>{{ number_format($payment->allowances, 2) }}</flux:table.cell>
                            <flux:table.cell>{{ number_format($payment->deductions, 2) }}</flux:table.cell>
                            <flux:table.cell>{{ number_format($payment->net_amount, 2) }}</flux:table.cell>
                            <flux:table.cell>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge
                                    :color="match ($payment->status) {
                                        'paid' => 'emerald',
                                        'cancelled' => 'red',
                                        default => 'amber',
                                    }">
                                    {{ ucfirst($payment->status) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button href="{{ route('staff.payments.show', $payment) }}" icon="eye"
                                    variant="primary" color="emerald">
                                    view
                                </flux:button>
                                @can('edit payroll')
                                    <flux:button href="{{ route('staff.payments.edit', $payment) }}" icon="pencil"
                                        variant="primary" color="yellow">
                                        edit
                                    </flux:button>
                                @endcan
                                @can('delete payroll')
                                    <form action="{{ route('staff.payments.destroy', $payment) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Remove this payment record?');">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" icon="trash" variant="primary" color="red">
                                            delete
                                        </flux:button>
                                    </form>
                                @endcan
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="9" class="text-center text-gray-500">
                                No staff payments found.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
            <div class="mt-4">
                {{ $payments->links() }}
            </div>
        </flux:card>
    </div>
</x-layouts::app>
