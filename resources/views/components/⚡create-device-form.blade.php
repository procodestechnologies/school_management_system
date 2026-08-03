<?php

use App\Models\Devices;
use App\Models\User;
use Athwari\LaravelZktecoAdms\Models\ZktecoDevice;
use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    #[Validate('nullable|string|max:255')]
    public string $name;
    #[Validate('nullable|string')]
    public string $ip_address;
    #[Validate('string|required|max:255|unique:devices,serial_number')]
    public string $serial_number;
    public User $user;
    public $institutions;
    #[Validate('required|exists:institutions,id')]
    public $institution_id;
    public function mount()
    {
        $this->user = auth()->user();
        $this->institutions = $this->user->institution;
        $this->name = '';
        $this->ip_address = '';
        $this->serial_number = '';
    }
    public function createDevice()
    {
        $this->validate();

        // The physical device may still be offline - register it against
        // its serial number without fabricating connection details it
        // hasn't reported yet. If the device already exists (it connected
        // to the ADMS server before anyone got around to assigning it to
        // an institution here), link to that real record instead of
        // creating a duplicate.
        $zktecoDevice = ZktecoDevice::firstOrCreate(
            ['serial_number' => $this->serial_number],
            [
                'name' => $this->name !== '' ? $this->name : $this->serial_number,
                'ip_address' => $this->ip_address !== '' ? $this->ip_address : null,
                'options' => [],
            ]
        );

        $device = new Devices();
        $device->institution_id = $this->institution_id;
        $device->zkteco_device_id = $zktecoDevice->id;
        $device->serial_number = $this->serial_number;
        $device->is_active = true;
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
    <flux:input label="Device Name (optional)" wire:model.live="name"
        placeholder="Leave blank if the device is still offline" class="mb-1" />
    <flux:error for="name" class="text-red-500 text-sm mb-1" />
    <flux:input label="Device IP (optional)" wire:model.live="ip_address"
        placeholder="Leave blank if the device is still offline" class="mb-1" />
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
