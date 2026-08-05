<x-layouts::app :title="__('Create Student')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">Create Student</h4>
                <small class="text-sm text-gray-500">
                    Enter the student's, parent's and guardian's details below.
                </small>
            </div>

            <form action="{{ route('student.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="p-6">

                    {{-- Account Information --}}
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

                    {{-- Personal Information --}}
                    <h5 class="text-md font-semibold text-gray-800 mb-3">Personal Information</h5>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

                        <div>
                            <flux:input type="text" name="phone" label="Phone"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('phone') }}" />
                        </div>

                        <div>
                            <flux:input type="date" name="date_of_birth" label="Date of Birth"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('date_of_birth') }}" />
                        </div>

                        <div>
                            <flux:select name="gender" label="Gender"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <flux:select.option value="">Select Gender</flux:select.option>
                                <flux:select.option value="male">Male</flux:select.option>
                                <flux:select.option value="female">Female</flux:select.option>
                                <flux:select.option value="other">Other</flux:select.option>
                            </flux:select>
                        </div>

                        <div>
                            <flux:input type="file" name="profile_image" label="Profile Photo"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>

                        <div>
                            <flux:input type="text" name="admission_number" label="Admission Number"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('admission_number') }}" />
                        </div>

                        <div>
                            <flux:input type="text" name="student_number" label="Student Number"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('student_number') }}" />
                        </div>

                        <div>
                            <flux:select name="institution_id" label="Institution"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <flux:select.option value="">Select Institution</flux:select.option>
                                @foreach ($institutions as $institution)
                                    <flux:select.option value="{{ $institution->id }}">{{ $institution->name }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        <div>
                            <flux:select name="enrollment_status" label="Enrollment Status"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <flux:select.option value="active">Active</flux:select.option>
                                <flux:select.option value="transferred">Transferred</flux:select.option>
                                <flux:select.option value="graduated">Graduated</flux:select.option>
                                <flux:select.option value="dropped">Dropped</flux:select.option>
                                <flux:select.option value="suspended">Suspended</flux:select.option>
                                <flux:select.option value="expelled">Expelled</flux:select.option>
                                <flux:select.option value="withdrawn">Withdrawn</flux:select.option>

                            </flux:select>
                        </div>

                    </div>

                    {{-- Address --}}
                    <h5 class="text-md font-semibold text-gray-800 mb-3">Address</h5>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

                        <div class="md:col-span-3">
                            <flux:textarea name="address" rows="2" label="Address"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                {{ old('address') }}</flux:textarea>
                        </div>

                        <div>
                            <flux:input type="text" name="city" label="City"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('city') }}" />
                        </div>

                        <div>
                            <flux:input type="text" name="state" label="State"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('state') }}" />
                        </div>

                        <div>
                            <flux:input type="text" name="country" label="Country"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('country', 'Kenya') }}" />
                        </div>

                    </div>

                    {{-- Parent Details --}}
                    <h5 class="text-md font-semibold text-gray-800 mb-3">Parent Details</h5>

                    <div class="mb-4">
                        <span class="text-sm font-medium text-gray-700 block mb-2">A parent can have more than one
                            student - link an existing parent account, or add a new one.</span>
                        <div class="flex gap-6">
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="parent_mode" value="new" id="parent_mode_new"
                                    {{ old('parent_mode', old('parent_id') ? 'existing' : 'new') === 'new' ? 'checked' : '' }}
                                    class="text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700">Add new parent</span>
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="parent_mode" value="existing" id="parent_mode_existing"
                                    {{ old('parent_mode', old('parent_id') ? 'existing' : 'new') === 'existing' ? 'checked' : '' }}
                                    class="text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700">Link existing parent</span>
                            </label>
                        </div>
                    </div>

                    <div id="existing-parent-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 hidden">
                        <div>
                            <flux:select name="parent_id" label="Select Parent"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <flux:select.option value="">Select Parent</flux:select.option>
                                @foreach ($existingParents as $existingParent)
                                    <flux:select.option value="{{ $existingParent->id }}"
                                        data-institution-ids="{{ $existingParent->children->pluck('institution_id')->unique()->implode(' ') }}"
                                        :selected="old('parent_id') == $existingParent->id">
                                        {{ $existingParent->name }} ({{ $existingParent->email }}) &mdash;
                                        {{ $existingParent->children->count() }}
                                        {{ $existingParent->children->count() === 1 ? 'child' : 'children' }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="parent_id" class="mt-1 text-sm text-red-600" />
                        </div>
                    </div>

                    <div id="new-parent-fields" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

                        <div>
                            <flux:input type="text" name="parent_name" label="Parent Name"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('parent_name') }}" />
                        </div>

                        <div>
                            <flux:input type="text" name="parent_phone" label="Parent Phone"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('parent_phone') }}" />
                        </div>

                        <div>
                            <flux:input type="email" name="parent_email" label="Parent Email"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('parent_email') }}" />
                        </div>

                        <div>
                            <flux:input type="text" name="parent_occupation" label="Parent Occupation"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('parent_occupation') }}" />
                        </div>

                    </div>

                    {{-- Guardian Details --}}
                    <h5 class="text-md font-semibold text-gray-800 mb-3">Guardian Details <span
                            class="text-xs font-normal text-gray-400">(if different from parent)</span></h5>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

                        <div>
                            <flux:input type="text" name="guardian_name" label="Guardian Name"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('guardian_name') }}" />
                        </div>

                        <div>
                            <flux:input type="text" name="guardian_phone" label="Guardian Phone"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('guardian_phone') }}" />
                        </div>

                        <div>
                            <flux:input type="email" name="guardian_email" label="Guardian Email"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('guardian_email') }}" />
                        </div>

                        <div>
                            <flux:input type="text" name="guardian_relationship" label="Relationship"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('guardian_relationship') }}" />
                        </div>

                    </div>

                    {{-- Additional Information --}}
                    <h5 class="text-md font-semibold text-gray-800 mb-3">Additional Information</h5>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

                        <div>
                            <flux:textarea name="medical_conditions" rows="2" label="Medical Conditions"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                {{ old('medical_conditions') }}</flux:textarea>
                        </div>

                        <div>
                            <flux:textarea name="allergies" rows="2" label="Allergies"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                {{ old('allergies') }}</flux:textarea>
                        </div>

                        <div>
                            <flux:textarea name="special_needs" rows="2" label="Special Needs"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                {{ old('special_needs') }}</flux:textarea>
                        </div>

                        <div class="md:col-span-3">
                            <flux:textarea name="notes" rows="2" label="Notes"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                {{ old('notes') }}</flux:textarea>
                        </div>

                        <div>
                            <flux:checkbox name="is_active" value="1" label="Active" checked />
                        </div>

                    </div>

                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">

                    <a href="{{ route('student.index') }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>

                    <flux:button variant="primary" type="submit"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Save Student
                    </flux:button>

                </div>

            </form>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newFields = document.getElementById('new-parent-fields');
            const existingFields = document.getElementById('existing-parent-fields');
            const modeRadios = document.querySelectorAll('input[name="parent_mode"]');
            const institutionSelect = document.getElementById('institution_id') ||
                document.querySelector('select[name="institution_id"]');
            const parentSelect = document.querySelector('select[name="parent_id"]');

            function applyMode() {
                const checked = document.querySelector('input[name="parent_mode"]:checked');
                const mode = checked ? checked.value : 'new';
                newFields.classList.toggle('hidden', mode !== 'new');
                existingFields.classList.toggle('hidden', mode !== 'existing');
            }

            // Hides parent options that don't belong to the selected
            // institution, so a Director can't accidentally link a student
            // to an unrelated parent from a different school.
            function filterParentsByInstitution() {
                if (! institutionSelect || ! parentSelect) {
                    return;
                }

                const institutionId = institutionSelect.value;
                const currentValue = parentSelect.value;

                Array.from(parentSelect.options).forEach(function(option) {
                    if (!option.value || option.value === currentValue) {
                        option.hidden = false;
                        return;
                    }

                    const ids = (option.dataset.institutionIds || '').split(' ').filter(Boolean);
                    option.hidden = institutionId !== '' && ids.length > 0 && !ids.includes(institutionId);
                });
            }

            modeRadios.forEach(radio => radio.addEventListener('change', applyMode));
            institutionSelect?.addEventListener('change', filterParentsByInstitution);

            applyMode();
            filterParentsByInstitution();
        });
    </script>
</x-layouts::app>
