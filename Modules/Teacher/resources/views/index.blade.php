<x-layouts::app :title="__(config('teacher.name'))">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-2 flex flex-row justify-between">
            @can('create teacher')
                <flux:button href="{{ route('teacher.create') }}">Add Teacher</flux:button>
            @endcan
        </div>

        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Employee No.</flux:table.column>
                    <flux:table.column>Department</flux:table.column>
                    <flux:table.column>Institution</flux:table.column>
                    <flux:table.column>Phone</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($teachers as $teacherDetails)
                        <flux:table.row>
                            <flux:table.cell>{{ $teacherDetails->teacher?->name }}</flux:table.cell>
                            <flux:table.cell>{{ $teacherDetails->employee_number ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $teacherDetails->department ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $teacherDetails->institution?->name }}</flux:table.cell>
                            <flux:table.cell>{{ $teacherDetails->phone }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$teacherDetails->is_active ? 'emerald' : 'red'">
                                    {{ ucfirst(str_replace('_', ' ', $teacherDetails->status)) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button href="{{ route('teacher.show', $teacherDetails->teacher_id) }}"
                                    icon="eye" variant="primary" color="emerald">
                                    view
                                </flux:button>
                                @can('edit teacher')
                                    <flux:button href="{{ route('teacher.edit', $teacherDetails->teacher_id) }}"
                                        icon="pencil" variant="primary" color="yellow">
                                        edit
                                    </flux:button>
                                @endcan
                                @can('delete teacher')
                                    <form action="{{ route('teacher.destroy', $teacherDetails->teacher_id) }}"
                                        method="POST" class="inline" onsubmit="return confirm('Remove this teacher?');">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" icon="trash" variant="primary" color="red">
                                            delete
                                        </flux:button>
                                    </form>
                                @endcan
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
            <div class="mt-4">
                {{ $teachers->links() }}
            </div>
        </flux:card>
    </div>
</x-layouts::app>
