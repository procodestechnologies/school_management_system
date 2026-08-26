<?php

use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\ReportCard\Models\ReportCard;
use Modules\ReportCard\Services\ReportCardSender;
use Modules\Student\Models\StudentDetails;

new #[Title('Report Cards')] class extends Component
{
    use WithPagination;

    /** @var string[] */
    public const SORTABLE = ['term', 'mean_grade', 'completed_at'];

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $sort = 'completed_at';

    #[Url]
    public string $direction = 'desc';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('view reportcard'), 403);
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

    /**
     * Send one report card to its parent now.
     *
     * The nightly run is the normal path; this is the hand crank for when
     * it didn't fire, or a parent lost the link. It sends whatever the
     * report says as it stands - no waiting a day, and no re-checking that
     * every subject is marked, since whoever pressed the button is looking
     * at the report.
     */
    public function send(int $id, ReportCardSender $sender): void
    {
        abort_unless(auth()->user()->can('edit reportcard'), 403);

        $reportCard = $this->scoped()->findOrFail($id);

        $outcome = $sender->send($reportCard);

        unset($this->reportCards);

        if (! $outcome['sent']) {
            Flux::toast(text: $outcome['reason'], variant: 'danger');

            return;
        }

        $channels = collect(['email' => $outcome['email'], 'SMS' => $outcome['sms']])
            ->filter()
            ->keys();

        if ($channels->isEmpty()) {
            Flux::toast(text: $outcome['reason'], variant: 'danger');

            return;
        }

        Flux::toast(
            text: 'Sent to '.$reportCard->student?->name.'\'s parent by '.$channels->join(' and ').'.',
            variant: 'success',
        );
    }

    #[Computed]
    public function reportCards()
    {
        $sort = in_array($this->sort, self::SORTABLE, true) ? $this->sort : 'completed_at';
        $direction = $this->direction === 'asc' ? 'asc' : 'desc';

        return $this->scoped()
            ->with(['institution', 'schoolClass', 'student'])
            // "Sent" is a status column; anything else that's finished is
            // waiting to go out.
            ->when($this->status === 'sent', fn ($query) => $query->where('status', 'sent'))
            ->when($this->status === 'ready', fn ($query) => $query->where('status', '!=', 'sent'))
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';

                $query->where(fn ($q) => $q->where('term', 'like', $term)
                    ->orWhereHas('student', fn ($q2) => $q2->where('name', 'like', $term)));
            })
            ->orderBy($sort, $direction)
            ->paginate(15);
    }

    private function scoped()
    {
        $user = auth()->user();
        $query = ReportCard::query();

        if (isAdmin()) {
            return $query;
        }

        if ($user->hasRole('Teacher')) {
            return $query->where('institution_id', $user->teacherUserDetails?->institution_id ?? 0);
        }

        if ($user->hasRole('Parent')) {
            return $query->whereIn('student_id', StudentDetails::where('parent_id', $user->id)->pluck('student_id'));
        }

        if ($user->hasRole('Student')) {
            return $query->where('student_id', $user->id);
        }

        return $query->where('institution_id', currentInstitution()?->id ?? 0);
    }
}; ?>

<div class="p-4">
    <div class="mb-2 flex flex-row flex-wrap items-end justify-between gap-3">
        @can('edit reportcard')
            <flux:button href="{{ route('reportcard.settings') }}" icon="cog-6-tooth" variant="primary" wire:navigate>
                Customize Report Card Settings
            </flux:button>
        @endcan

        <div class="flex flex-wrap items-end gap-2">
            <flux:input type="search" icon="magnifying-glass" placeholder="Search student or term"
                wire:model.live.debounce.400ms="search" class="w-64" />
            <flux:select wire:model.live="status" label="Status">
                <flux:select.option value="">All</flux:select.option>
                <flux:select.option value="ready">Ready</flux:select.option>
                <flux:select.option value="sent">Sent</flux:select.option>
            </flux:select>
        </div>
    </div>

    <flux:card>
        <flux:table wire:loading.class="opacity-60">
            <flux:table.columns>
                <flux:table.column>Student</flux:table.column>
                <flux:table.column>Class</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'term'" :direction="$direction"
                    wire:click="sortBy('term')">Term</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'mean_grade'" :direction="$direction"
                    wire:click="sortBy('mean_grade')">Mean Grade</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->reportCards as $reportCard)
                    <flux:table.row :key="$reportCard->id">
                        <flux:table.cell>{{ $reportCard->student?->name }}</flux:table.cell>
                        <flux:table.cell>{{ $reportCard->schoolClass?->name }}</flux:table.cell>
                        <flux:table.cell>
                            {{ $reportCard->term }}
                            @if ($reportCard->academic_year)
                                {{ $reportCard->academic_year }}
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $reportCard->mean_grade ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$reportCard->isSent() ? 'emerald' : 'amber'">
                                {{ $reportCard->isSent() ? 'Sent' : 'Ready' }}
                            </flux:badge>

                            {{-- When it went out, so a director can see at a glance
                                 whether the nightly run actually fired. --}}
                            @if ($reportCard->sent_at)
                                <flux:text class="mt-1 block text-xs text-zinc-500">
                                    {{ $reportCard->sent_at->diffForHumans() }}
                                </flux:text>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button href="{{ route('reportcard.show', $reportCard->id) }}" icon="eye"
                                variant="primary" color="emerald" wire:navigate>view</flux:button>

                            @can('edit reportcard')
                                <flux:button type="button" icon="paper-airplane" variant="primary" color="blue"
                                    wire:click="send({{ $reportCard->id }})" wire:loading.attr="disabled"
                                    wire:target="send({{ $reportCard->id }})"
                                    wire:confirm="{{ $reportCard->isSent()
                                        ? 'Send this report card to the parent again? The previous download link stops working.'
                                        : 'Send this report card to the parent now, by email and SMS?' }}">
                                    <span wire:loading.remove wire:target="send({{ $reportCard->id }})">
                                        {{ $reportCard->isSent() ? 'resend' : 'send' }}
                                    </span>
                                    <span wire:loading wire:target="send({{ $reportCard->id }})">sending…</span>
                                </flux:button>
                            @endcan
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center text-gray-500">
                            No report cards yet.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <div class="mt-4">
        {{ $this->reportCards->links() }}
    </div>
</div>
