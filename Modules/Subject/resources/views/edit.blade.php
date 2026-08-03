<x-layouts::app :title="__('Edit Subject')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">Edit Subject</h4>
                <small class="text-sm text-gray-500">{{ $subject->name }}</small>
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

            <form action="{{ route('subject.update', $subject->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select name="institution_id" label="Institution">
                        @foreach ($institutions as $institution)
                            <flux:select.option value="{{ $institution->id }}"
                                :selected="old('institution_id', $subject->institution_id) == $institution->id">
                                {{ $institution->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input type="text" name="name" label="Subject Name"
                        value="{{ old('name', $subject->name) }}" required />

                    <flux:input type="text" name="code" label="Code" value="{{ old('code', $subject->code) }}" />

                    <flux:checkbox name="is_compulsory" value="1" label="Compulsory"
                        description="Every student takes this subject automatically"
                        :checked="old('is_compulsory', $subject->is_compulsory)" />

                    <flux:checkbox name="is_active" value="1" label="Active"
                        :checked="old('is_active', $subject->is_active)" />
                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                    <a href="{{ route('subject.show', $subject->id) }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <flux:button variant="primary" type="submit">Save Changes</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
