<x-layouts::app :title="__(config('examinations.name'))">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-2 flex flex-row justify-between">
            @can('create examination')
                <flux:button href="{{ route('examinations.create') }}">Add Examination</flux:button>
            @endcan
        </div>

        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Title</flux:table.column>
                    <flux:table.column>Subject</flux:table.column>
                    <flux:table.column>Class</flux:table.column>
                    <flux:table.column>Institution</flux:table.column>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>Marks</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($examinations as $examination)
                        <flux:table.row>
                            <flux:table.cell>{{ $examination->title }}</flux:table.cell>
                            <flux:table.cell>{{ $examination->subject }}</flux:table.cell>
                            <flux:table.cell>{{ $examination->class_name }}</flux:table.cell>
                            <flux:table.cell>{{ $examination->institution?->name }}</flux:table.cell>
                            <flux:table.cell>{{ $examination->exam_date?->format('d M Y') }}</flux:table.cell>
                            <flux:table.cell>
                                {{ $examination->passing_marks ?? '—' }} / {{ $examination->total_marks }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button href="{{ route('examinations.show', $examination->id) }}"
                                    icon="eye" variant="primary" color="emerald">view</flux:button>
                                @can('edit examination')
                                    <flux:button href="{{ route('examinations.edit', $examination->id) }}"
                                        icon="pencil" variant="primary" color="yellow">edit</flux:button>
                                @endcan
                                @can('delete examination')
                                    <form action="{{ route('examinations.destroy', $examination->id) }}"
                                        method="POST" class="inline"
                                        onsubmit="return confirm('Remove this examination?');">
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
                                No examinations found.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</x-layouts::app>
