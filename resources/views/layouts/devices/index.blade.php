<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 md:p-6">
        <div class="flex items-center justify-between">
            <flux:heading size="2xl" class="mb-4">Biometric Devices ({{ $deviceCount }})</flux:heading>
            <div class="flex justify-end mb-4">
                <flux:button wire:navigate href="{{ route('devices.create') }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add Device
                </flux:button>
            </div>
        </div>
        {{-- flux table showing devices for the user institution --}}
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Institution Name</flux:table.column>
                <flux:table.column>Device Name</flux:table.column>
                <flux:table.column>Device IP</flux:table.column>
                <flux:table.column>Device Serial Number</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($devices as $device)
                    <flux:table.row>
                        <flux:table.cell class="truncate">{{ $device->institution->name }}</flux:table.cell>
                        <flux:table.cell>{{ $device->zktecoDevice }}</flux:table.cell>
                        <flux:table.cell>{{ $device->zktecoDevice->ip_address }}</flux:table.cell>
                        <flux:table.cell>{{ $device->zktecoDevice->serial_number }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button wire:navigate href="{{ route('devices.edit', $device) }}" variant="primary"
                                color="blue" icon="pencil">Edit</flux:button>
                            <form action="{{ route('devices.destroy', $device) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="device_id" value="{{ $device->id }}">
                                <input type="hidden" name="institution_id" value="{{ $device->institution->id }}">
                                <flux:button type="submit" variant="danger" color="red" icon="trash">Delete
                                </flux:button>
                            </form>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
                <flux:table.rows>
        </flux:table>
    </div>
</x-layouts::app>
