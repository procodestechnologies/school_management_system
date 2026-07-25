<?php

use Flux\Flux;
use Illuminate\Support\Facades\Artisan;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Nwidart\Modules\Facades\Module;

new class extends Component {
    #[Validate('required')]
    public string $name;
    public function mount()
    {
        $this->name = '';
    }
    public function createModule()
    {
        $data = $this->validate();

        Artisan::call('module:make', [
            'name' => $data,
        ]);

        Artisan::call('module:make-service-provider', [
            'name' => "{$data}ServiceProvider",
            'module' => $data,
        ]);

        Artisan::call('module:make-controller', [
            'controller' => 'HomeController',
            'module' => $data,
        ]);

        Artisan::call('module:make-model', [
            'model' => 'Example',
            'module' => $data,
        ]);

        Artisan::call('module:make-migration', [
            'name' => 'create_libraries_table',
            'module' => $data,
        ]);
        Flux::toast($data['name'] . " created successfully!");
    }
};
?>

<div>
    <form wire:submit.prevent="createModule">
        <div class="mb-4">
            <flux:input label="Module name" wire:model="name" placeholder="e.g Library" />
            <flux:error></flux:error>
        </div>
        <flux:button type="submit" icon="plus" label="create">Create</flux:button>
    </form>
</div>