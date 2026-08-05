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
                <x-sortable-column column="institution">Institution Name</x-sortable-column>
                <x-sortable-column column="device_name">Device Name</x-sortable-column>
                <x-sortable-column column="ip_address">Device IP</x-sortable-column>
                <x-sortable-column column="serial_number">Device Serial Number</x-sortable-column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($devices as $device)
                    <flux:table.row>
                        <flux:table.cell class="truncate">{{ $device->institution->name }}</flux:table.cell>
                        <flux:table.cell>{{ $device->zktecoDevice?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $device->zktecoDevice?->ip_address ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $device->serial_number }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($device->zktecoDevice?->isOnline())
                                <flux:badge color="emerald">Online</flux:badge>
                            @elseif ($device->zktecoDevice)
                                <flux:badge color="zinc">Offline</flux:badge>
                            @else
                                <flux:badge color="amber">Not yet connected</flux:badge>
                            @endif
                        </flux:table.cell>
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
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center text-gray-500">
                            No devices yet. Add one even before it's online - you'll see it come online here
                            once it connects.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</x-layouts::app>
