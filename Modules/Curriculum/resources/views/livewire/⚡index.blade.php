<?php

use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Modules\Curriculum\Models\Curriculum;
use Modules\Student\Models\StudentDetails;

new #[Title('Curriculum')] class extends Component
{
    #[Url]
    public string $search = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('view curriculum'), 403);
    }

    public function delete(int $id): void
    {
        abort_unless(auth()->user()->can('delete curriculum'), 403);

        $this->scoped()->findOrFail($id)->delete();

        Flux::toast(text: 'Curriculum deleted.', variant: 'success');
    }

    #[Computed]
    public function curricula(): Collection
    {
        return $this->scoped()
            ->with('institution')
            ->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
            ->orderBy('name')
            ->get();
    }

    private function scoped()
    {
        $user = auth()->user();
        $query = Curriculum::query();

        if (isAdmin()) {
            return $query;
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
    <div class="mb-4 flex flex-row flex-wrap items-end justify-between gap-3">
        @can('create curriculum')
            <flux:button href="{{ route('curriculum.create') }}" icon="plus" wire:navigate>Add Curriculum</flux:button>
        @endcan

        <flux:input type="search" icon="magnifying-glass" placeholder="Search curricula"
            wire:model.live.debounce.400ms="search" class="w-64" />
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3" wire:loading.class="opacity-60">
        @forelse ($this->curricula as $curriculum)
            <flux:card class="relative overflow-hidden transition-all duration-200 hover:shadow-lg"
                :key="$curriculum->id">
                <a href="{{ route('curriculum.show', $curriculum->id) }}" class="block" wire:navigate>
                    <flux:heading>{{ $curriculum->name }}</flux:heading>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <flux:badge color="blue">{{ $curriculum->systemLabel() }}</flux:badge>
                        <flux:badge :color="$curriculum->status === 'active' ? 'emerald' : 'zinc'">
                            {{ ucfirst($curriculum->status) }}
                        </flux:badge>
                    </div>
                </a>

                <div class="mt-4 flex flex-row justify-between">
                    @can('edit curriculum')
                        <flux:button variant="primary" href="{{ route('curriculum.edit', $curriculum->id) }}"
                            class="bg-blue-500" icon="pencil-square" wire:navigate>Edit</flux:button>
                    @endcan
                    @can('delete curriculum')
                        <flux:button type="button" variant="primary" class="bg-red-500" color="red" icon="trash"
                            wire:click="delete({{ $curriculum->id }})"
                            wire:confirm="Delete this curriculum?">Delete</flux:button>
                    @endcan
                </div>
            </flux:card>
        @empty
            <flux:text class="text-zinc-500">No curricula found.</flux:text>
        @endforelse
    </div>
</div>
