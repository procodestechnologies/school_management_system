<x-layouts::app :title="__('Class Details')">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">{{ $schoolClass->name }}</h4>
                <small class="text-sm text-gray-500">{{ $schoolClass->institution?->name }}</small>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Level / Grade</p>
                        <p class="text-sm text-gray-900">{{ $schoolClass->level ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Class Teacher</p>
                        <p class="text-sm text-gray-900">{{ $schoolClass->classTeacher?->name ?? 'Unassigned' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Capacity</p>
                        <p class="text-sm text-gray-900">{{ $schoolClass->capacity ?? '—' }}</p>
                    </div>
                </div>

                <h5 class="text-md font-semibold text-gray-800 mb-3">Students ({{ $schoolClass->students->count() }})</h5>
                @if ($schoolClass->students->isEmpty())
                    <p class="text-sm text-gray-500">No students assigned to this class yet.</p>
                @else
                    <flux:table>
                        <flux:table.columns>
                            <x-sortable-column column="name">Name</x-sortable-column>
                            <x-sortable-column column="admission_number">Admission No.</x-sortable-column>
                            <flux:table.column>Status</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($schoolClass->students as $studentDetails)
                                <flux:table.row>
                                    <flux:table.cell>{{ $studentDetails->student?->name }}</flux:table.cell>
                                    <flux:table.cell>{{ $studentDetails->admission_number }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge :color="$studentDetails->is_active ? 'emerald' : 'zinc'">
                                            {{ ucfirst($studentDetails->enrollment_status) }}
                                        </flux:badge>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                @endif
            </div>

            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                <a href="{{ route('classes.index') }}"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50" wire:navigate>
                    Back to List
                </a>
                @can('edit classes')
                    <a href="{{ route('classes.edit', $schoolClass->id) }}"
                        class="px-4 py-2 bg-yellow-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-yellow-600" wire:navigate>
                        Edit
                    </a>
                @endcan
                @can('delete classes')
                    <form action="{{ route('classes.destroy', $schoolClass->id) }}" method="POST"
                        onsubmit="return confirm('Remove this class?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</x-layouts::app>
