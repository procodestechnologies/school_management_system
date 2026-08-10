<?php

use App\Models\Setting;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Site Settings')] class extends Component {
    public array $toggles = [];

    public function mount(): void
    {
        abort_unless(isAdmin(), 403);

        foreach (array_keys(Setting::FEATURES) as $key) {
            $this->toggles[$key] = Setting::isEnabled($key);
        }
    }

    public function updated(string $name, $value): void
    {
        if (! str_starts_with($name, 'toggles.')) {
            return;
        }

        $key = substr($name, strlen('toggles.'));

        Setting::set($key, (bool) $value);

        Flux::toast(variant: 'success', text: __(':feature :state.', [
            'feature' => Setting::FEATURES[$key]['label'],
            'state' => $value ? __('enabled') : __('disabled'),
        ]));
    }
}; ?>

    <div class="p-4">
        <flux:heading size="lg">{{ __('Site Settings') }}</flux:heading>
        <flux:text class="mb-6 mt-1">{{ __('Enable or disable platform-wide features. Changes take effect immediately.') }}</flux:text>

        <flux:card class="max-w-2xl divide-y divide-zinc-200 dark:divide-zinc-700">
            @foreach (Setting::FEATURES as $key => $meta)
                <div class="flex items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                    <div>
                        <flux:heading size="sm">{{ $meta['label'] }}</flux:heading>
                        <flux:text size="sm" class="text-zinc-500">{{ $meta['description'] }}</flux:text>
                    </div>

                    <flux:switch wire:model.live="toggles.{{ $key }}" />
                </div>
            @endforeach
        </flux:card>
    </div>
