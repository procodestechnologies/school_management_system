<x-layouts::app :title="__(config('classes.name'))">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-2 flex flex-row justify-between">
            @can('create classes')
                <flux:button href="{{ route('classes.create') }}">Add Class</flux:button>
            @endcan
        </div>

        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Level</flux:table.column>
                    <flux:table.column>Institution</flux:table.column>
                    <flux:table.column>Class Teacher</flux:table.column>
                    <flux:table.column>Students</flux:table.column>
                    <flux:table.column>Capacity</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($classes as $schoolClass)
                        <flux:table.row>
                            <flux:table.cell>{{ $schoolClass->name }}</flux:table.cell>
                            <flux:table.cell>{{ $schoolClass->level ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $schoolClass->institution?->name }}</flux:table.cell>
                            <flux:table.cell>{{ $schoolClass->classTeacher?->name ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $schoolClass->students()->count() }}</flux:table.cell>
                            <flux:table.cell>{{ $schoolClass->capacity ?? '—' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:button href="{{ route('classes.show', $schoolClass->id) }}" icon="eye"
                                    variant="primary" color="emerald">view</flux:button>
                                @can('edit classes')
                                    <flux:button href="{{ route('classes.edit', $schoolClass->id) }}"
                                        icon="pencil" variant="primary" color="yellow">edit</flux:button>
                                @endcan
                                @can('delete classes')
                                    <form action="{{ route('classes.destroy', $schoolClass->id) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Remove this class?');">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" icon="trash" variant="primary"
                                            color="red">delete</flux:button>
                                    </form>
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
</x-layouts::app>
