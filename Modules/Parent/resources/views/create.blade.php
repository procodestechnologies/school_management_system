<x-layouts::app :title="__('Create Parent')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">Create Parent</h4>
                <small class="text-sm text-gray-500">Add a parent/guardian account and optionally link existing
                    students to them.</small>
            </div>

            @if ($errors->any())
                <div class="mx-6 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p class="font-medium">{{ $errors->count() }} error(s) prevented this parent from being saved:</p>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('parent.store') }}" method="POST">
                @csrf

                <div class="p-6">
                    <h5 class="text-md font-semibold text-gray-800 mb-3">Account Information</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <flux:input type="text" name="name" label="Full Name"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('name') }}" required />
                        </div>
                        <div>
                            <flux:input type="email" name="email" label="Email"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('email') }}" required />
                        </div>
                        <div>
                            <flux:input type="password" name="password" label="Password"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required />
                        </div>
                    </div>

                    <h5 class="text-md font-semibold text-gray-800 mb-3">Contact</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <flux:input type="text" name="parent_phone" label="Phone"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('parent_phone') }}" />
                        </div>
                        <div>
                            <flux:input type="text" name="parent_occupation" label="Occupation"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('parent_occupation') }}" />
                        </div>
                    </div>

                    <h5 class="text-md font-semibold text-gray-800 mb-3">Link Children</h5>
                    <p class="text-xs text-gray-500 mb-3">Only students not already linked to a parent are shown.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-64 overflow-y-auto border border-gray-200 rounded-md p-3">
                        @forelse ($availableStudents as $studentDetails)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="children[]" value="{{ $studentDetails->student_id }}"
                                    @checked(collect(old('children'))->contains($studentDetails->student_id))>
                                {{ $studentDetails->student?->name }}
                                @if ($studentDetails->admission_number)
                                    ({{ $studentDetails->admission_number }})
                                @endif
                                @if (!isAdmin())
                                @else
                                    &mdash; {{ $studentDetails->institution?->name }}
                                @endif
                            </label>
                        @empty
                            <p class="text-sm text-gray-500 col-span-2">No unlinked students available.</p>
                        @endforelse
                    </div>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                    <a href="{{ route('parent.index') }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <flux:button variant="primary" type="submit"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Save Parent
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
