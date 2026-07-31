<x-layouts::app :title="__(config('curriculum.name'))">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 flex flex-row justify-between">
            @can('create curriculum')
                <flux:button href="{{ route('curriculum.create') }}" icon="plus">Add Curriculum</flux:button>
            @endcan
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem;">
            @forelse ($curricula as $curriculum)
                <flux:card class="relative overflow-hidden transition-all duration-200 hover:shadow-lg">
                    <a href="{{ route('curriculum.show', $curriculum->id) }}" class="block">
                        <flux:heading>{{ $curriculum->name }}</flux:heading>
                        <flux:badge :color="$curriculum->status === 'active' ? 'emerald' : 'zinc'" class="mt-2">
                            {{ ucfirst($curriculum->status) }}
                        </flux:badge>
                    </a>

                    <div class="mt-4 flex flex-row justify-between">
                        @can('edit curriculum')
                            <flux:button variant="primary" href="{{ route('curriculum.edit', $curriculum->id) }}"
                                class="bg-blue-500" icon="pencil-square"> Edit</flux:button>
                        @endcan
                        @can('delete curriculum')
                            <form action="{{ route('curriculum.destroy', $curriculum->id) }}" method="POST"
                                onsubmit="return confirm('Delete this curriculum?');">
                                @csrf
                                @method('DELETE')
                                <flux:button type="submit" variant="primary" class="bg-red-500" color="red"
                                    icon="trash">
                                    Delete</flux:button>
                            </form>
                        @endcan
                    </div>
                </flux:card>
            @empty
                <flux:text class="text-zinc-500">No curricula found.</flux:text>
            @endforelse
        </div>
    </div>
</x-layouts::app>
