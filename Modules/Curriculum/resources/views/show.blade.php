<x-layouts::app :title="$curriculum->name">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg flex items-center justify-between">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">{{ $curriculum->name }}</h4>
                <flux:badge :color="$curriculum->status === 'active' ? 'emerald' : 'zinc'">
                    {{ ucfirst($curriculum->status) }}
                </flux:badge>
            </div>

            <div class="p-6">
                <h5 class="text-md font-semibold text-gray-800 mb-3">Schools Using This Curriculum</h5>
                @if ($curriculum->institutions->isEmpty())
                    <p class="text-sm text-gray-500">No institutions are using this curriculum yet.</p>
                @else
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Name</flux:table.column>
                            <flux:table.column>Type</flux:table.column>
                            <flux:table.column>Status</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($curriculum->institutions as $institution)
                                <flux:table.row>
                                    <flux:table.cell>{{ $institution->name }}</flux:table.cell>
                                    <flux:table.cell>{{ $institution->type }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge :color="$institution->is_active ? 'emerald' : 'zinc'">
                                            {{ ucfirst($institution->status ?? 'unknown') }}
                                        </flux:badge>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                @endif
            </div>

            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                <a href="{{ route('curriculum.index') }}"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Back to List
                </a>
                @can('edit curriculum')
                    <a href="{{ route('curriculum.edit', $curriculum->id) }}"
                        class="px-4 py-2 bg-yellow-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-yellow-600">
                        Edit
                    </a>
                @endcan
                @can('delete curriculum')
                    <form action="{{ route('curriculum.destroy', $curriculum->id) }}" method="POST"
                        onsubmit="return confirm('Delete this curriculum?');">
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
