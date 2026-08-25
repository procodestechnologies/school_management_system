<x-layouts::app :title="__('Edit Expenditure')">
    <div class="p-4">

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="rounded-t-lg border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h4 class="mb-0 text-lg font-semibold text-gray-900">Edit Expenditure</h4>
            </div>

            @if (session('error'))
                <div class="mx-6 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mx-6 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @include('expenditure::form', [
                'action' => route('expenditure.update', $expenditure->id),
                'method' => 'PUT',
                'submitLabel' => __('Update Expenditure'),
            ])
        </div>
    </div>
</x-layouts::app>
