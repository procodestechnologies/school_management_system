<x-layouts::app :title="__(config('institution.name'))">
    <div class="p-4">
        <div class="mb-2 flex flex-row justify-between">
            @hasrole('Director')
                <flux:button href="{{ route('institution.create') }}">Add Institution</flux:button>
            @endhasrole
        </div>
        {{-- {{ $institution }} --}}
        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <x-sortable-column column="name">Name</x-sortable-column>
                    @hasrole('Admin')
                        <flux:table.column>Owner</flux:table.column>
                    @endhasrole
                    <x-sortable-column column="code">Code</x-sortable-column>
                    <x-sortable-column column="phone">Phone</x-sortable-column>
                    <x-sortable-column column="email">Email</x-sortable-column>
                    <flux:table.column>Status</flux:table.column>
                    <x-sortable-column column="created_at">Created At</x-sortable-column>
                    @hasrole('Director')
                        <flux:table.column>Active</flux:table.column>
                    @endhasrole
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($institution as $institute)
                        <flux:table.row>
                            <flux:table.cell>{{ $institute->name }}</flux:table.cell>
                            @hasrole('Admin')
                                <flux:table.cell>{{ $institute->owner->name }}</flux:table.cell>
                            @endhasrole
                            <flux:table.cell>{{ $institute->code }}</flux:table.cell>
                            <flux:table.cell>{{ $institute->phone }}</flux:table.cell>
                            <flux:table.cell>{{ $institute->email }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$institute->is_approved ? 'emerald' : 'amber'">
                                    {{ $institute->is_approved ? 'Approved' : 'Pending' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $institute->created_at->diffForHumans() }}</flux:table.cell>
                            @hasrole('Director')
                                <flux:table.cell>
                                    @if (auth()->user()->active_institution_id === $institute->id)
                                        <flux:badge color="emerald">Active</flux:badge>
                                    @elseif (institutionOwner($institute->owner->id))
                                        <form action="{{ route('institution.choose', $institute->id) }}" method="POST">
                                            @csrf
                                            <flux:button size="sm" type="submit">Choose</flux:button>
                                        </form>
                                    @endif
                                </flux:table.cell>
                            @endhasrole
                            <flux:table.cell>
                                @if (institutionOwner($institute->owner->id))
                                    <flux:button href="{{ route('institution.edit', $institute->id) }}" icon='pencil'
                                        variant="primary" color="yellow">
                                        edit
                                    </flux:button>
                                @endif
                                <flux:button href="{{ route('institution.show', $institute->id) }}" icon='eye'
                                    variant="primary" color="emerald">
                                    view
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</x-layouts::app>
