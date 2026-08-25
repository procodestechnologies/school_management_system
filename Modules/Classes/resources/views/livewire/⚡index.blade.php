<?php

use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Modules\Classes\Models\SchoolClass;
use Modules\Student\Models\StudentDetails;

new #[Title('Classes')] class extends Component
{
    /** @var string[] */
    public const SORTABLE = ['name', 'level', 'capacity'];

    #[Url]
    public string $search = '';

    #[Url]
    public string $sort = 'name';

    #[Url]
    public string $direction = 'asc';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('view classes'), 403);
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
        abort_unless(auth()->user()->can('delete classes'), 403);

        $this->scoped()->findOrFail($id)->delete();

        Flux::toast(text: 'Class removed.', variant: 'success');
    }

    #[Computed]
    public function classes()
    {
        $sort = in_array($this->sort, self::SORTABLE, true) ? $this->sort : 'name';
        $direction = $this->direction === 'desc' ? 'desc' : 'asc';

        return $this->scoped()
            ->with(['institution', 'classTeacher', 'curriculum'])
            ->withCount('students')
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';

                $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('level', 'like', $term));
            })
            ->orderBy($sort, $direction)
            ->get();
    }

    private function scoped()
    {
        $user = auth()->user();
        $query = SchoolClass::query();

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
        @can('create classes')
            <flux:button href="{{ route('classes.create') }}" icon="plus" wire:navigate>Add Class</flux:button>
        @endcan

        <flux:input type="search" icon="magnifying-glass" placeholder="Search name or level"
            wire:model.live.debounce.400ms="search" class="w-64" />
    </div>

    <flux:card>
        <flux:table wire:loading.class="opacity-60">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sort === 'name'" :direction="$direction"
                    wire:click="sortBy('name')">Name</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'level'" :direction="$direction"
                    wire:click="sortBy('level')">Level</flux:table.column>
                <flux:table.column>Curriculum</flux:table.column>
                <flux:table.column>Class Teacher</flux:table.column>
                <flux:table.column>Students</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'capacity'" :direction="$direction"
                    wire:click="sortBy('capacity')">Capacity</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->classes as $schoolClass)
                    <flux:table.row :key="$schoolClass->id">
                        <flux:table.cell>{{ $schoolClass->name }}</flux:table.cell>
                        <flux:table.cell>{{ $schoolClass->level ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($schoolClass->curriculum)
                                <flux:badge color="blue">{{ $schoolClass->curriculum->systemLabel() }}</flux:badge>
                            @else
                                <flux:text class="text-zinc-500">School default</flux:text>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $schoolClass->classTeacher?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $schoolClass->students_count }}</flux:table.cell>
                        <flux:table.cell>{{ $schoolClass->capacity ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button href="{{ route('classes.show', $schoolClass->id) }}" icon="eye"
                                variant="primary" color="emerald" wire:navigate>view</flux:button>
                            @can('edit classes')
                                <flux:button href="{{ route('classes.edit', $schoolClass->id) }}" icon="pencil"
                                    variant="primary" color="yellow" wire:navigate>edit</flux:button>
                            @endcan
                            @can('delete classes')
                                <flux:button type="button" icon="trash" variant="primary" color="red"
                                    wire:click="delete({{ $schoolClass->id }})"
                                    wire:confirm="Remove this class?">delete</flux:button>
                            @endcan
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-gray-500">
                            No classes found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
