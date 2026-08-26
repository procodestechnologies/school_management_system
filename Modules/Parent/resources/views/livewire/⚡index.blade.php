<?php

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Modules\Parent\Models\ParentDetails;
use Modules\Student\Models\StudentDetails;

new #[Title('Parents')] class extends Component
{
    #[Url]
    public string $search = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('view parent'), 403);
    }

    public function delete(int $id): void
    {
        abort_unless(auth()->user()->can('delete parent'), 403);

        $parent = User::role('Parent')->findOrFail($id);
        $this->authorizeAccessTo($parent);

        DB::transaction(function () use ($parent) {
            // Children are unlinked rather than deleted with the parent.
            StudentDetails::where('parent_id', $parent->id)->update(['parent_id' => null]);

            ParentDetails::where('parent_id', $parent->id)->delete();

            $parent->delete();
        });

        Flux::toast(text: 'Parent removed.', variant: 'success');
    }

    /**
     * An Admin sees every parent; everyone else sees the parents of children
     * in whichever institution is currently active for them.
     */
    #[Computed]
    public function parents()
    {
        $institution = currentInstitution();

        $query = isAdmin()
            ? User::role('Parent')
            : ($institution ? $institution->parents() : null);

        if (! $query) {
            return collect();
        }

        return $query
            ->with([
                'children' => function ($childQuery) use ($institution) {
                    $childQuery->when(! isAdmin() && $institution, fn ($q) => $q->where('institution_id', $institution->id))
                        ->with('student');
                },
                'parent',
            ])
            ->when($this->search !== '', function ($q) {
                $term = '%'.$this->search.'%';

                $q->where(fn ($q2) => $q2->where('name', 'like', $term)->orWhere('email', 'like', $term));
            })
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function institution()
    {
        return currentInstitution();
    }

    /**
     * A parent with no children at all isn't claimed by any institution yet,
     * so any Director can manage them. Otherwise at least one child must be
     * in the viewer's active institution.
     */
    private function authorizeAccessTo(User $parent): void
    {
        if (isAdmin()) {
            return;
        }

        $children = StudentDetails::where('parent_id', $parent->id)->get();

        $accessible = $children->isEmpty()
            || $children->where('institution_id', currentInstitution()?->id)->isNotEmpty();

        abort_unless($accessible, 403);
    }
}; ?>

<div class="p-4">
    <div class="mb-2 flex flex-row flex-wrap items-end justify-between gap-3">
        @can('create parent')
            <flux:button href="{{ route('parent.create') }}" icon="plus" wire:navigate>Add Parent</flux:button>
        @endcan

        <flux:input type="search" icon="magnifying-glass" placeholder="Search name or email"
            wire:model.live.debounce.400ms="search" class="w-72" />
    </div>

    @if (! isAdmin() && ! $this->institution)
        <flux:card class="py-10 text-center">
            <flux:text class="text-zinc-500">No institution found for your account.</flux:text>
        </flux:card>
    @else
        <flux:card>
            <flux:table wire:loading.class="opacity-60">
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Email</flux:table.column>
                    <flux:table.column>Phone</flux:table.column>
                    <flux:table.column>Children</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($this->parents as $parent)
                        <flux:table.row :key="$parent->id">
                            <flux:table.cell>{{ $parent->name }}</flux:table.cell>
                            <flux:table.cell>{{ $parent->email }}</flux:table.cell>
                            <flux:table.cell>{{ $parent->parent?->parent_phone ?? '—' }}</flux:table.cell>
                            <flux:table.cell>
                                @forelse ($parent->children as $child)
                                    <flux:badge color="zinc" class="mb-1 mr-1">
                                        {{ $child->student?->name ?? '—' }}
                                    </flux:badge>
                                @empty
                                    <flux:text class="text-zinc-500">None linked</flux:text>
                                @endforelse
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button href="{{ route('parent.show', $parent->id) }}" icon="eye"
                                    variant="primary" color="emerald" wire:navigate>view</flux:button>
                                @can('edit parent')
                                    <flux:button href="{{ route('parent.edit', $parent->id) }}" icon="pencil"
                                        variant="primary" color="yellow" wire:navigate>edit</flux:button>
                                @endcan
                                @can('delete parent')
                                    <flux:button type="button" icon="trash" variant="primary" color="red"
                                        wire:click="delete({{ $parent->id }})"
                                        wire:confirm="Remove this parent? Their children stay, unlinked.">delete
                                    </flux:button>
                                @endcan
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center text-gray-500">
                                No parents found.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
    @endif
</div>
