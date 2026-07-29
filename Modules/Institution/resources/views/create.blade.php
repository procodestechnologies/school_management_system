<x-layouts::app :title="__('Institution')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">Create Institution</h4>
                <small class="text-sm text-gray-500">
                    Enter the institution's details below.
                </small>
            </div>

            <form action="{{ route('institution.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="p-6">

                    {{-- Basic Information --}}
                    <h5 class="text-md font-semibold text-gray-800 mb-3">Basic Information</h5>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                        <div>
                            <flux:input type="text" name="name" label="Institution Name"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('name') }}" required />
                        </div>

                        <div class="md:col-span-1">
                            <flux:input type="text" name="code" label="Institution Code"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('code') }}" required />
                        </div>

                        <div>
                            <flux:select name="curriculum" label="Curriculum"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach ($curricula as $curriculum)
                                    <flux:select.option value="{{ $curriculum->id }}">{{ $curriculum->name }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                        <div>
                            <flux:select name="type" label="Institution Type"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <flux:select.option value="School">School</flux:select.option>
                                <flux:select.option value="College">College</flux:select.option>
                                <flux:select.option value="University">University</flux:select.option>
                                <flux:select.option value="Training Centre">Training Centre</flux:select.option>
                            </flux:select>
                        </div>

                    </div>

                    {{-- Contact --}}
                    <h5 class="text-md font-semibold text-gray-800 mb-3">Contact Information</h5>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

                        <div>
                            {{-- <label class="block text-sm font-medium text-gray-700 mb-1">Email</label> --}}
                            <flux:input label="Email" type="email" name="email"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('email') }}" />
                        </div>

                        <div>
                            <flux:input type="text" name="phone" label="Phone"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('phone') }}" />
                        </div>

                        <div>
                            <flux:input type="text" name="alternate_phone" label="Alternative Phone"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('alternate_phone') }}" />
                        </div>

                        <div class="md:col-span-3">
                            <flux:input type="url" name="website" label="Website"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('website') }}" />
                        </div>

                    </div>

                    {{-- Address --}}
                    <h5 class="text-md font-semibold text-gray-800 mb-3">Address</h5>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

                        <div>
                            <flux:input type="text" name="country" readonly label="Country"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('country', 'Kenya') }}" />
                        </div>

                        <div>
                            <flux:input type="text" name="county" label="County"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('county') }}" />
                        </div>

                        <div>
                            <flux:input type="text" name="city" label="City"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('city') }}" />
                        </div>

                        <div>
                            <flux:input type="text" name="postal_address" label="Postal Address"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('postal_address') }}" />
                        </div>

                        <div class="md:col-span-4">
                            <flux:textarea name="physical_address" rows="3" label="Physical Address"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                {{ old('physical_address') }}</textarea>
                        </div>

                    </div>

                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">

                    <a href="{{ route('institution.index') }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>

                    <flux:button variant="primary" type="submit"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Save Institution
                    </flux:button>

                </div>

            </form>

        </div>

    </div>
</x-layouts::app>
