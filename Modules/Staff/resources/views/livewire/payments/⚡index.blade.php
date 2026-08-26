<?php

use Flux\Flux;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Staff\Models\StaffPayment;

new #[Title('Payroll')] class extends Component
{
    use WithPagination;

    /** @var string[] */
    public const SORTABLE = ['period', 'net_amount', 'status'];

    #[Url]
    public string $period = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $sort = 'period';

    #[Url]
    public string $direction = 'desc';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('view payroll'), 403);
    }

    public function updating(string $property): void
    {
        if (in_array($property, ['period', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function sortBy(string $column): void
    {
        if (! in_array($column, self::SORTABLE, true)) {
            return;
        }

        if ($this->sort === $column) {
            $this->direction = $this->direction === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sort = $column;
        $this->direction = 'asc';
    }

    public function delete(int $id): void
    {
        abort_unless(auth()->user()->can('delete payroll'), 403);

        $this->scoped()->findOrFail($id)->delete();

        Flux::toast(text: 'Payment record removed.', variant: 'success');
    }

    #[Computed]
    public function payments()
    {
        $sort = in_array($this->sort, self::SORTABLE, true) ? $this->sort : 'period';
        $direction = $this->direction === 'asc' ? 'asc' : 'desc';

        return $this->filtered()
            ->with(['staff', 'institution'])
            ->orderBy($sort, $direction)
            ->paginate(10);
    }

    /**
     * @return array{total: float, paid: float}
     */
    #[Computed]
    public function totals(): array
    {
        $totals = $this->filtered()
            ->selectRaw('SUM(net_amount) as total, SUM(CASE WHEN status = ? THEN net_amount ELSE 0 END) as paid', ['paid'])
            ->first();

        return [
            'total' => (float) ($totals->total ?? 0),
            'paid' => (float) ($totals->paid ?? 0),
        ];
    }

    private function filtered()
    {
        return $this->scoped()
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->when($this->period !== '', function ($query) {
                $month = Carbon::parse($this->period.'-01');

                $query->whereYear('period', $month->year)->whereMonth('period', $month->month);
            });
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
    <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <flux:card class="space-y-1">
            <flux:text class="text-zinc-500">Total Payroll</flux:text>
            <flux:heading size="xl">{{ number_format($this->totals['total'], 2) }}</flux:heading>
        </flux:card>
        <flux:card class="space-y-1">
            <flux:text class="text-zinc-500">Paid Out</flux:text>
            <flux:heading size="xl">{{ number_format($this->totals['paid'], 2) }}</flux:heading>
        </flux:card>
        <flux:card class="space-y-1">
            <flux:text class="text-zinc-500">Outstanding</flux:text>
            <flux:heading size="xl">
                {{ number_format($this->totals['total'] - $this->totals['paid'], 2) }}
            </flux:heading>
        </flux:card>
    </div>

    <div class="mb-2 flex flex-row flex-wrap items-end justify-between gap-3">
        @can('create payroll')
            <flux:button href="{{ route('staff.payments.create') }}" icon="plus" wire:navigate>
                Record Payment
            </flux:button>
        @endcan

        <div class="flex flex-wrap items-end gap-2">
            <flux:input type="month" label="Period" wire:model.live="period" />
            <flux:select wire:model.live="status" label="Status">
                <flux:select.option value="">All</flux:select.option>
                @foreach (['pending', 'paid', 'cancelled'] as $option)
                    <flux:select.option value="{{ $option }}">{{ ucfirst($option) }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <flux:card>
        <flux:table wire:loading.class="opacity-60">
            <flux:table.columns>
                <flux:table.column>Staff</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'period'" :direction="$direction"
                    wire:click="sortBy('period')">Period</flux:table.column>
                <flux:table.column>Gross</flux:table.column>
                <flux:table.column>Allowances</flux:table.column>
                <flux:table.column>Deductions</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'net_amount'" :direction="$direction"
                    wire:click="sortBy('net_amount')">Net</flux:table.column>
                <flux:table.column>Method</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'status'" :direction="$direction"
                    wire:click="sortBy('status')">Status</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->payments as $payment)
                    <flux:table.row :key="$payment->id">
                        <flux:table.cell>
                            {{ $payment->staff?->name }}
                            @if ($payment->staff?->job_title)
                                <flux:text class="text-xs text-zinc-500">{{ $payment->staff->job_title }}</flux:text>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $payment->period?->format('M Y') }}</flux:table.cell>
                        <flux:table.cell>{{ number_format($payment->gross_amount, 2) }}</flux:table.cell>
                        <flux:table.cell>{{ number_format($payment->allowances, 2) }}</flux:table.cell>
                        <flux:table.cell>{{ number_format($payment->deductions, 2) }}</flux:table.cell>
                        <flux:table.cell>{{ number_format($payment->net_amount, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
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
                            <flux:button href="{{ route('staff.payments.show', $payment->id) }}" icon="eye"
                                variant="primary" color="emerald" wire:navigate>view</flux:button>
                            @can('edit payroll')
                                <flux:button href="{{ route('staff.payments.edit', $payment->id) }}" icon="pencil"
                                    variant="primary" color="yellow" wire:navigate>edit</flux:button>
                            @endcan
                            @can('delete payroll')
                                <flux:button type="button" icon="trash" variant="primary" color="red"
                                    wire:click="delete({{ $payment->id }})"
                                    wire:confirm="Remove this payment record?">delete</flux:button>
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
    </flux:card>

    <div class="mt-4">
        {{ $this->payments->links() }}
    </div>
</div>
