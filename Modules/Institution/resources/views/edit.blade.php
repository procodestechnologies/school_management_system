<x-layouts::app :title="__('Edit Institution')">
    <div class="p-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            {{-- Header --}}
            <div
                class="border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-t-lg flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-0">Edit Institution ({{ progress($institution) }}%)
                    </h4>
                    <small class="text-sm text-gray-500">
                        Update institution's details below
                    </small>
                </div>
                <div>
                    <a href="{{ route('institution.show', $institution->id) }}"
                        class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View Details
                    </a>
                </div>
            </div>

            <form action="{{ route('institution.update', $institution->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="p-6">

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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <flux:input type="text" name="name" label="Institution Name *"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('name', $institution->name) }}" required />
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <flux:input type="text" name="code" label="Institution Code *"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('code', $institution->code) }}" required />
                                @error('code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <flux:select name="type" label="Institution Type *"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="School"
                                        {{ old('type', $institution->type) == 'School' ? 'selected' : '' }}>School
                                    </option>
                                    <option value="College"
                                        {{ old('type', $institution->type) == 'College' ? 'selected' : '' }}>College
                                    </option>
                                    <option value="University"
                                        {{ old('type', $institution->type) == 'University' ? 'selected' : '' }}>
                                        University</option>
                                    <option value="Training Centre"
                                        {{ old('type', $institution->type) == 'Training Centre' ? 'selected' : '' }}>
                                        Training Centre</option>
                                </flux:select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <flux:input type="text" name="education_level" label="Education Level"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('education_level', $institution->education_level) }}" />
                                <small class="text-xs text-gray-500">e.g., Primary, Secondary, Tertiary</small>
                                @error('education_level')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <flux:select name="timezone" label="Timezone"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="Africa/Nairobi"
                                        {{ old('timezone', $institution->timezone) == 'Africa/Nairobi' ? 'selected' : '' }}>
                                        Africa/Nairobi</option>
                                    <option value="Africa/Cairo"
                                        {{ old('timezone', $institution->timezone) == 'Africa/Cairo' ? 'selected' : '' }}>
                                        Africa/Cairo</option>
                                    <option value="Africa/Johannesburg"
                                        {{ old('timezone', $institution->timezone) == 'Africa/Johannesburg' ? 'selected' : '' }}>
                                        Africa/Johannesburg</option>
                                    <option value="America/New_York"
                                        {{ old('timezone', $institution->timezone) == 'America/New_York' ? 'selected' : '' }}>
                                        America/New_York</option>
                                    <option value="Europe/London"
                                        {{ old('timezone', $institution->timezone) == 'Europe/London' ? 'selected' : '' }}>
                                        Europe/London</option>
                                    <option value="Asia/Dubai"
                                        {{ old('timezone', $institution->timezone) == 'Asia/Dubai' ? 'selected' : '' }}>
                                        Asia/Dubai</option>
                                </flux:select>
                                @error('timezone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Branding --}}
                    <div class="mb-6">
                        <h5 class="text-md font-semibold text-gray-800 mb-3">Branding</h5>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                            <div>
                                <flux:input type="file" name="logo" label="Logo" accept="image/*" />
                                <p class="mt-1 text-xs text-gray-500">Used on the letterhead of generated report
                                    cards.</p>
                                @error('logo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            @if ($institution->logo)
                                <div>
                                    <img src="{{ Storage::url($institution->logo) }}" alt="{{ $institution->name }}"
                                        class="h-16 w-auto rounded border border-gray-200 bg-white p-1">
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Academic Settings --}}
                    <div class="mb-6">
                        <h5 class="text-md font-semibold text-gray-800 mb-3">Academic Settings</h5>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <flux:input type="number" name="min_electives" label="Minimum Elective Subjects"
                                    min="0" value="{{ old('min_electives', $institution->min_electives) }}" />
                                @error('min_electives')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <flux:input type="number" name="max_electives" label="Maximum Elective Subjects"
                                    min="0" value="{{ old('max_electives', $institution->max_electives) }}" />
                                <small class="text-xs text-gray-500">Leave blank for no maximum.</small>
                                @error('max_electives')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <flux:input type="email" name="email" label="Email Address *"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('email', $institution->email) }}" required />
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <flux:input type="tel" name="phone" label="Phone Number *"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('phone', $institution->phone) }}" required />
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <flux:input type="tel" name="alternate_phone" label="Alternative Phone"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('alternate_phone', $institution->alternate_phone) }}" />
                                @error('alternate_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-3">
                                <flux:input type="url" name="website" label="Website"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('website', $institution->website) }}"
                                    placeholder="https://example.com" />
                                @error('website')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <flux:input type="text" name="country" label="Country"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('country', $institution->country) }}" />
                                @error('country')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <flux:input type="text" name="county" label="County"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('county', $institution->county) }}" />
                                @error('county')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <flux:input type="text" name="city" label="City"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('city', $institution->city) }}" />
                                @error('city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <flux:input type="text" name="postal_address" label="Postal Address"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('postal_address', $institution->postal_address) }}"
                                    placeholder="P.O. Box 123-00100" />
                                @error('postal_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-4">
                                <flux:textarea name="physical_address" rows="3" label="Physical Address"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Street address, building, landmark">
                                    {{ old('physical_address', $institution->physical_address) }}</flux:textarea>
                                @error('physical_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <flux:input type="text" name="principal_name" label="Principal Name"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('principal_name', $institution->principal_name) }}" />
                                @error('principal_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <flux:input type="tel" name="principal_phone" label="Principal Phone"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('principal_phone', $institution->principal_phone) }}" />
                                @error('principal_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <flux:select name="subscription_plan" label="Subscription Plan"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Plan</option>
                                    <option value="Free"
                                        {{ old('subscription_plan', $institution->subscription_plan) == 'Free' ? 'selected' : '' }}>
                                        Free</option>
                                    <option value="Basic"
                                        {{ old('subscription_plan', $institution->subscription_plan) == 'Basic' ? 'selected' : '' }}>
                                        Basic</option>
                                    <option value="Premium"
                                        {{ old('subscription_plan', $institution->subscription_plan) == 'Premium' ? 'selected' : '' }}>
                                        Premium</option>
                                    <option value="Enterprise"
                                        {{ old('subscription_plan', $institution->subscription_plan) == 'Enterprise' ? 'selected' : '' }}>
                                        Enterprise</option>
                                </flux:select>
                                @error('subscription_plan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <flux:input type="date" name="subscription_expires_at"
                                    label="Subscription Expiry Date"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('subscription_expires_at', $institution->subscription_expires_at ? \Carbon\Carbon::parse($institution->subscription_expires_at)->format('Y-m-d') : '') }}" />
                                @error('subscription_expires_at')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <flux:textarea name="notes" rows="3" label="Notes"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Any additional notes about the institution">
                                    {{ old('notes', $institution->notes) }}</flux:textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="mb-6">
                        <h5 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Status
                        </h5>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center space-x-6">
                                <label class="flex items-center">
                                    <input type="radio" name="is_active" value="1"
                                        {{ old('is_active', $institution->is_active) == 1 ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="is_active" value="0"
                                        {{ old('is_active', $institution->is_active) == 0 ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Inactive</span>
                                </label>
                            </div>
                            @error('is_active')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                </div>

                {{-- Footer Actions --}}
                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                    <a href="{{ route('institution.show', $institution->id) }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                        Cancel
                    </a>

                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Update Institution
                    </button>
                </div>

            </form>
        </div>

    </div>
</x-layouts::app>
