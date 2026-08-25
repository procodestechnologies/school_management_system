<x-layouts::app :title="__('Enroll Device')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">Enroll Offline Device</h4>
            </div>

            @if ($errors->any())
                <div class="mx-6 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('sync-devices.store') }}" method="POST">
                @csrf

                <div class="p-6">
                    <p class="text-sm text-gray-500 mb-4">
                        The device works offline as the account you pick below and can reach exactly what that account
                        can. Enroll one per physical machine, so a lost laptop can be cut off without disturbing
                        anyone else.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <flux:input type="text" name="name" label="Device Name" value="{{ old('name') }}"
                            placeholder="Bursar's laptop" required />
                        <flux:select name="platform" label="Platform">
                            @foreach (['desktop' => 'Desktop (Windows/Mac)', 'android' => 'Android', 'ios' => 'iOS'] as $value => $label)
                                <flux:select.option value="{{ $value }}"
                                    :selected="old('platform', 'desktop') === $value">
                                    {{ $label }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:select name="user_id" label="Syncs As" required>
                            <flux:select.option value="">Select Account</flux:select.option>
                            @foreach ($accounts as $account)
                                <flux:select.option value="{{ $account->id }}"
                                    :selected="old('user_id') == $account->id">
                                    {{ $account->name }} ({{ $account->getRoleNames()->first() }})
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                    <a href="{{ route('sync-devices.index') }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50" wire:navigate>
                        Cancel
                    </a>
                    <flux:button variant="primary" type="submit">Enroll Device</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
