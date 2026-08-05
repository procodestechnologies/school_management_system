<x-layouts::app :title="__(config('subject.name'))">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-2 flex flex-row justify-between">
            @can('create subject')
                <flux:button href="{{ route('subject.create') }}">Add Subject</flux:button>
            @endcan
        </div>

        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <x-sortable-column column="name">Name</x-sortable-column>
                    <x-sortable-column column="code">Code</x-sortable-column>
                    <flux:table.column>Institution</flux:table.column>
                    <flux:table.column>Type</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($subjects as $subject)
                        <flux:table.row>
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
                                    variant="primary" color="emerald">view</flux:button>
                                @can('edit subject')
                                    <flux:button href="{{ route('subject.edit', $subject->id) }}" icon="pencil"
                                        variant="primary" color="yellow">edit</flux:button>
                                @endcan
                                @can('delete subject')
                                    <form action="{{ route('subject.destroy', $subject->id) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Remove this subject?');">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" icon="trash" variant="primary" color="red">delete
                                        </flux:button>
                                    </form>
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
            {{ $subjects->links() }}
        </div>
    </div>
</x-layouts::app>
