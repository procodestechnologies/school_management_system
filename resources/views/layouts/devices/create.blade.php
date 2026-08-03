<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 md:p-6">
        <div class="flex items-center justify-between">
            <flux:heading size="2xl" class="mb-4">Add Biometric Device </flux:heading>
            <div class="flex justify-end mb-4">
                <flux:button wire:navigate href="{{ route('devices.index') }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    View Devices
                </flux:button>
            </div>
        </div>
        {{-- create form --}}
        <livewire:create-device-form />
    </div>
</x-layouts::app>
