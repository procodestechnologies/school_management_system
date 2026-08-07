<x-layouts::app :title="__('Student Details')">
    <div class="p-4">
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">There were {{ $errors->count() }} errors with your
                            submission</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg flex justify-between items-center">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-0">Student Details</h4>
                    <small class="text-sm text-gray-500">
                        View complete information for {{ $student->name ?? 'Student' }}
                    </small>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('student.edit', $student->id) }}"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Edit Student
                    </a>
                    <a href="{{ route('student.index') }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Back to List
                    </a>
                </div>
            </div>

            <div class="p-6">

                {{-- Profile Photo --}}
                @if ($student->studentUserDetails?->profile_photo)
                    <div class="flex justify-center mb-6">
                        <img src="{{ Storage::disk('public')->url($student->studentUserDetails->profile_photo) }}" alt="{{ $student->name }}"
                            class="h-32 w-32 rounded-full object-cover border-4 border-gray-200">
                    </div>
                @endif

                {{-- Account Information --}}
                <h5 class="text-md font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2">Account Information
                </h5>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Full Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $student->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $student->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <p class="mt-1">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $student->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $student->studentUserDetails->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                </div>

                {{-- Personal Information --}}
                <h5 class="text-md font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2">Personal Information
                </h5>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $student->studentUserDetails->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $student->studentUserDetails->date_of_birth ? \Carbon\Carbon::parse($student->studentUserDetails->date_of_birth)->format('M d, Y') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Gender</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ ucfirst($student->studentUserDetails->gender ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Admission Number</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $student->studentUserDetails->admission_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Student ID</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $student->studentUserDetails->student_id ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Institution</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $student->studentInstitution->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Enrollment Status</label>
                        <p class="mt-1 text-sm text-gray-900">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if ($student->studentUserDetails->enrollment_status == 'active') bg-green-100 text-green-800
                                @elseif($student->studentUserDetails->enrollment_status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($student->studentUserDetails->enrollment_status == 'graduated') bg-blue-100 text-blue-800
                                @elseif($student->studentUserDetails->enrollment_status == 'transferred') bg-purple-100 text-purple-800
                                @elseif($student->studentUserDetails->enrollment_status == 'withdrawn') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($student->studentUserDetails->enrollment_status ?? 'N/A') }}
                            </span>
                        </p>
                    </div>
                </div>

                <x-selections::student-subjects :student="$student" />

                {{-- Address --}}
                <h5 class="text-md font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2">Address</h5>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Address</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $student->studentUserDetails->address ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">City</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $student->studentUserDetails->city ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">State</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $student->studentUserDetails->state ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Country</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $student->studentUserDetails->country ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- Parent Details --}}
                <h5 class="text-md font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2">Parent Details</h5>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Parent Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $student->studentParent->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Parent Phone</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $student->studentParent->parent->parent_phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Parent Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $student->studentParent->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Parent Occupation</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $student->studentParent->parent->parent_occupation ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- Guardian Details --}}
                <h5 class="text-md font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2">Guardian Details</h5>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Guardian Name</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $student->studentUserDetails->guardian_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Guardian Phone</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $student->studentUserDetails->guardian_phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Guardian Email</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $student->studentUserDetails->guardian_email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Relationship</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $student->studentUserDetails->guardian_relationship ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- Additional Information --}}
                <h5 class="text-md font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2">Additional
                    Information</h5>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Medical Conditions</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $student->studentUserDetails->medical_conditions ?? 'None reported' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Allergies</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $student->studentUserDetails->allergies ?? 'None reported' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Special Needs</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $student->studentUserDetails->special_needs ?? 'None reported' }}</p>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $student->studentUserDetails->notes ?? 'No additional notes' }}</p>
                    </div>
                </div>

                {{-- Timestamps --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6 pt-6 border-t border-gray-200">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Created At</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $student->created_at ? $student->created_at->format('M d, Y H:i') : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $student->updated_at ? $student->updated_at->format('M d, Y H:i') : 'N/A' }}</p>
                    </div>
                </div>

            </div>

            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                <form action="{{ route('student.destroy', (int) $student->id) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this student?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete Student
                    </button>
                </form>
                <a href="{{ route('student.edit', $student) }}"
                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Edit Student
                </a>
            </div>

        </div>

    </div>
</x-layouts::app>
