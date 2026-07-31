<x-layouts::app :title="__(config('feemanagement.name'))">
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

        <div class="mb-2 flex flex-row justify-between">
            @can('create feemanagement')
                <flux:button href="{{ route('feemanagement.create') }}">Add Fee</flux:button>
            @endcan
        </div>

        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Student</flux:table.column>
                    <flux:table.column>Institution</flux:table.column>
                    <flux:table.column>Title</flux:table.column>
                    <flux:table.column>Amount</flux:table.column>
                    <flux:table.column>Paid</flux:table.column>
                    <flux:table.column>Balance</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Due Date</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($fees as $fee)
                        <flux:table.row>
                            <flux:table.cell>
                                {{ $fee->student?->name }}
                                @if ($fee->student?->studentUserDetails?->admission_number)
                                    <flux:text class="text-zinc-500 text-xs">
                                        {{ $fee->student->studentUserDetails->admission_number }}
                                    </flux:text>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>{{ $fee->institution?->name }}</flux:table.cell>
                            <flux:table.cell>
                                {{ $fee->title }}
                                <flux:text class="text-zinc-500 text-xs">{{ ucfirst($fee->fee_type) }}</flux:text>
                            </flux:table.cell>
                            <flux:table.cell>{{ number_format($fee->amount, 2) }}</flux:table.cell>
                            <flux:table.cell>{{ number_format($fee->amount_paid, 2) }}</flux:table.cell>
                            <flux:table.cell>{{ number_format($fee->balance, 2) }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="match ($fee->status) {
                                    'paid' => 'emerald',
                                    'partial' => 'amber',
                                    'overdue' => 'red',
                                    default => 'zinc',
                                }">
                                    {{ ucfirst($fee->status) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $fee->due_date?->format('d M Y') ?? '—' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:button href="{{ route('feemanagement.show', $fee->id) }}" icon="eye"
                                    variant="primary" color="emerald">
                                    view
                                </flux:button>
                                @can('edit feemanagement')
                                    <flux:button href="{{ route('feemanagement.edit', $fee->id) }}" icon="pencil"
                                        variant="primary" color="yellow">
                                        edit
                                    </flux:button>
                                @endcan
                                @can('delete feemanagement')
                                    <form action="{{ route('feemanagement.destroy', $fee->id) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Remove this fee record?');">
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
                                No fee records found.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</x-layouts::app>
