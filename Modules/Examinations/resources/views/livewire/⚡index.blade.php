<?php

use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Classes\Models\SchoolClass;
use Modules\Examinations\Models\Examination;
use Modules\Student\Models\StudentDetails;

new #[Title('Examinations')] class extends Component
{
    use WithPagination;

    /** @var string[] */
    public const SORTABLE = ['exam_date', 'title', 'total_marks'];

    #[Url]
    public string $search = '';

    #[Url(as: 'class_id')]
    public string $classId = '';

    #[Url]
    public string $sort = 'exam_date';

    #[Url]
    public string $direction = 'desc';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('view examination'), 403);
    }

    public function updating(string $property): void
    {
        if (in_array($property, ['search', 'classId'], true)) {
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
        abort_unless(auth()->user()->can('delete examination'), 403);

        $this->scoped()->findOrFail($id)->delete();

        Flux::toast(text: 'Examination removed.', variant: 'success');
    }

    #[Computed]
    public function examinations()
    {
        $sort = in_array($this->sort, self::SORTABLE, true) ? $this->sort : 'exam_date';
        $direction = $this->direction === 'asc' ? 'asc' : 'desc';

        return $this->scoped()
            ->with(['institution', 'schoolClass', 'subject'])
            ->when($this->classId !== '', fn ($query) => $query->where('class_id', $this->classId))
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';

                $query->where(fn ($q) => $q->where('title', 'like', $term)
                    ->orWhere('term', 'like', $term)
                    ->orWhereHas('subject', fn ($q2) => $q2->where('name', 'like', $term)));
            })
            ->orderBy($sort, $direction)
            ->paginate(10);
    }

    #[Computed]
    public function classes(): Collection
    {
        $query = SchoolClass::query();

        if (! isAdmin()) {
            $query->where('institution_id', $this->viewerInstitutionId());
        }

        return $query->orderBy('name')->get();
    }

    private function viewerInstitutionId(): int
    {
        $user = auth()->user();

        if ($user->hasRole('Teacher')) {
            return $user->teacherUserDetails?->institution_id ?? 0;
        }

        return currentInstitution()?->id ?? 0;
    }

    private function scoped()
    {
        $user = auth()->user();
        $query = Examination::query();

        if (isAdmin()) {
            return $query;
        }

        if ($user->hasAnyRole(['Parent', 'Student'])) {
            $institutionIds = $user->hasRole('Parent')
                ? StudentDetails::where('parent_id', $user->id)->pluck('institution_id')
                : StudentDetails::where('student_id', $user->id)->pluck('institution_id');

            return $query->whereIn('institution_id', $institutionIds);
        }

        return $query->where('institution_id', $this->viewerInstitutionId());
    }
}; ?>

<div class="p-4">
    <div class="mb-2 flex flex-row flex-wrap items-end justify-between gap-3">
        @can('create examination')
            <flux:button href="{{ route('examinations.create') }}" icon="plus" wire:navigate>
                Add Examination
            </flux:button>
        @endcan

        <div class="flex flex-wrap items-end gap-2">
            <flux:input type="search" icon="magnifying-glass" placeholder="Search title, term or subject"
                wire:model.live.debounce.400ms="search" class="w-64" />
            <flux:select wire:model.live="classId" label="Class">
                <flux:select.option value="">All Classes</flux:select.option>
                @foreach ($this->classes as $schoolClass)
                    <flux:select.option value="{{ $schoolClass->id }}">{{ $schoolClass->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <flux:card>
        <flux:table wire:loading.class="opacity-60">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sort === 'title'" :direction="$direction"
                    wire:click="sortBy('title')">Title</flux:table.column>
                <flux:table.column>Class</flux:table.column>
                <flux:table.column>Subject</flux:table.column>
                <flux:table.column>Term</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'exam_date'" :direction="$direction"
                    wire:click="sortBy('exam_date')">Date</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'total_marks'" :direction="$direction"
                    wire:click="sortBy('total_marks')">Total</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->examinations as $examination)
                    <flux:table.row :key="$examination->id">
                        <flux:table.cell>{{ $examination->title }}</flux:table.cell>
                        <flux:table.cell>
                            {{ $examination->schoolClass?->name ?? $examination->class_name ?? '—' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $examination->subject?->name ?? $examination->subject_name ?? '—' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $examination->term ?? '—' }}
                            @if ($examination->academic_year)
                                {{ $examination->academic_year }}
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $examination->exam_date?->format('d M Y') }}</flux:table.cell>
                        <flux:table.cell>{{ $examination->total_marks }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button href="{{ route('examinations.show', $examination->id) }}" icon="eye"
                                variant="primary" color="emerald" wire:navigate>view</flux:button>
                            @can('edit examination')
                                <flux:button href="{{ route('examinations.edit', $examination->id) }}" icon="pencil"
                                    variant="primary" color="yellow" wire:navigate>edit</flux:button>
                            @endcan
                            @can('delete examination')
                                <flux:button type="button" icon="trash" variant="primary" color="red"
                                    wire:click="delete({{ $examination->id }})"
                                    wire:confirm="Remove this examination?">delete</flux:button>
                            @endcan
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-gray-500">
                            No examinations found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <div class="mt-4">
        {{ $this->examinations->links() }}
    </div>
</div>
