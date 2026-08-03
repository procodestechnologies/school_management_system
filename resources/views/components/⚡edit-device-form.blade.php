<?php

use App\Models\Devices;
use Athwari\LaravelZktecoAdms\Models\ZktecoDevice;
use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public $device;
    #[Validate('string|required|max:255')]
    public $name;
    #[Validate('string|required')]
    public $ip_address;
    #[Validate('string|required|max:255')]
    public $serial_number;

    #[Validate('boolean')]
    public $is_active;

    public function mount(ZktecoDevice $device)
    {
        $this->device = $device;
        $this->name = $device->name;
        $this->ip_address = $device->ip_address;
        $this->serial_number = $device->serial_number;
        $this->is_active = $device->is_active;
    }
    public function updateDevice()
    {
        $data = $this->validate();
        $deviceData = [
            'name' => $data['name'],
            'ip_address' => $data['ip_address'],
            'serial_number' => $data['serial_number'],
        ];
        $this->device->serial_number = $this->serial_number;
        $this->device->is_active = $this->is_active;
        $this->device->save();
        $this->device->zktecoDevice->update($deviceData);
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
        <flux:input type="text" wire:model.live="name" id="name"
            class="w-full border border-gray-300 rounded px-3 py-2 mt-1" required>
        </flux:input>
    </div>
    <div class="mb-4">
        <flux:label for="ip_address">Device IP Address</flux:label>
        <flux:input type="text" wire:model.live="ip_address" id="ip_address"
            class="w-full border border-gray-300 rounded px-3 py-2 mt-1" required>
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
