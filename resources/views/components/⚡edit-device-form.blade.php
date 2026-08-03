<?php

use App\Models\Devices;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public Devices $device;
    #[Validate('nullable|string|max:255')]
    public $name;
    #[Validate('nullable|string')]
    public $ip_address;
    #[Validate('string|required|max:255')]
    public $serial_number;

    #[Validate('boolean')]
    public $is_active;

    public function mount(Devices $device)
    {
        $this->device = $device;
        $this->name = $device->zktecoDevice?->name;
        $this->ip_address = $device->zktecoDevice?->ip_address;
        $this->serial_number = $device->serial_number;
        $this->is_active = (bool) $device->is_active;
    }
    public function updateDevice()
    {
        $this->validate([
            'name' => 'nullable|string|max:255',
            'ip_address' => 'nullable|string',
            'serial_number' => [
                'required', 'string', 'max:255',
                Rule::unique('devices', 'serial_number')->ignore($this->device->id),
            ],
            'is_active' => 'boolean',
        ]);

        // Re-resolve the ZktecoDevice for the (possibly changed) serial
        // number rather than renaming whichever device happened to be
        // linked before - editing the serial number re-points this
        // institution's device to a different physical unit, it never
        // hijacks another device's identity.
        $this->device->serial_number = $this->serial_number;
        $this->device->is_active = $this->is_active;
        $this->device->linkZktecoDevice($this->name ?: null, $this->ip_address ?: null);

        $this->dispatch('deviceUpdated');
        Flux::toast(text: 'Device updated successfully.', variant: 'success');
    }
};
?>

<form wire:submit="updateDevice" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-4">
        <flux:label for="name">Device Name</flux:label>
        <flux:input type="text" wire:model.live="name" id="name" placeholder="Not yet connected"
            class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
        </flux:input>
    </div>
    <div class="mb-4">
        <flux:label for="ip_address">Device IP Address</flux:label>
        <flux:input type="text" wire:model.live="ip_address" id="ip_address" placeholder="Not yet connected"
            class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
        </flux:input>
    </div>
    <div class="mb-4">
        <flux:label for="serial_number">Device Serial Number</flux:label>
        <flux:input type="text" wire:model.live="serial_number" id="serial_number"
            class="w-full border border-gray-300 rounded px-3 py-2 mt-1" required>
        </flux:input>
    </div>
    <div class="mt-4">
        <flux:checkbox label="Active" wire:model.live="is_active" id="is_active" class="mr-2">

        </flux:checkbox>
    </div>
    <div class="flex justify-end">
        <flux:button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Update Device
        </flux:button>
    </div>
</form>
