<x-layouts::app :title="__('Edit Fee')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">Edit Fee</h4>
                <small class="text-sm text-gray-500">
                    {{ $fee->student?->name }} &mdash; {{ $fee->institution?->name }}
                </small>
            </div>

            @if ($errors->any())
                <div class="mx-6 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p class="font-medium">{{ $errors->count() }} error(s) prevented this fee from being saved:</p>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('feemanagement.update', $fee->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

                        <div>
                            <flux:input type="text" label="Student"
                                class="w-full rounded-md border-gray-300 bg-gray-100"
                                value="{{ $fee->student?->name }}" disabled />
                        </div>

                        <div>
                            <flux:input type="text" label="Institution"
                                class="w-full rounded-md border-gray-300 bg-gray-100"
                                value="{{ $fee->institution?->name }}" disabled />
                        </div>

                        <div>
                            <flux:input type="text" label="Parent/Guardian"
                                class="w-full rounded-md border-gray-300 bg-gray-100"
                                value="{{ $fee->parent?->name ?? 'Not linked' }}" disabled />
                        </div>

                        <div class="md:col-span-3 border-t border-gray-200 pt-4"></div>

                        <div>
                            <flux:input type="text" name="title" label="Title"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('title', $fee->title) }}" required />
                        </div>

                        <div>
                            <flux:select name="fee_type" label="Fee Type"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach (['tuition', 'transport', 'boarding', 'exam', 'uniform', 'activity', 'other'] as $type)
                                    <flux:select.option value="{{ $type }}"
                                        :selected="old('fee_type', $fee->fee_type) === $type">
                                        {{ ucfirst($type) }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        <div>
                            <flux:input type="date" name="due_date" label="Due Date"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('due_date', $fee->due_date?->format('Y-m-d')) }}" />
                        </div>

                        <div>
                            <flux:input type="number" step="0.01" min="0" name="amount" label="Amount"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('amount', $fee->amount) }}" required />
                        </div>

                        <div>
                            <flux:input type="number" step="0.01" min="0" name="amount_paid" label="Amount Paid"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ old('amount_paid', $fee->amount_paid) }}" />
                        </div>

                        <div class="md:col-span-3">
                            <flux:textarea name="notes" rows="3" label="Notes"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $fee->notes) }}</flux:textarea>
                        </div>

                    </div>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">

                    <a href="{{ route('feemanagement.show', $fee->id) }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>

                    <flux:button variant="primary" type="submit"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Save Changes
                    </flux:button>

                </div>

            </form>

        </div>

    </div>
</x-layouts::app>
