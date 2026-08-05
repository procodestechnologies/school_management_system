<x-layouts::app :title="__('Parent Details')">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold">
                        {{ $parent->initials() }}
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-0">{{ $parent->name }}</h4>
                        <small class="text-sm text-gray-500">{{ $parent->email }}</small>
                    </div>
                </div>
                <flux:badge :color="$parent->children->isNotEmpty() ? 'emerald' : 'zinc'">
                    {{ $parent->children->isNotEmpty() ? 'Active' : 'No children linked' }}
                </flux:badge>
            </div>

            <div class="p-6">
                <h5 class="text-md font-semibold text-gray-800 mb-3">Contact</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Phone</p>
                        <p class="text-sm text-gray-900">{{ $parent->parent?->parent_phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Occupation</p>
                        <p class="text-sm text-gray-900">{{ $parent->parent?->parent_occupation ?? '—' }}</p>
                    </div>
                </div>

                <h5 class="text-md font-semibold text-gray-800 mb-3">Children</h5>
                @if ($parent->children->isEmpty())
                    <p class="text-sm text-gray-500">No students linked to this parent yet.</p>
                @else
                    <flux:table>
                        <flux:table.columns>
                            <x-sortable-column column="name">Name</x-sortable-column>
                            <x-sortable-column column="admission_number">Admission No.</x-sortable-column>
                            <x-sortable-column column="institution">Institution</x-sortable-column>
                            <flux:table.column>Status</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($parent->children as $child)
                                <flux:table.row>
                                    <flux:table.cell>{{ $child->student?->name }}</flux:table.cell>
                                    <flux:table.cell>{{ $child->admission_number }}</flux:table.cell>
                                    <flux:table.cell>{{ $child->institution?->name }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge :color="$child->is_active ? 'emerald' : 'zinc'">
                                            {{ ucfirst($child->enrollment_status) }}
                                        </flux:badge>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                @endif
            </div>

            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                <a href="{{ route('parent.index') }}"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Back to List
                </a>

                @can('edit parent')
                    <a href="{{ route('parent.edit', $parent->id) }}"
                        class="px-4 py-2 bg-yellow-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        Edit
                    </a>
                @endcan

                @can('delete parent')
                    <form action="{{ route('parent.destroy', $parent->id) }}" method="POST"
                        onsubmit="return confirm('Remove this parent? Linked students will be unlinked, not deleted.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Delete
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</x-layouts::app>
