<x-layouts::app :title="__('Add Staff')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">Add Staff Member</h4>
            </div>

            @if ($errors->any())
                <div class="mx-6 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p class="font-medium">{{ $errors->count() }} error(s) prevented this staff member from being saved:
                    </p>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('staff.store') }}" method="POST">
                @csrf

                <div class="p-6">
                    <h5 class="text-md font-semibold text-gray-800 mb-3">Personal Information</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <flux:input type="text" name="name" label="Full Name" value="{{ old('name') }}"
                            required />
                        <flux:input type="email" name="email" label="Email" value="{{ old('email') }}" />
                        <flux:input type="text" name="phone" label="Phone" value="{{ old('phone') }}" />
                    </div>

                    <h5 class="text-md font-semibold text-gray-800 mb-3">Employment Details</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <flux:select name="institution_id" label="Institution">
                            <flux:select.option value="">Select Institution</flux:select.option>
                            @foreach ($institutions as $institution)
                                <flux:select.option value="{{ $institution->id }}"
                                    :selected="old('institution_id') == $institution->id">
                                    {{ $institution->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:input type="text" name="staff_number" label="Staff Number"
                            value="{{ old('staff_number') }}" />
                        <flux:input type="text" name="job_title" label="Job Title"
                            value="{{ old('job_title') }}" />
                        <flux:input type="text" name="department" label="Department"
                            value="{{ old('department') }}" />
                        <flux:select name="employment_type" label="Employment Type">
                            @foreach (['full_time', 'part_time', 'contract', 'volunteer'] as $type)
                                <flux:select.option value="{{ $type }}"
                                    :selected="old('employment_type', 'full_time') === $type">
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:input type="date" name="hire_date" label="Hire Date"
                            value="{{ old('hire_date') }}" />
                        <flux:input type="number" step="0.01" min="0" name="salary"
                            label="Monthly Salary" value="{{ old('salary') }}" />
                        <flux:select name="status" label="Status">
                            @foreach (['active', 'on_leave', 'suspended', 'resigned', 'terminated'] as $status)
                                <flux:select.option value="{{ $status }}"
                                    :selected="old('status', 'active') === $status">
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:checkbox name="is_active" value="1" label="Active" checked />
                    </div>

                    <h5 class="text-md font-semibold text-gray-800 mb-1">System Access</h5>
                    <p class="text-sm text-gray-500 mb-3">
                        Optional. Tick this to give this staff member a login. An Accountant can manage payroll and fee
                        management only.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <flux:checkbox name="create_account" value="1" label="Give this staff member a login"
                            :checked="(bool) old('create_account')" />
                        <flux:select name="system_role" label="Role">
                            @foreach ($systemRoles as $role)
                                <flux:select.option value="{{ $role }}" :selected="old('system_role') === $role">
                                    {{ $role }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:input type="password" name="password" label="Password"
                            description="Required when creating a login." />
                    </div>

                    <div class="grid grid-cols-1 gap-4 mb-6">
                        <flux:textarea name="address" rows="2" label="Address">{{ old('address') }}</flux:textarea>
                        <flux:textarea name="notes" rows="2" label="Notes">{{ old('notes') }}</flux:textarea>
                    </div>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                    <a href="{{ route('staff.index') }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <flux:button variant="primary" type="submit">Save Staff Member</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
