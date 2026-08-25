<x-layouts::app :title="__('Teacher Details')">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-0">{{ $teacherDetails->teacher?->name }}</h4>
                    <small class="text-sm text-gray-500">{{ $teacherDetails->teacher?->email }}</small>
                </div>
                <flux:badge :color="$teacherDetails->is_active ? 'emerald' : 'red'">
                    {{ ucfirst(str_replace('_', ' ', $teacherDetails->status)) }}
                </flux:badge>
            </div>

            <div class="p-6">
                <h5 class="text-md font-semibold text-gray-800 mb-3">Employment Details</h5>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Institution</p>
                        <p class="text-sm text-gray-900">{{ $teacherDetails->institution?->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Employee Number</p>
                        <p class="text-sm text-gray-900">{{ $teacherDetails->employee_number ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Department / Subject</p>
                        <p class="text-sm text-gray-900">{{ $teacherDetails->department ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Qualification</p>
                        <p class="text-sm text-gray-900">{{ $teacherDetails->qualification ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Hire Date</p>
                        <p class="text-sm text-gray-900">{{ $teacherDetails->hire_date?->format('d M Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Phone</p>
                        <p class="text-sm text-gray-900">{{ $teacherDetails->phone ?? '—' }}</p>
                    </div>
                </div>

                @if ($teacherDetails->address)
                    <h5 class="text-md font-semibold text-gray-800 mb-2">Address</h5>
                    <p class="text-sm text-gray-700 mb-6">{{ $teacherDetails->address }}</p>
                @endif

                @if ($teacherDetails->notes)
                    <h5 class="text-md font-semibold text-gray-800 mb-2">Notes</h5>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $teacherDetails->notes }}</p>
                @endif
            </div>

            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                <a href="{{ route('teacher.index') }}"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50" wire:navigate>
                    Back to List
                </a>
                @can('edit teacher')
                    <a href="{{ route('teacher.edit', $teacherDetails->teacher_id) }}"
                        class="px-4 py-2 bg-yellow-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-yellow-600" wire:navigate>
                        Edit
                    </a>
                @endcan
                @can('delete teacher')
                    <form action="{{ route('teacher.destroy', $teacherDetails->teacher_id) }}" method="POST"
                        onsubmit="return confirm('Remove this teacher?');">
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
