<x-layouts::app :title="__('Edit Student')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg flex justify-between items-center">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-0">Edit Student</h4>
                    <small class="text-sm text-gray-500">
                        Update the student's, parent's and guardian's details below.
                    </small>
                </div>
                <div>
                    <a href="{{ route('student.show', $student->id) }}"
                        class="text-sm text-blue-600 hover:text-blue-800">
                        View Student
                    </a>
                </div>
            </div>

            {{-- Display all errors at the top --}}
            @if ($errors->any())
                <div class="mx-6 mt-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
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
                            <h3 class="text-sm font-medium text-red-800">
                                Please fix the following {{ $errors->count() }} error(s):
                            </h3>
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

            <form action="{{ route('student.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="p-6">
                    {{-- Profile Photo --}}
                    @include('student.partials.profile_photo')

                    {{-- Account Information --}}
                    {{-- @include('student.partials.account_info') --}}

                    {{-- Personal Information --}}
                    @include('student.partials.personal_info')

                    {{-- Address --}}
                    @include('student.partials.address_info')

                    {{-- Parent Details --}}
                    @include('student.partials.parent_info')

                    {{-- Guardian Details --}}
                    @include('student.partials.guardian_info')

                    {{-- Additional Information --}}
                    @include('student.partials.additional_info')
                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                    <a href="{{ route('student.index') }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>

                    <flux:button variant="primary" type="submit"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Student
                    </flux:button>
                </div>

            </form>

        </div>

    </div>
</x-layouts::app>
