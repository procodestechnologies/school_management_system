{{-- Guardian Details --}}
<h5 class="text-md font-semibold text-gray-800 mb-3">Guardian Details <span
        class="text-xs font-normal text-gray-400">(if different from parent)</span></h5>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div>
        <flux:input type="text" name="guardian_name" label="Guardian Name"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('guardian_name') border-red-500 @enderror"
            value="{{ old('guardian_name', $student->studentUserDetails->guardian_name ?? '') }}" />
        @error('guardian_name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <flux:input type="text" name="guardian_phone" label="Guardian Phone"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('guardian_phone') border-red-500 @enderror"
            value="{{ old('guardian_phone', $student->studentUserDetails->guardian_phone ?? '') }}" />
        @error('guardian_phone')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <flux:input type="email" name="guardian_email" label="Guardian Email"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('guardian_email') border-red-500 @enderror"
            value="{{ old('guardian_email', $student->studentUserDetails->guardian_email ?? '') }}" />
        @error('guardian_email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <flux:input type="text" name="guardian_relationship" label="Relationship"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('guardian_relationship') border-red-500 @enderror"
            value="{{ old('guardian_relationship', $student->studentUserDetails->guardian_relationship ?? '') }}" />
        @error('guardian_relationship')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>