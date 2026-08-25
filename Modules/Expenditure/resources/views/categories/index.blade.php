<x-layouts::app :title="__('Expenditure Categories')">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <flux:button href="{{ route('expenditure.index') }}" icon="arrow-left" variant="ghost">
                Back to Expenditure
            </flux:button>

            @can('create expenditure')
                @if ($categories->isEmpty())
                    <form action="{{ route('expenditure.categories.defaults') }}" method="POST">
                        @csrf
                        <flux:button type="submit" icon="sparkles" variant="primary">
                            Load the standard categories
                        </flux:button>
                    </form>
                @else
                    <form action="{{ route('expenditure.categories.defaults') }}" method="POST">
                        @csrf
                        <flux:button type="submit" icon="sparkles" variant="ghost">
                            Add any missing standard categories
                        </flux:button>
                    </form>
                @endif
            @endcan
        </div>

        <flux:card class="mb-6">
            <flux:heading size="lg" class="mb-4">Categories</flux:heading>

            @if ($categories->isEmpty())
                <flux:text class="text-zinc-500">
                    No categories yet. Load the standard set above, or add your own below - spending can also be
                    recorded without one and filed later.
                </flux:text>
            @else
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Name</flux:table.column>
                        <flux:table.column>Description</flux:table.column>
                        <flux:table.column>Records</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column>Actions</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($categories as $category)
                            <flux:table.row>
                                <flux:table.cell>{{ $category->name }}</flux:table.cell>
                                <flux:table.cell>{{ $category->description ?? '—' }}</flux:table.cell>
                                <flux:table.cell>{{ $category->expenditures_count }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge :color="$category->is_active ? 'emerald' : 'zinc'">
                                        {{ $category->is_active ? 'Active' : 'Retired' }}
                                    </flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>
                                    @can('edit expenditure')
                                        <form action="{{ route('expenditure.categories.update', $category->id) }}"
                                            method="POST" class="flex flex-wrap items-end gap-2">
                                            @csrf
                                            @method('PUT')
                                            <flux:input type="text" name="name" value="{{ $category->name }}"
                                                size="sm" required />
                                            <flux:input type="text" name="description"
                                                value="{{ $category->description }}" size="sm" />
                                            <flux:checkbox name="is_active" value="1"
                                                :checked="$category->is_active" label="Active" />
                                            <flux:button type="submit" size="sm" icon="check"
                                                variant="primary">save</flux:button>
                                        </form>
                                    @endcan
                                    @can('delete expenditure')
                                        <form action="{{ route('expenditure.categories.destroy', $category->id) }}"
                                            method="POST" class="mt-2"
                                            onsubmit="return confirm('Remove this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" size="sm" icon="trash"
                                                variant="ghost">delete</flux:button>
                                        </form>
                                    @endcan
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif
        </flux:card>

        @can('create expenditure')
            <flux:card>
                <flux:heading size="lg" class="mb-4">Add a Category</flux:heading>

                <form action="{{ route('expenditure.categories.store') }}" method="POST"
                    class="grid grid-cols-1 items-end gap-3 md:grid-cols-3">
                    @csrf
                    <flux:input type="text" name="name" label="Name" placeholder="e.g. Boarding Supplies" required />
                    <flux:input type="text" name="description" label="Description"
                        placeholder="What belongs under this heading" />
                    <flux:button type="submit" variant="primary" icon="plus">Add Category</flux:button>
                </form>
            </flux:card>
        @endcan
    </div>
</x-layouts::app>
