<x-layouts::app>
    <div class="p-4">
        <form action="{{ route('curriculum.store') }}" method="POST">
            @method('POST')
            @csrf
            <flux:input label="Name" name="name" class="mb-2" placeholder="e.g CBC/8.4.4" />
            <flux:button type="submit" icon="plus">create</flux:button>
        </form>
    </div>
</x-layouts::app>
