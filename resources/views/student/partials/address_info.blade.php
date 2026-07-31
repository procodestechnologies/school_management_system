{{-- Address --}}
<h5 class="text-md font-semibold text-gray-800 mb-3">Address</h5>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="md:col-span-3">
        <flux:textarea name="address" rows="2" label="Address"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('address') border-red-500 @enderror">
            {{ old('address', $student->studentUserDetails->address ?? '') }}
        </flux:textarea>
        @error('address')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <flux:input type="text" name="city" label="City"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('city') border-red-500 @enderror"
            value="{{ old('city', $student->studentUserDetails->city ?? '') }}" />
        @error('city')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <flux:input type="text" name="state" label="State"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('state') border-red-500 @enderror"
            value="{{ old('state', $student->studentUserDetails->state ?? '') }}" />
        @error('state')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <flux:input type="text" name="country" label="Country"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('country') border-red-500 @enderror"
            value="{{ old('country', $student->studentUserDetails->country ?? 'Kenya') }}" />
        @error('country')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>