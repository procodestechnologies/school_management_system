<x-layouts::app :title="__('Scan Receipt')">
    <div class="p-4">
        @if (session('error'))
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 max-w-xl">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">Scan a Receipt</h4>
                <small class="text-sm text-gray-500">
                    Upload a photo (or PDF) of an M-Pesa message, bank slip, or receipt - OCR will read the amount and
                    admission number for you to review before saving.
                </small>
            </div>

            <form action="{{ route('feemanagement.receipts.extract') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <flux:input type="file" name="receipt" label="Receipt Image or PDF" accept="image/*,application/pdf" required />
                        @error('receipt')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                    <flux:button href="{{ route('feemanagement.index') }}" wire:navigate>Cancel</flux:button>
                    <flux:button type="submit" variant="primary">Read Receipt</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
