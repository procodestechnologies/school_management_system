@php
    $plan = new \App\Models\Plan();
@endphp

<x-layouts::app :title="__('Create Plan')">
    <div class="p-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">Create Plan</h4>
            </div>

            <form action="{{ route('admin.plans.store') }}" method="POST" class="p-6">
                @csrf
                @include('layouts::admin.plans._form')

                <flux:button type="submit" variant="primary">Create Plan</flux:button>
            </form>
        </div>
    </div>
</x-layouts::app>
