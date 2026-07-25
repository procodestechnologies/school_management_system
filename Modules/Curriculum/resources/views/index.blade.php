<x-layouts::app :title="__(config('curriculum.name'))">
    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem;">
        @foreach ($curricula as $curriculum)
            <flux:card class="relative overflow-hidden transition-all duration-200 hover:shadow-lg cursor-pointer">
                <flux:heading>{{ $curriculum->name }}</flux:heading>

                <div class="mt-4 flex flex-row justify-between">
                    <flux:button variant="primary" href="{{ route('curriculum.edit', $curriculum->id) }}"
                        class="bg-blue-500" icon="pencil-square"> Edit</flux:button>
                    <form action="{{ route('curriculum.destroy', $curriculum->id) }}" method="POST">
                        @method('DELETE')
                        <flux:button type="submit" variant="primary" class="bg-red-500" color="red" icon="trash">
                            Delete</flux:button>
                    </form>
                </div>
            </flux:card>
        @endforeach
    </div>
</x-layouts::app>
