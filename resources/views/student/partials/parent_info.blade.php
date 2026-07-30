{{-- Parent Details --}}
<h5 class="text-md font-semibold text-gray-800 mb-3">Parent Details</h5>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div>
        <flux:input type="text" name="parent_name" label="Parent Name"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('parent_name') border-red-500 @enderror"
            value="{{ old('parent_name', $student->studentParent->name ?? '') }}" />
        @error('parent_name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <flux:input type="text" name="parent_phone" label="Parent Phone"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('parent_phone') border-red-500 @enderror"
            value="{{ old('parent_phone', $student->studentParent->parent->parent_phone ?? '') }}" />
        @error('parent_phone')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <flux:input type="email" name="parent_email" label="Parent Email"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('parent_email') border-red-500 @enderror"
            value="{{ old('parent_email', $student->studentParent->email ?? '') }}" />
        @error('parent_email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <flux:input type="text" name="parent_occupation" label="Parent Occupation"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('parent_occupation') border-red-500 @enderror"
            value="{{ old('parent_occupation', $student->studentParent->parent->parent_occupation ?? '') }}" />
        @error('parent_occupation')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>