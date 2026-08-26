<?php

use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\FeeManagement\Models\Fee;
use App\Services\FeeReminderService;

new #[Title('Fee Management')] class extends Component
{
    use WithPagination;

    /** @var string[] */
    public const SORTABLE = ['title', 'amount', 'amount_paid', 'due_date', 'created_at'];

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $sort = 'created_at';

    #[Url]
    public string $direction = 'desc';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('view feemanagement'), 403);
    }

    public function updating(string $property): void
    {
        if (in_array($property, ['search', 'status'], true)) {
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
        abort_unless(auth()->user()->can('delete feemanagement'), 403);

        $this->scoped()->findOrFail($id)->delete();

        Flux::toast(text: 'Fee record removed.', variant: 'success');
    }

    /**
     * Send a consolidated fee-balance reminder to every parent with an
     * outstanding fee, scoped the same way the listing is.
     */
    public function sendReminders(FeeReminderService $reminders): void
    {
        abort_unless(auth()->user()->can('create feemanagement'), 403);

        $query = Fee::query();
        $this->scopeToViewer($query);

        $result = $reminders->sendForDefaulters($query);

        if ($result['parents_notified'] === 0) {
            Flux::toast(text: 'No outstanding fee balances to remind anyone about.', variant: 'warning');

            return;
        }

        $message = "Reminders sent to {$result['parents_notified']} parent(s) - {$result['emails_sent']} email(s), {$result['sms_sent']} SMS.";

        if ($result['skipped_no_contact'] > 0) {
            $message .= " {$result['skipped_no_contact']} parent(s) skipped - no email or phone on file.";
        }

        Flux::toast(text: $message, variant: 'success');
    }

    #[Computed]
    public function fees()
    {
        $sort = in_array($this->sort, self::SORTABLE, true) ? $this->sort : 'created_at';
        $direction = $this->direction === 'asc' ? 'asc' : 'desc';

        return $this->filtered()
            ->with(['student.studentUserDetails', 'institution', 'parent'])
            ->orderBy($sort, $direction)
            ->paginate(10);
    }

    /**
     * Parents with at least one unpaid fee - who a reminder run would reach.
     */
    #[Computed]
    public function defaulterCount(): int
    {
        $query = Fee::query();
        $this->scopeToViewer($query);

        return $query->whereColumn('amount_paid', '<', 'amount')
            ->whereNotNull('parent_id')
            ->distinct()
            ->count('parent_id');
    }

    /**
     * Status is worked out from the amounts rather than stored, so filtering
     * by it has to be expressed as the same arithmetic in SQL - otherwise
     * only the rows on the current page could ever be filtered.
     */
    private function filtered()
    {
        $query = Fee::query();
        $this->scopeToViewer($query);

        return $query
            ->when($this->search !== '', function ($q) {
                $term = '%'.$this->search.'%';

                $q->where(function ($q2) use ($term) {
                    $q2->where('title', 'like', $term)
                        ->orWhere('fee_type', 'like', $term)
                        ->orWhereHas('student', fn ($q3) => $q3->where('name', 'like', $term));
                });
            })
            ->when($this->status === 'paid', fn ($q) => $q->whereColumn('amount_paid', '>=', 'amount'))
            ->when($this->status === 'partial', fn ($q) => $q->where('amount_paid', '>', 0)->whereColumn('amount_paid', '<', 'amount'))
            ->when($this->status === 'overdue', fn ($q) => $q->whereColumn('amount_paid', '<', 'amount')
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', now()->toDateString()))
            ->when($this->status === 'pending', fn ($q) => $q->where('amount_paid', '<=', 0)
                ->where(fn ($q2) => $q2->whereNull('due_date')->orWhereDate('due_date', '>=', now()->toDateString())));
    }

    private function scopeToViewer($query): void
    {
        $user = auth()->user();

        if (isAdmin()) {
            return;
        }

        if ($user->hasRole('Parent')) {
            $query->where('parent_id', $user->id);

            return;
        }

        if ($user->hasRole('Student')) {
            $query->where('student_id', $user->id);

            return;
        }

        $query->where('institution_id', currentInstitution()?->id ?? 0);
    }

    private function scoped()
    {
        $query = Fee::query();
        $this->scopeToViewer($query);

        return $query;
    }
}; ?>

<div class="p-4">
    <div class="mb-2 flex flex-row flex-wrap items-end justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            @can('create feemanagement')
                <flux:button href="{{ route('feemanagement.create') }}" icon="plus" wire:navigate>Add Fee</flux:button>
            @endcan

            @can('create feemanagement')
                @if (institutionHasFeature('ai_receipt_scanning'))
                    <flux:button href="{{ route('feemanagement.receipts.create') }}" icon="sparkles" color="purple"
                        wire:navigate>
                        Scan Receipt
                    </flux:button>
                @endif
            @endcan

            @can('create feemanagement')
                @if ($this->defaulterCount > 0)
                    <flux:button type="button" icon="bell-alert" variant="ghost" wire:click="sendReminders"
                        wire:confirm="Send a balance reminder to {{ $this->defaulterCount }} parent(s)?">
                        <span wire:loading.remove wire:target="sendReminders">
                            Remind {{ $this->defaulterCount }} parent(s)
                        </span>
                        <span wire:loading wire:target="sendReminders">Sending…</span>
                    </flux:button>
                @endif
            @endcan
        </div>

        <div class="flex flex-wrap items-end gap-2">
            <flux:input type="search" icon="magnifying-glass" placeholder="Search title, type or student"
                wire:model.live.debounce.400ms="search" class="w-72" />
            <flux:select wire:model.live="status" label="Status">
                <flux:select.option value="">All</flux:select.option>
                @foreach (['pending', 'partial', 'paid', 'overdue'] as $option)
                    <flux:select.option value="{{ $option }}">{{ ucfirst($option) }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <flux:card>
        <flux:table wire:loading.class="opacity-60">
            <flux:table.columns>
                <flux:table.column>Student</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'title'" :direction="$direction"
                    wire:click="sortBy('title')">Title</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'amount'" :direction="$direction"
                    wire:click="sortBy('amount')">Amount</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'amount_paid'" :direction="$direction"
                    wire:click="sortBy('amount_paid')">Paid</flux:table.column>
                <flux:table.column>Balance</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'due_date'" :direction="$direction"
                    wire:click="sortBy('due_date')">Due Date</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->fees as $fee)
                    <flux:table.row :key="$fee->id">
                        <flux:table.cell>
                            {{ $fee->student?->name }}
                            @if ($fee->student?->studentUserDetails?->admission_number)
                                <flux:text class="text-xs text-zinc-500">
                                    {{ $fee->student->studentUserDetails->admission_number }}
                                </flux:text>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $fee->title }}
                            <flux:text class="text-xs text-zinc-500">{{ ucfirst($fee->fee_type) }}</flux:text>
                        </flux:table.cell>
                        <flux:table.cell>{{ number_format($fee->amount, 2) }}</flux:table.cell>
                        <flux:table.cell>{{ number_format($fee->amount_paid, 2) }}</flux:table.cell>
                        <flux:table.cell>{{ number_format($fee->balance, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge
                                :color="match ($fee->status) {
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
                                variant="primary" color="emerald" wire:navigate>view</flux:button>
                            @can('edit feemanagement')
                                <flux:button href="{{ route('feemanagement.edit', $fee->id) }}" icon="pencil"
                                    variant="primary" color="yellow" wire:navigate>edit</flux:button>
                            @endcan
                            @can('delete feemanagement')
                                <flux:button type="button" icon="trash" variant="primary" color="red"
                                    wire:click="delete({{ $fee->id }})"
                                    wire:confirm="Remove this fee record?">delete</flux:button>
                            @endcan
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="text-center text-gray-500">
                            No fee records found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <div class="mt-4">
        {{ $this->fees->links() }}
    </div>
</div>
