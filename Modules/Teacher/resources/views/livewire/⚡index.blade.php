<?php

use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Teacher\Models\TeacherDetails;

new #[Title('Teachers')] class extends Component
{
    use WithPagination;

    /** @var string[] */
    public const SORTABLE = ['employee_number', 'department', 'hire_date', 'status'];

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $sort = 'department';

    #[Url]
    public string $direction = 'asc';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('view teacher'), 403);
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
     * Removing a teacher takes their login with them - the same thing the
     * endpoint has always done.
     */
    public function delete(int $teacherId): void
    {
        abort_unless(auth()->user()->can('delete teacher'), 403);

        $details = $this->scoped()->where('teacher_id', $teacherId)->firstOrFail();
        $teacher = User::findOrFail($details->teacher_id);

        $details->delete();
        $teacher->delete();

        Flux::toast(text: 'Teacher removed.', variant: 'success');
    }

    #[Computed]
    public function teachers()
    {
        $sort = in_array($this->sort, self::SORTABLE, true) ? $this->sort : 'department';
        $direction = $this->direction === 'desc' ? 'desc' : 'asc';

        return $this->scoped()
            ->with(['teacher', 'institution'])
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';

                $query->where(function ($q) use ($term) {
                    $q->where('employee_number', 'like', $term)
                        ->orWhere('department', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhereHas('teacher', fn ($q2) => $q2->where('name', 'like', $term)->orWhere('email', 'like', $term));
                });
            })
            ->orderBy($sort, $direction)
            ->paginate(10);
    }

    private function scoped()
    {
        $query = TeacherDetails::query();

        if (! isAdmin()) {
            $query->where('institution_id', currentInstitution()?->id ?? 0);
        }

        return $query;
    }
}; ?>

<div class="p-4">
    <div class="mb-2 flex flex-row flex-wrap items-end justify-between gap-3">
        @can('create teacher')
            <flux:button href="{{ route('teacher.create') }}" icon="plus" wire:navigate>Add Teacher</flux:button>
        @endcan

        <div class="flex flex-wrap items-end gap-2">
            <flux:input type="search" icon="magnifying-glass" placeholder="Search name, staff no. or department"
                wire:model.live.debounce.400ms="search" class="w-72" />
            <flux:select wire:model.live="status" label="Status">
                <flux:select.option value="">All</flux:select.option>
                @foreach (['active', 'on_leave', 'suspended', 'resigned', 'terminated'] as $option)
                    <flux:select.option value="{{ $option }}">
                        {{ ucfirst(str_replace('_', ' ', $option)) }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <flux:card>
        <flux:table wire:loading.class="opacity-60">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'employee_number'" :direction="$direction"
                    wire:click="sortBy('employee_number')">Staff No.</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'department'" :direction="$direction"
                    wire:click="sortBy('department')">Department</flux:table.column>
                <flux:table.column>Phone</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'hire_date'" :direction="$direction"
                    wire:click="sortBy('hire_date')">Hired</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'status'" :direction="$direction"
                    wire:click="sortBy('status')">Status</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->teachers as $details)
                    <flux:table.row :key="$details->id">
                        <flux:table.cell>
                            {{ $details->teacher?->name }}
                            <flux:text class="text-xs text-zinc-500">{{ $details->teacher?->email }}</flux:text>
                        </flux:table.cell>
                        <flux:table.cell>{{ $details->employee_number ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $details->department ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $details->phone ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $details->hire_date?->format('d M Y') ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge
                                :color="match ($details->status) {
                                    'active' => 'emerald',
                                    'on_leave' => 'amber',
                                    'suspended' => 'red',
                                    default => 'zinc',
                                }">
                                {{ ucfirst(str_replace('_', ' ', $details->status ?? 'unknown')) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($details->teacher)
                                <flux:button href="{{ route('teacher.show', $details->teacher_id) }}" icon="eye"
                                    variant="primary" color="emerald" wire:navigate>view</flux:button>
                                @can('edit teacher')
                                    <flux:button href="{{ route('teacher.edit', $details->teacher_id) }}" icon="pencil"
                                        variant="primary" color="yellow" wire:navigate>edit</flux:button>
                                @endcan
                                @can('delete teacher')
                                    <flux:button type="button" icon="trash" variant="primary" color="red"
                                        wire:click="delete({{ $details->teacher_id }})"
                                        wire:confirm="Remove this teacher and their login?">delete</flux:button>
                                @endcan
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-gray-500">
                            No teachers found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <div class="mt-4">
        {{ $this->teachers->links() }}
    </div>
</div>
