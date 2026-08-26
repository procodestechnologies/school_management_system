<?php

use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Student\Models\StudentDetails;
use Modules\Subject\Models\Subject;

new #[Title('Subjects')] class extends Component
{
    use WithPagination;

    /** @var string[] */
    public const SORTABLE = ['name', 'code'];

    #[Url]
    public string $search = '';

    #[Url]
    public string $sort = 'name';

    #[Url]
    public string $direction = 'asc';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('view subject'), 403);
    }

    public function updating(string $property): void
    {
        if ($property === 'search') {
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
        abort_unless(auth()->user()->can('delete subject'), 403);

        $this->scoped()->findOrFail($id)->delete();

        Flux::toast(text: 'Subject removed.', variant: 'success');
    }

    #[Computed]
    public function subjects()
    {
        $sort = in_array($this->sort, self::SORTABLE, true) ? $this->sort : 'name';
        $direction = $this->direction === 'desc' ? 'desc' : 'asc';

        return $this->scoped()
            ->with('institution')
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';

                $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('code', 'like', $term));
            })
            ->orderBy($sort, $direction)
            ->paginate(10);
    }

    private function scoped()
    {
        $user = auth()->user();
        $query = Subject::query();

        if (isAdmin()) {
            return $query;
        }

        if ($user->hasRole('Teacher')) {
            return $query->where('institution_id', $user->teacherUserDetails?->institution_id ?? 0);
        }

        if ($user->hasAnyRole(['Parent', 'Student'])) {
            $institutionIds = $user->hasRole('Parent')
                ? StudentDetails::where('parent_id', $user->id)->pluck('institution_id')
                : StudentDetails::where('student_id', $user->id)->pluck('institution_id');

            return $query->whereIn('institution_id', $institutionIds);
        }

        return $query->where('institution_id', currentInstitution()?->id ?? 0);
    }
}; ?>

<div class="p-4">
    <div class="mb-2 flex flex-row flex-wrap items-end justify-between gap-3">
        <div class="flex gap-2">
            @can('create subject')
                <flux:button href="{{ route('subject.create') }}" icon="plus" wire:navigate>Add Subject</flux:button>
            @endcan
            @can('edit subject')
                <flux:button href="{{ route('subject.teachers.index') }}" icon="user-plus" variant="ghost"
                    wire:navigate>
                    Subject Teachers
                </flux:button>
            @endcan
        </div>

        <flux:input type="search" icon="magnifying-glass" placeholder="Search name or code"
            wire:model.live.debounce.400ms="search" class="w-64" />
    </div>

    <flux:card>
        <flux:table wire:loading.class="opacity-60">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sort === 'name'" :direction="$direction"
                    wire:click="sortBy('name')">Name</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'code'" :direction="$direction"
                    wire:click="sortBy('code')">Code</flux:table.column>
                <flux:table.column>Institution</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->subjects as $subject)
                    <flux:table.row :key="$subject->id">
                        <flux:table.cell>{{ $subject->name }}</flux:table.cell>
                        <flux:table.cell>{{ $subject->code ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $subject->institution?->name }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$subject->is_compulsory ? 'amber' : 'zinc'">
                                {{ $subject->is_compulsory ? 'Compulsory' : 'Optional' }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$subject->is_active ? 'emerald' : 'zinc'">
                                {{ $subject->is_active ? 'Active' : 'Inactive' }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button href="{{ route('subject.show', $subject->id) }}" icon="eye"
                                variant="primary" color="emerald" wire:navigate>view</flux:button>
                            @can('edit subject')
                                <flux:button href="{{ route('subject.edit', $subject->id) }}" icon="pencil"
                                    variant="primary" color="yellow" wire:navigate>edit</flux:button>
                            @endcan
                            @can('delete subject')
                                <flux:button type="button" icon="trash" variant="primary" color="red"
                                    wire:click="delete({{ $subject->id }})"
                                    wire:confirm="Remove this subject?">delete</flux:button>
                            @endcan
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center text-gray-500">
                            No subjects found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <div class="mt-4">
        {{ $this->subjects->links() }}
    </div>
</div>
