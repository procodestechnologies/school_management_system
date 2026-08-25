<x-layouts::app :title="__('Subject Details')">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">{{ $subject->name }}</h4>
                <small class="text-sm text-gray-500">{{ $subject->institution?->name }}</small>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Code</p>
                        <p class="text-sm text-gray-900">{{ $subject->code ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Type</p>
                        <p class="text-sm">
                            <flux:badge :color="$subject->is_compulsory ? 'amber' : 'zinc'">
                                {{ $subject->is_compulsory ? 'Compulsory' : 'Optional' }}
                            </flux:badge>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Status</p>
                        <p class="text-sm">
                            <flux:badge :color="$subject->is_active ? 'emerald' : 'zinc'">
                                {{ $subject->is_active ? 'Active' : 'Inactive' }}
                            </flux:badge>
                        </p>
                    </div>
                </div>

                <h5 class="text-md font-semibold text-gray-800 mb-3">
                    Examinations ({{ $subject->examinations->count() }})
                </h5>
                @if ($subject->examinations->isEmpty())
                    <p class="text-sm text-gray-500">No examinations linked to this subject yet.</p>
                @else
                    <flux:table>
                        <flux:table.columns>
                            <x-sortable-column column="title">Title</x-sortable-column>
                            <x-sortable-column column="exam_date">Date</x-sortable-column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($subject->examinations as $examination)
                                <flux:table.row>
                                    <flux:table.cell>{{ $examination->title }}</flux:table.cell>
                                    <flux:table.cell>{{ $examination->exam_date?->format('d M Y') }}</flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                @endif
            </div>

            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                <a href="{{ route('subject.index') }}"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50" wire:navigate>
                    Back to List
                </a>
                @can('edit subject')
                    <a href="{{ route('subject.edit', $subject->id) }}"
                        class="px-4 py-2 bg-yellow-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-yellow-600" wire:navigate>
                        Edit
                    </a>
                @endcan
                @can('delete subject')
                    <form action="{{ route('subject.destroy', $subject->id) }}" method="POST"
                        onsubmit="return confirm('Remove this subject?');">
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
