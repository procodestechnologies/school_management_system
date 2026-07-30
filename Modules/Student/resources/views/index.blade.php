<x-layouts::app :title="__(config('student.name'))">
    <div class="p-4">
        <div class="mb-2 flex flex-row justify-between">
            <flux:button href="{{ route('student.create') }}">Add Student</flux:button>
        </div>

        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Admission No.</flux:table.column>
                    <flux:table.column>Gender</flux:table.column>
                    <flux:table.column>Institution</flux:table.column>
                    <flux:table.column>Phone</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($students as $student)
                        <flux:table.row>
                            <flux:table.cell>{{ $student->student?->name }}</flux:table.cell>
                            <flux:table.cell>{{ $student->admission_number }}</flux:table.cell>
                            <flux:table.cell>{{ ucfirst($student->gender ?? '') }}</flux:table.cell>
                            <flux:table.cell>{{ $student->institution?->name }}</flux:table.cell>
                            <flux:table.cell>{{ $student->phone }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$student->is_active ? 'emerald' : 'red'">
                                    {{ ucfirst($student->enrollment_status) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button href="{{ route('student.show', $student->student_id) }}" icon="eye"
                                    variant="primary" color="emerald">
                                    view
                                </flux:button>
                                <flux:button href="{{ route('student.edit', $student->student_id) }}" icon="pencil"
                                    variant="primary" color="yellow">
                                    edit
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7" class="text-center text-gray-500">
                                No students found.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</x-layouts::app>
