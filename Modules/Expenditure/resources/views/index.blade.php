<x-layouts::app :title="__('Expenditure')">
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
                <flux:text class="text-zinc-500">Total Recorded</flux:text>
                <flux:heading size="xl">{{ number_format($totalSpend, 2) }}</flux:heading>
            </flux:card>
            <flux:card class="space-y-1">
                <flux:text class="text-zinc-500">Paid Out</flux:text>
                <flux:heading size="xl">{{ number_format($totalSettled, 2) }}</flux:heading>
            </flux:card>
            <flux:card class="space-y-1">
                <flux:text class="text-zinc-500">Not Yet Paid</flux:text>
                <flux:heading size="xl">{{ number_format($totalSpend - $totalSettled, 2) }}</flux:heading>
            </flux:card>
        </div>

        @if ($byCategory->isNotEmpty())
            <flux:card class="mb-4">
                <flux:heading class="mb-3">Where the money went</flux:heading>
                <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                    @foreach ($byCategory as $row)
                        <div class="rounded-md border border-zinc-200 px-3 py-2 dark:border-zinc-700">
                            <flux:text class="text-xs text-zinc-500">
                                {{ $row->category?->name ?? 'Uncategorised' }}
                            </flux:text>
                            <flux:heading size="lg">{{ number_format((float) $row->total, 2) }}</flux:heading>
                        </div>
                    @endforeach
                </div>
            </flux:card>
        @endif

        <div class="mb-2 flex flex-row flex-wrap items-end justify-between gap-3">
            <div class="flex gap-2">
                @can('create expenditure')
                    <flux:button href="{{ route('expenditure.create') }}" icon="plus">Record Expenditure</flux:button>
                @endcan
                <flux:button href="{{ route('expenditure.categories.index') }}" icon="tag" variant="ghost">
                    Categories
                </flux:button>
            </div>

            <form action="{{ route('expenditure.index') }}" method="GET" class="flex flex-wrap items-end gap-2">
                <flux:select name="category_id" label="Category">
                    <flux:select.option value="">All</flux:select.option>
                    @foreach ($categories as $category)
                        <flux:select.option value="{{ $category->id }}"
                            :selected="request('category_id') == $category->id">
                            {{ $category->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:select name="status" label="Status">
                    <flux:select.option value="">All</flux:select.option>
                    @foreach (\Modules\Expenditure\Models\Expenditure::STATUSES as $status)
                        <flux:select.option value="{{ $status }}" :selected="request('status') === $status">
                            {{ ucfirst($status) }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input type="date" name="from" label="From" value="{{ request('from') }}" />
                <flux:input type="date" name="to" label="To" value="{{ request('to') }}" />
                <flux:button type="submit" icon="funnel" variant="ghost">Filter</flux:button>
            </form>
        </div>

        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <x-sortable-column column="spent_on">Date</x-sortable-column>
                    <x-sortable-column column="title">Item</x-sortable-column>
                    <flux:table.column>Category</flux:table.column>
                    <flux:table.column>Payee</flux:table.column>
                    <x-sortable-column column="amount">Amount</x-sortable-column>
                    <flux:table.column>Method</flux:table.column>
                    <x-sortable-column column="status">Status</x-sortable-column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($expenditures as $expenditure)
                        <flux:table.row>
                            <flux:table.cell>{{ $expenditure->spent_on?->format('d M Y') }}</flux:table.cell>
                            <flux:table.cell>{{ $expenditure->title }}</flux:table.cell>
                            <flux:table.cell>{{ $expenditure->category?->name ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $expenditure->payee ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ number_format($expenditure->amount, 2) }}</flux:table.cell>
                            <flux:table.cell>
                                {{ ucfirst(str_replace('_', ' ', $expenditure->payment_method)) }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge
                                    :color="match ($expenditure->status) {
                                        'paid' => 'emerald',
                                        'approved' => 'blue',
                                        'cancelled' => 'red',
                                        default => 'amber',
                                    }">
                                    {{ ucfirst($expenditure->status) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button href="{{ route('expenditure.show', $expenditure->id) }}" icon="eye"
                                    variant="primary" color="emerald">view</flux:button>
                                @can('edit expenditure')
                                    <flux:button href="{{ route('expenditure.edit', $expenditure->id) }}" icon="pencil"
                                        variant="primary" color="yellow">edit</flux:button>
                                @endcan
                                @can('delete expenditure')
                                    <form action="{{ route('expenditure.destroy', $expenditure->id) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Remove this expenditure?');">
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
                            <flux:table.cell colspan="8" class="text-center text-gray-500">
                                No expenditure recorded yet.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>

            <div class="mt-4">
                {{ $expenditures->links() }}
            </div>
        </flux:card>
    </div>
</x-layouts::app>
