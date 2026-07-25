<x-layouts::app>
    <div class="p-4">
        <form action="{{ route('curriculum.update', $curriculum->id) }}" method="POST">
            @method('PATCH')
            @csrf
            <flux:input label="Name" value="{{ $curriculum->name }}" name="name" class="mb-2"
                placeholder="e.g CBC/8.4.4" />
            <flux:button type="submit" icon="plus">Update</flux:button>
        </form>
    </div>
</x-layouts::app>
