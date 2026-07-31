{{-- Account Information --}}
<h5 class="text-md font-semibold text-gray-800 mb-3">Account Information</h5>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div>
        <flux:input type="text" name="name" label="Full Name"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror"
            value="{{ old('name', $student->name ?? '') }}" required />
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <flux:input type="email" name="email" label="Email"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror"
            value="{{ old('email', $student->email ?? '') }}" required />
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <flux:input type="password" name="password" label="Password (leave blank to keep current)"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('password') border-red-500 @enderror" />
        @error('password')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
