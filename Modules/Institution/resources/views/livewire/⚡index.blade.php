<?php

use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Modules\Institution\Models\Institution;

new #[Title('Institutions')] class extends Component
{
    /** @var string[] */
    public const SORTABLE = ['name', 'code', 'phone', 'email', 'created_at'];

    #[Url]
    public string $search = '';

    #[Url]
    public string $sort = 'created_at';

    #[Url]
    public string $direction = 'desc';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('view institution'), 403);
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
     * Approve a self-created school, unlocking full access for its
     * Director. Admins review and approve; they never create schools.
     */
    public function approve(int $id): void
    {
        abort_unless(isAdmin(), 403);

        $institution = Institution::findOrFail($id);

        $institution->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by_id' => auth()->id(),
        ]);

        unset($this->institutions);

        Flux::toast(text: 'Institution "'.$institution->name.'" has been approved.', variant: 'success');
    }

    /**
     * Set which of a Director's schools the rest of the system runs as for
     * them. Remembered across logins until they choose a different one.
     */
    public function choose(int $id): void
    {
        $institution = Institution::findOrFail($id);

        abort_unless($institution->user_id === auth()->id(), 403);

        auth()->user()->update(['active_institution_id' => $institution->id]);

        session()->flash('success', 'Now managing "'.$institution->name.'".');

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function delete(int $id): void
    {
        // Only the platform owner may de-register a school.
        abort_unless(isAdmin() && auth()->user()->can('delete institution'), 403);

        Institution::findOrFail($id)->delete();

        unset($this->institutions);

        Flux::toast(text: 'Institution removed.', variant: 'success');
    }

    /**
     * @return Collection<int, Institution>
     */
    #[Computed]
    public function institutions(): Collection
    {
        $sort = in_array($this->sort, self::SORTABLE, true) ? $this->sort : 'created_at';
        $direction = $this->direction === 'asc' ? 'asc' : 'desc';

        // An Admin sees every school on the platform; a Director sees the
        // ones they own.
        $query = isAdmin()
            ? Institution::with('owner')
            : auth()->user()->institution()->with('owner');

        return $query
            ->when($this->search !== '', function ($q) {
                $term = '%'.$this->search.'%';

                $q->where(fn ($q2) => $q2->where('name', 'like', $term)
                    ->orWhere('code', 'like', $term)
                    ->orWhere('email', 'like', $term));
            })
            ->orderBy($sort, $direction)
            ->get();
    }
}; ?>

<div class="p-4">
    <div class="mb-2 flex flex-row flex-wrap items-end justify-between gap-3">
        @hasrole('Director')
            <flux:button href="{{ route('institution.create') }}" icon="plus" wire:navigate>Add Institution</flux:button>
        @endhasrole

        <flux:input type="search" icon="magnifying-glass" placeholder="Search name, code or email"
            wire:model.live.debounce.400ms="search" class="w-72" />
    </div>

    <flux:card>
        <flux:table wire:loading.class="opacity-60">
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sort === 'name'" :direction="$direction"
                    wire:click="sortBy('name')">Name</flux:table.column>
                @hasrole('Admin')
                    <flux:table.column>Owner</flux:table.column>
                @endhasrole
                <flux:table.column sortable :sorted="$sort === 'code'" :direction="$direction"
                    wire:click="sortBy('code')">Code</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'phone'" :direction="$direction"
                    wire:click="sortBy('phone')">Phone</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'email'" :direction="$direction"
                    wire:click="sortBy('email')">Email</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'created_at'" :direction="$direction"
                    wire:click="sortBy('created_at')">Created</flux:table.column>
                @hasrole('Director')
                    <flux:table.column>Active</flux:table.column>
                @endhasrole
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->institutions as $institute)
                    <flux:table.row :key="$institute->id">
                        <flux:table.cell>{{ $institute->name }}</flux:table.cell>
                        @hasrole('Admin')
                            <flux:table.cell>{{ $institute->owner?->name ?? '—' }}</flux:table.cell>
                        @endhasrole
                        <flux:table.cell>{{ $institute->code }}</flux:table.cell>
                        <flux:table.cell>{{ $institute->phone }}</flux:table.cell>
                        <flux:table.cell>{{ $institute->email }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$institute->is_approved ? 'emerald' : 'amber'">
                                {{ $institute->is_approved ? 'Approved' : 'Pending' }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $institute->created_at?->diffForHumans() }}</flux:table.cell>
                        @hasrole('Director')
                            <flux:table.cell>
                                @if (auth()->user()->active_institution_id === $institute->id)
                                    <flux:badge color="emerald">Active</flux:badge>
                                @elseif ($institute->user_id === auth()->id())
                                    <flux:button size="sm" type="button" wire:click="choose({{ $institute->id }})">
                                        Choose
                                    </flux:button>
                                @endif
                            </flux:table.cell>
                        @endhasrole
                        <flux:table.cell>
                            @if ($institute->user_id === auth()->id() || isAdmin())
                                <flux:button href="{{ route('institution.show', $institute->id) }}" icon="eye"
                                    variant="primary" color="emerald" wire:navigate>view</flux:button>
                            @endif
                            @if ($institute->user_id === auth()->id())
                                <flux:button href="{{ route('institution.edit', $institute->id) }}" icon="pencil"
                                    variant="primary" color="yellow" wire:navigate>edit</flux:button>
                            @endif
                            @hasrole('Admin')
                                @unless ($institute->is_approved)
                                    <flux:button type="button" icon="check-badge" variant="primary" color="emerald"
                                        wire:click="approve({{ $institute->id }})"
                                        wire:confirm="Approve this institution?">approve</flux:button>
                                @endunless
                                @can('delete institution')
                                    <flux:button type="button" icon="trash" variant="primary" color="red"
                                        wire:click="delete({{ $institute->id }})"
                                        wire:confirm="De-register this institution? This cannot be undone.">delete
                                    </flux:button>
                                @endcan
                            @endhasrole
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="9" class="text-center text-gray-500">
                            No institutions found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
