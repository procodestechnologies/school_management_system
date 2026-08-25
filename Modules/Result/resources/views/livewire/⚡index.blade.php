<?php

use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Classes\Models\SchoolClass;
use Modules\ReportCard\Services\ReportCardGenerationService;
use Modules\ReportCard\Support\TermResolver;
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

    /**
     * Build the whole selected class's report cards for the current term,
     * rendering each to PDF.
     *
     * The examination filter above has no say in this: an examination is a
     * single subject's paper, and a report card covers every subject the
     * class takes. The term comes from the class itself.
     *
     * Deliberately doesn't send anything: the scheduled send still waits a
     * day so results can be corrected first. This only produces the
     * documents, which is what a teacher printing for a parents' evening
     * actually wants.
     */
    public function generateReportCards(ReportCardGenerationService $generator): void
    {
        abort_unless(auth()->user()->can('edit reportcard'), 403);

        $schoolClass = $this->selectedClass;

        if (! $schoolClass) {
            Flux::toast(text: 'Pick a class first - report cards are generated a class at a time.', variant: 'danger');

            return;
        }

        $term = $this->reportTerm;

        if (! $term) {
            Flux::toast(
                text: 'This class has no examinations yet, so there is no term to report on.',
                variant: 'danger',
            );

            return;
        }

        $outcome = $generator->forClass($schoolClass, $term['term'], $term['academic_year']);

        if ($outcome['generated'] === 0) {
            Flux::toast(text: 'No learner in this class has marks for '.$term['term'].' yet.', variant: 'danger');

            return;
        }

        $message = $outcome['generated'].' report card'.($outcome['generated'] === 1 ? '' : 's')
            .' generated for '.$term['term'].'.';

        if ($outcome['skipped'] > 0) {
            $message .= ' '.$outcome['skipped'].' learner'.($outcome['skipped'] === 1 ? '' : 's')
                .' skipped for having no marks this term.';
        }

        Flux::toast(text: $message, variant: 'success');
    }

    /**
     * The class the filters are narrowed to, re-checked against what this
     * user is allowed to see rather than trusted from the URL.
     */
    #[Computed]
    public function selectedClass(): ?SchoolClass
    {
        if ($this->classId === '') {
            return null;
        }

        return $this->classes->firstWhere('id', (int) $this->classId);
    }

    /**
     * The term a generated report card will cover: whichever one the class
     * is currently in.
     *
     * @return array{term: string, academic_year: int, term_number: int|null}|null
     */
    #[Computed]
    public function reportTerm(): ?array
    {
        return $this->selectedClass
            ? TermResolver::currentFor($this->selectedClass)
            : null;
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

        <div class="flex flex-wrap gap-2">
            @can('edit reportcard')
                <flux:button type="button" icon="document-arrow-down" variant="primary" color="emerald"
                    wire:click="generateReportCards" wire:loading.attr="disabled"
                    wire:target="generateReportCards"
                    wire:confirm="Generate report cards for every learner in this class?">
                    <span wire:loading.remove wire:target="generateReportCards">Generate Report Cards</span>
                    <span wire:loading wire:target="generateReportCards">Generating…</span>
                </flux:button>
            @endcan

            @can('create result')
                <flux:button href="{{ route('result.entry.create') }}" icon="table-cells" variant="primary"
                    wire:navigate>
                    Enter Marks
                </flux:button>
                <flux:button href="{{ route('result.create') }}" wire:navigate>Add Result</flux:button>
            @endcan
        </div>
    </div>

    @can('edit reportcard')
        <flux:text class="mb-4 block text-xs text-zinc-500">
            @if (! $this->selectedClass)
                Pick a class to generate its report cards.
            @elseif ($this->reportTerm)
                Report cards will cover every subject in <strong>{{ $this->reportTerm['term'] }}</strong>
                ({{ $this->reportTerm['academic_year'] }}) for {{ $this->selectedClass->name }}, compared against the
                previous term.
            @else
                {{ $this->selectedClass->name }} has no examinations yet, so there is no term to report on.
            @endif
        </flux:text>
    @endcan

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
