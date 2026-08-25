<?php

use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Result\Models\Result;
use Modules\Result\Services\ResultAccessService;

new #[Title('Results')] class extends Component
{
    use WithPagination;

    /** @var string[] */
    public const SORTABLE = ['marks_obtained', 'grade'];

    #[Url]
    public string $search = '';

    #[Url(as: 'class_id')]
    public string $classId = '';

    #[Url(as: 'examination_id')]
    public string $examinationId = '';

    #[Url]
    public string $sort = 'grade';

    #[Url]
    public string $direction = 'asc';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('view result'), 403);
    }

    public function updating(string $property): void
    {
        if (in_array($property, ['search', 'classId', 'examinationId'], true)) {
            $this->resetPage();
        }

        // A different class has a different set of papers - keeping the old
        // one selected would silently show nothing.
        if ($property === 'classId') {
            $this->examinationId = '';
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
        abort_unless(auth()->user()->can('delete result'), 403);

        $this->scoped()->findOrFail($id)->delete();

        Flux::toast(text: 'Result removed.', variant: 'success');
    }

    #[Computed]
    public function results()
    {
        $sort = in_array($this->sort, self::SORTABLE, true) ? $this->sort : 'grade';
        $direction = $this->direction === 'desc' ? 'desc' : 'asc';

        return $this->scoped()
            ->with(['schoolClass', 'student', 'examination'])
            ->when($this->classId !== '', fn ($query) => $query->where('class_id', $this->classId))
            ->when($this->examinationId !== '', fn ($query) => $query->where('examination_id', $this->examinationId))
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';

                $query->whereHas('student', fn ($q) => $q->where('name', 'like', $term));
            })
            ->orderBy($sort, $direction)
            ->paginate(10);
    }

    #[Computed]
    public function classes(): Collection
    {
        return ResultAccessService::selectableClasses(auth()->user());
    }

    #[Computed]
    public function examinations(): Collection
    {
        return ResultAccessService::selectableExaminations(
            auth()->user(),
            $this->classId !== '' ? (int) $this->classId : null,
        );
    }

    private function scoped()
    {
        $query = Result::query();

        ResultAccessService::scopeVisibleResults($query, auth()->user());

        return $query;
    }
}; ?>

<div class="p-4">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex flex-wrap items-end gap-2">
            <flux:input type="search" icon="magnifying-glass" placeholder="Search student"
                wire:model.live.debounce.400ms="search" class="w-56" />

            <flux:select wire:model.live="classId" label="Class">
                <flux:select.option value="">All Classes</flux:select.option>
                @foreach ($this->classes as $schoolClass)
                    <flux:select.option value="{{ $schoolClass->id }}">{{ $schoolClass->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="examinationId" label="Examination">
                <flux:select.option value="">
                    {{ $classId !== '' && $this->examinations->isEmpty() ? 'No examinations for this class' : 'All Examinations' }}
                </flux:select.option>
                @foreach ($this->examinations as $examination)
                    <flux:select.option value="{{ $examination->id }}">{{ $examination->title }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        @can('create result')
            <div class="flex flex-wrap gap-2">
                <flux:button href="{{ route('result.entry.create') }}" icon="table-cells" variant="primary"
                    wire:navigate>
                    Enter Marks
                </flux:button>
                <flux:button href="{{ route('result.create') }}" wire:navigate>Add Result</flux:button>
            </div>
        @endcan
    </div>

    <flux:card>
        <flux:table wire:loading.class="opacity-60">
            <flux:table.columns>
                <flux:table.column>Student</flux:table.column>
                <flux:table.column>Class</flux:table.column>
                <flux:table.column class="truncate">Examination</flux:table.column>
                <flux:table.column>Term</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'marks_obtained'" :direction="$direction"
                    wire:click="sortBy('marks_obtained')">Marks</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'grade'" :direction="$direction"
                    wire:click="sortBy('grade')">Grade</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->results as $result)
                    <flux:table.row :key="$result->id">
                        <flux:table.cell>{{ $result->student?->name }}</flux:table.cell>
                        <flux:table.cell>{{ $result->schoolClass?->name }}</flux:table.cell>
                        <flux:table.cell>{{ $result->examination?->title }}</flux:table.cell>
                        <flux:table.cell>{{ $result->examination?->term ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            {{ $result->marks_obtained }} / {{ $result->examination?->total_marks }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $result->grade ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button href="{{ route('result.show', $result->id) }}" icon="eye" variant="primary"
                                color="emerald" wire:navigate>view</flux:button>
                            @can('edit result')
                                <flux:button href="{{ route('result.edit', $result->id) }}" icon="pencil"
                                    variant="primary" color="yellow" wire:navigate>edit</flux:button>
                            @endcan
                            @can('delete result')
                                <flux:button type="button" icon="trash" variant="primary" color="red"
                                    wire:click="delete({{ $result->id }})"
                                    wire:confirm="Remove this result?">delete</flux:button>
                            @endcan
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-gray-500">
                            No results found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <div class="mt-4">
        {{ $this->results->links() }}
    </div>
</div>
