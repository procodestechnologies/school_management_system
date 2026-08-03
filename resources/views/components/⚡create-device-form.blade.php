<?php

use App\Models\User;
use Athwari\LaravelZktecoAdms\Models\ZktecoDevice;
use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    #[Validate('string|required|max:255')]
    public string $name;
    #[Validate('string|required')]
    public string $ip_address;
    #[Validate('string|required|max:255')]
    public string $serial_number;
    public User $user;
    public $institutions;
    #[Validate('required|exists:institutions,id')]
    public $institution_id;
    public function mount()
    {
        $this->user = auth()->user();
        $this->institutions = $this->user->institution;
        $this->ip_address = '';
        $this->serial_number = '';
    }
    public function createDevice()
    {
        $data = $this->validate();
        $zkteckoDevice = ZktecoDevice::create([
            'name' => $this->name,
            'ip_address' => $this->ip_address,
            'serial_number' => $this->serial_number,
        ]);
        $device = new \App\Models\Devices();
        $device->institution_id = $this->institution_id;
        $device->zkteco_device_id = $zkteckoDevice->id;
        $device->serial_number = $this->serial_number;
        $device->save();
        Flux::toast(text: 'Device created successfully.', variant: 'success');
        $this->reset(['name', 'ip_address', 'serial_number', 'institution_id']);
    }
};
?>
<form wire:submit="createDevice">
    <flux:select label="Select Institution" wire:model.live="institution_id" class="mb-1">
        <option value="">Select Institution</option>
        @foreach ($institutions as $institution)
            <option value="{{ $institution->id }}">{{ $institution->name }}</option>
        @endforeach
    </flux:select>
    <flux:error for="institution_id" class="text-red-500 text-sm mb-1" />
    <flux:input label="Device Name" wire:model.live="name" placeholder="Enter device name" class="mb-1" />
    <flux:error for="name" class="text-red-500 text-sm mb-1" />
    <flux:input label="Device IP" wire:model.live="ip_address" placeholder="Enter device IP" class="mb-1" />
    <flux:error for="ip_address" class="text-red-500 text-sm mb-1" />
    <flux:input label="Serial Number" wire:model.live="serial_number" placeholder="Enter serial number"
        class="mb-1" />
    <flux:error for="serial_number" class="text-red-500 text-sm mb-1" />
    <div class="mt-4">
        <flux:button type="submit" variant="primary" color="blue">
            Create Device
        </flux:button>
    </div>
</form>
