<x-layouts::app :title="__('Institution Details')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            {{-- Header --}}
            <div
                class="border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-t-lg flex items-center justify-between">
                <div class="flex items-center gap-4">
                    @if ($institution->logo)
                        <img src="{{ Storage::disk('public')->url($institution->logo) }}" alt="{{ $institution->name }}"
                            class="h-12 w-auto rounded border border-gray-200 bg-white p-1">
                    @endif
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-0">Institution Details</h4>
                        <small class="text-sm text-gray-500">
                            View comprehensive information about the institution
                        </small>
                    </div>
                </div>
                <div class="flex gap-2">
                    @if (institutionOwner($institution->owner->id))
                        <a href="{{ route('institution.edit', $institution->id) }}"
                            class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>
                    @endif
                    <a href="{{ route('institution.index') }}"
                        class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back
                    </a>
                </div>
            </div>

            <div class="p-6">
                {{-- Status Badge --}}
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span
                            class="px-3 py-1 text-sm font-medium rounded-full {{ $institution->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $institution->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-purple-100 text-purple-800">
                            {{ $institution->type }}
                        </span>
                        <span
                            class="px-3 py-1 text-sm font-medium rounded-full {{ $institution->is_approved ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $institution->is_approved ? 'Approved' : 'Pending Approval' }}
                        </span>
                    </div>
                    <span class="text-sm text-gray-500">
                        Last updated: {{ $institution->updated_at->format('M d, Y h:i A') }}
                    </span>
                </div>

                {{-- Basic Information --}}
                <div class="mb-6">
                    <h5 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Basic Information
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Institution
                                Name</label>
                            <p class="mt-1 text-sm text-gray-900 font-medium">{{ $institution->name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Institution
                                Code</label>
                            <p class="mt-1 text-sm text-gray-900 font-mono">{{ $institution->code }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Type</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $institution->type }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Education
                                Level</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $institution->education_level ?? 'Not Set' }}</p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Timezone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $institution->timezone }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Elective
                                Subjects</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $institution->min_electives }}
                                @if ($institution->max_electives)
                                    – {{ $institution->max_electives }}
                                @else
                                    or more
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Contact Information --}}
                <div class="mb-6">
                    <h5 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Contact Information
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg">
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Email</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <a href="mailto:{{ $institution->email }}" class="text-blue-600 hover:underline">
                                    {{ $institution->email }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <a href="tel:{{ $institution->phone }}" class="text-blue-600 hover:underline">
                                    {{ $institution->phone }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Alternative
                                Phone</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if ($institution->alternate_phone)
                                    <a href="tel:{{ $institution->alternate_phone }}"
                                        class="text-blue-600 hover:underline">
                                        {{ $institution->alternate_phone }}
                                    </a>
                                @else
                                    <span class="text-gray-400">Not provided</span>
                                @endif
                            </p>
                        </div>
                        <div class="md:col-span-2 lg:col-span-3">
                            <label
                                class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Website</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if ($institution->website)
                                    <a href="{{ $institution->website }}" target="_blank"
                                        class="text-blue-600 hover:underline">
                                        {{ $institution->website }}
                                    </a>
                                @else
                                    <span class="text-gray-400">Not provided</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Address --}}
                <div class="mb-6">
                    <h5 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Address
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-lg">
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Country</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $institution->country }}</p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 uppercase tracking-wider">County</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $institution->county }}</p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 uppercase tracking-wider">City</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $institution->city }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Postal
                                Address</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $institution->postal_address }}</p>
                        </div>
                        <div class="md:col-span-2 lg:col-span-4">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Physical
                                Address</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $institution->physical_address }}</p>
                        </div>
                    </div>
                </div>

                {{-- Additional Information --}}
                <div class="mb-6">
                    <h5 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Additional Information
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Principal
                                Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $institution->principal_name ?? 'Not provided' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Principal
                                Phone</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if ($institution->principal_phone)
                                    <a href="tel:{{ $institution->principal_phone }}"
                                        class="text-blue-600 hover:underline">
                                        {{ $institution->principal_phone }}
                                    </a>
                                @else
                                    <span class="text-gray-400">Not provided</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Subscription
                                Plan</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $institution->plan->name ?? 'Not set' }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Subscription
                                Expires</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if ($institution->subscription_expires_at)
                                    {{ \Carbon\Carbon::parse($institution->subscription_expires_at)->format('M d, Y') }}
                                @else
                                    <span class="text-gray-400">Not set</span>
                                @endif
                            </p>
                        </div>
                        <div class="md:col-span-2 lg:col-span-3">
                            <label
                                class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $institution->notes ?? 'No notes available' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- System Information --}}
                <div>
                    <h5 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        System Information
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Created
                                At</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $institution->created_at->format('F d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Last
                                Updated</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $institution->updated_at->format('F d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                @if (isAdmin() && ! $institution->is_approved)
                    <form action="{{ route('institution.approve', $institution->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition duration-150">
                            Approve Institution
                        </button>
                    </form>
                @endif
                @if (institutionOwner($institution->owner->id))
                    <form action="{{ route('institution.destroy', $institution->id) }}" method="POST"
                        class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            onclick="return confirm('Are you sure you want to delete this institution? This action cannot be undone.')"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition duration-150">
                            Delete Institution
                        </button>
                    </form>
                    <a wire:navigate href="{{ route('institution.edit', $institution->id) }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150">
                        Edit Institution
                    </a>
                @endif
            </div>
        </div>

    </div>
</x-layouts::app>
