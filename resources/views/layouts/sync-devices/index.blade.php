<x-layouts::app :title="__('Sync Devices')">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('device_token'))
            <flux:card class="mb-4 border-amber-300">
                <flux:heading size="lg" class="mb-2">Device token</flux:heading>
                <flux:text class="text-zinc-500 mb-3">
                    Paste this into the offline app when it asks to be paired. It is shown once and cannot be
                    retrieved later — only a hash is stored. Lose it and you enroll the device again.
                </flux:text>
                <pre
                    class="overflow-x-auto rounded-md bg-zinc-900 px-4 py-3 text-sm text-zinc-100">{{ session('device_token') }}</pre>
            </flux:card>
        @endif

        <div class="mb-2 flex flex-row justify-between">
            @can('create syncdevice')
                <flux:button href="{{ route('sync-devices.create') }}" icon="plus">Enroll Device</flux:button>
            @endcan
        </div>

        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Device</flux:table.column>
                    <flux:table.column>Platform</flux:table.column>
                    <flux:table.column>Syncs As</flux:table.column>
                    <flux:table.column>Last Sync</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($devices as $device)
                        <flux:table.row>
                            <flux:table.cell>
                                {{ $device->name }}
                                <flux:text class="text-zinc-500 text-xs">{{ $device->client_id }}</flux:text>
                            </flux:table.cell>
                            <flux:table.cell>{{ ucfirst($device->platform) }}</flux:table.cell>
                            <flux:table.cell>
                                {{ $device->user?->name }}
                                <flux:text class="text-zinc-500 text-xs">
                                    {{ $device->user?->getRoleNames()->first() }}
                                </flux:text>
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $device->last_synced_at?->diffForHumans() ?? 'Never' }}
                                @if ($device->last_seen_ip)
                                    <flux:text class="text-zinc-500 text-xs">{{ $device->last_seen_ip }}</flux:text>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$device->isActive() ? 'emerald' : 'red'">
                                    {{ $device->isActive() ? 'Active' : 'Revoked' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                @can('delete syncdevice')
                                    @if ($device->isActive())
                                        <form action="{{ route('sync-devices.destroy', $device) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Revoke {{ $device->name }}? It will stop syncing immediately and any unsynced work on it is lost.');">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" icon="no-symbol" variant="primary"
                                                color="red">
                                                revoke
                                            </flux:button>
                                        </form>
                                    @else
                                        <flux:text class="text-zinc-500 text-xs">
                                            Revoked {{ $device->revoked_at?->diffForHumans() }}
                                        </flux:text>
                                    @endif
                                @endcan
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center text-gray-500">
                                No devices enrolled yet.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
            <div class="mt-4">
                {{ $devices->links() }}
            </div>
        </flux:card>
    </div>
</x-layouts::app>
