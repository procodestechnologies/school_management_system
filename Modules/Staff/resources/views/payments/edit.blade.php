<x-layouts::app :title="__('Edit Staff Payment')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">
                    Edit Payment — {{ $payment->staff?->name }} ({{ $payment->period?->format('F Y') }})
                </h4>
            </div>

            @if (session('error'))
                <div class="mx-6 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mx-6 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p class="font-medium">{{ $errors->count() }} error(s) prevented this payment from being saved:</p>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @include('staff::payments.form', [
                'action' => route('staff.payments.update', $payment),
                'method' => 'PUT',
                'submitLabel' => __('Update Payment'),
            ])
        </div>
    </div>
</x-layouts::app>
