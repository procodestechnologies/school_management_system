{{-- Profile Photo --}}
@if($student->studentUserDetails && $student->studentUserDetails->profile_photo)
    <div class="flex justify-center mb-6">
        <div class="relative">
            <img src="{{ Storage::url($student->studentUserDetails->profile_photo) }}" 
                 alt="{{ $student->name }}" 
                 class="h-32 w-32 rounded-full object-cover border-4 border-gray-200">
            <label for="profile_photo" class="absolute bottom-0 right-0 bg-blue-600 rounded-full p-2 cursor-pointer hover:bg-blue-700">
                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
            </label>
        </div>
    </div>
@endif