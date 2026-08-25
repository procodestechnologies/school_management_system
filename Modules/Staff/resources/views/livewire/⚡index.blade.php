<?php

use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Staff\Models\StaffDetails;

new #[Title('Staff')] class extends Component
{
    use WithPagination;

    /** @var string[] */
    public const SORTABLE = ['name', 'job_title', 'department', 'hire_date', 'status'];

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $sort = 'name';

    #[Url]
    public string $direction = 'asc';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('view staff'), 403);
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
        abort_unless(auth()->user()->can('delete staff'), 403);

        $this->scoped()->findOrFail($id)->delete();

        Flux::toast(text: 'Staff member removed.', variant: 'success');
    }

    #[Computed]
    public function staff()
    {
        $sort = in_array($this->sort, self::SORTABLE, true) ? $this->sort : 'name';
        $direction = $this->direction === 'desc' ? 'desc' : 'asc';

        return $this->scoped()
            ->with(['user', 'institution'])
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';

                $query->where(fn ($q) => $q->where('name', 'like', $term)
                    ->orWhere('staff_number', 'like', $term)
                    ->orWhere('job_title', 'like', $term)
                    ->orWhere('department', 'like', $term));
            })
            ->orderBy($sort, $direction)
            ->paginate(10);
    }

    private function scoped()
    {
        $query = StaffDetails::query();

        if (! isAdmin()) {
            $query->where('institution_id', currentInstitution()?->id ?? 0);
        }

        return $query;
    }
}; ?>

<div class="p-4">
    <div class="mb-2 flex flex-row flex-wrap items-end justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            @can('create staff')
                <flux:button href="{{ route('staff.create') }}" icon="plus" wire:navigate>Add Staff</flux:button>
            @endcan
            @can('view payroll')
                <flux:button href="{{ route('staff.payments.index') }}" icon="banknotes" variant="ghost"
                    wire:navigate>Payroll</flux:button>
            @endcan
        </div>

        <div class="flex flex-wrap items-end gap-2">
            <flux:input type="search" icon="magnifying-glass" placeholder="Search name, role or department"
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
                <flux:table.column sortable :sorted="$sort === 'name'" :direction="$direction"
                    wire:click="sortBy('name')">Name</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'job_title'" :direction="$direction"
                    wire:click="sortBy('job_title')">Job Title</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'department'" :direction="$direction"
                    wire:click="sortBy('department')">Department</flux:table.column>
                <flux:table.column>Login</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'hire_date'" :direction="$direction"
                    wire:click="sortBy('hire_date')">Hired</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'status'" :direction="$direction"
                    wire:click="sortBy('status')">Status</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->staff as $member)
                    <flux:table.row :key="$member->id">
                        <flux:table.cell>
                            {{ $member->name }}
                            @if ($member->staff_number)
                                <flux:text class="text-xs text-zinc-500">{{ $member->staff_number }}</flux:text>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $member->job_title ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $member->department ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($member->user)
                                <flux:badge color="emerald">{{ $member->user->email }}</flux:badge>
                            @else
                                <flux:text class="text-zinc-500">No login</flux:text>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $member->hire_date?->format('d M Y') ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge
                                :color="match ($member->status) {
                                    'active' => 'emerald',
                                    'on_leave' => 'amber',
                                    'suspended' => 'red',
                                    default => 'zinc',
                                }">
                                {{ ucfirst(str_replace('_', ' ', $member->status ?? 'unknown')) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button href="{{ route('staff.show', $member->id) }}" icon="eye" variant="primary"
                                color="emerald" wire:navigate>view</flux:button>
                            @can('edit staff')
                                <flux:button href="{{ route('staff.edit', $member->id) }}" icon="pencil"
                                    variant="primary" color="yellow" wire:navigate>edit</flux:button>
                            @endcan
                            @can('delete staff')
                                <flux:button type="button" icon="trash" variant="primary" color="red"
                                    wire:click="delete({{ $member->id }})"
                                    wire:confirm="Remove this staff member?">delete</flux:button>
                            @endcan
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-gray-500">
                            No staff found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <div class="mt-4">
        {{ $this->staff->links() }}
    </div>
</div>
