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
                    <flux:table.column>Name</flux:table.column>
                    @hasrole('Admin')
                        <flux:table.column>Owner</flux:table.column>
                    @endhasrole
                    <flux:table.column>Code</flux:table.column>
                    <flux:table.column>Phone</flux:table.column>
                    <flux:table.column>Email</flux:table.column>
                    <flux:table.column>Created At</flux:table.column>
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
                            <flux:table.cell>{{ $institute->created_at->diffForHumans() }}</flux:table.cell>
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
