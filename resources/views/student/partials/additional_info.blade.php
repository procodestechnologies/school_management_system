{{-- Additional Information --}}
<h5 class="text-md font-semibold text-gray-800 mb-3">Additional Information</h5>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div>
        <flux:textarea name="medical_conditions" rows="2" label="Medical Conditions"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('medical_conditions') border-red-500 @enderror">
            {{ old('medical_conditions', $student->studentUserDetails->medical_conditions ?? '') }}
        </flux:textarea>
        @error('medical_conditions')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <flux:textarea name="allergies" rows="2" label="Allergies"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('allergies') border-red-500 @enderror">
            {{ old('allergies', $student->studentUserDetails->allergies ?? '') }}
        </flux:textarea>
        @error('allergies')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <flux:textarea name="special_needs" rows="2" label="Special Needs"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('special_needs') border-red-500 @enderror">
            {{ old('special_needs', $student->studentUserDetails->special_needs ?? '') }}
        </flux:textarea>
        @error('special_needs')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-3">
        <flux:textarea name="notes" rows="2" label="Notes"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror">
            {{ old('notes', $student->studentUserDetails->notes ?? '') }}
        </flux:textarea>
        @error('notes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <flux:checkbox name="is_active" value="1" label="Active" 
            :checked="old('is_active', $student->studentUserDetails->is_active ?? false)" />
        @error('is_active')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>