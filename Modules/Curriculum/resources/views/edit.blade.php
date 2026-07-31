<x-layouts::app :title="__('Edit Curriculum')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">Edit Curriculum</h4>
            </div>

            @if ($errors->any())
                <div class="mx-6 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('curriculum.update', $curriculum->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input label="Name" value="{{ old('name', $curriculum->name) }}" name="name"
                        placeholder="e.g CBC/8.4.4" required />

                    <flux:select name="status" label="Status">
                        <flux:select.option value="active"
                            :selected="old('status', $curriculum->status) === 'active'">Active</flux:select.option>
                        <flux:select.option value="dismissed"
                            :selected="old('status', $curriculum->status) === 'dismissed'">Dismissed
                        </flux:select.option>
                    </flux:select>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                    <a href="{{ route('curriculum.show', $curriculum->id) }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <flux:button type="submit" icon="check" variant="primary">Update</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
