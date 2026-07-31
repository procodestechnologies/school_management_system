<x-layouts::app :title="__('Edit Teacher')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">Edit Teacher</h4>
                <small class="text-sm text-gray-500">{{ $teacherDetails->teacher?->name }}</small>
            </div>

            @if ($errors->any())
                <div class="mx-6 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p class="font-medium">{{ $errors->count() }} error(s) prevented these changes from being saved:
                    </p>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('teacher.update', $teacherDetails->teacher_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-6">
                    <h5 class="text-md font-semibold text-gray-800 mb-3">Account Information</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <flux:input type="text" name="name" label="Full Name"
                            value="{{ old('name', $teacherDetails->teacher?->name) }}" required />
                        <flux:input type="email" name="email" label="Email"
                            value="{{ old('email', $teacherDetails->teacher?->email) }}" required />
                    </div>

                    <h5 class="text-md font-semibold text-gray-800 mb-3">Employment Details</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <flux:select name="institution_id" label="Institution">
                            @foreach ($institutions as $institution)
                                <flux:select.option value="{{ $institution->id }}"
                                    :selected="old('institution_id', $teacherDetails->institution_id) == $institution->id">
                                    {{ $institution->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:input type="text" name="employee_number" label="Employee Number"
                            value="{{ old('employee_number', $teacherDetails->employee_number) }}" />
                        <flux:input type="text" name="department" label="Department / Subject"
                            value="{{ old('department', $teacherDetails->department) }}" />
                        <flux:input type="text" name="qualification" label="Qualification"
                            value="{{ old('qualification', $teacherDetails->qualification) }}" />
                        <flux:input type="date" name="hire_date" label="Hire Date"
                            value="{{ old('hire_date', $teacherDetails->hire_date?->format('Y-m-d')) }}" />
                        <flux:input type="text" name="phone" label="Phone"
                            value="{{ old('phone', $teacherDetails->phone) }}" />
                        <flux:select name="status" label="Status">
                            @foreach (['active', 'on_leave', 'suspended', 'resigned', 'terminated'] as $status)
                                <flux:select.option value="{{ $status }}"
                                    :selected="old('status', $teacherDetails->status) === $status">
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:checkbox name="is_active" value="1" label="Active"
                            :checked="old('is_active', $teacherDetails->is_active)" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 mb-6">
                        <flux:textarea name="address" rows="2" label="Address">{{ old('address', $teacherDetails->address) }}</flux:textarea>
                        <flux:textarea name="notes" rows="2" label="Notes">{{ old('notes', $teacherDetails->notes) }}</flux:textarea>
                    </div>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                    <a href="{{ route('teacher.show', $teacherDetails->teacher_id) }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <flux:button variant="primary" type="submit">Save Changes</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
