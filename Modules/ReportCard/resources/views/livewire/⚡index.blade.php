<?php

use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\ReportCard\Models\ReportCard;
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
            <flux:button href="{{ route('reportcard.settings') }}" icon="cog-6-tooth" variant="ghost" wire:navigate>
                Settings
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
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button href="{{ route('reportcard.show', $reportCard->id) }}" icon="eye"
                                variant="primary" color="emerald" wire:navigate>view</flux:button>
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
