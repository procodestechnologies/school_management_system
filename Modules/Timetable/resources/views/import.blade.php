<x-layouts::app :title="__('Import Timetable')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">Import Timetable</h4>
                <small class="text-sm text-gray-500">
                    Upload a CSV or Excel file for a single class. This replaces that class's existing timetable
                    &mdash; other classes are never affected.
                </small>
            </div>

            @if (session('error'))
                <div class="mx-6 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mx-6 mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('timetable.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select name="class_id" label="Class">
                        <flux:select.option value="">Select Class</flux:select.option>
                        @foreach ($classes as $schoolClass)
                            <flux:select.option value="{{ $schoolClass->id }}"
                                :selected="old('class_id') == $schoolClass->id">
                                {{ $schoolClass->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input type="file" name="file" label="CSV / XLS / XLSX File" accept=".csv,.txt,.xls,.xlsx"
                        required />

                    <div class="md:col-span-2 rounded-md border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
                        <p class="font-medium text-gray-800 mb-1">Expected columns (header row required):</p>
                        <p>Day &middot; Start Time &middot; End Time &middot; Subject &middot; Teacher Email
                            (optional) &middot; Room (optional)</p>
                        <p class="mt-2">Rows with Subject left blank, or set to "BREAK"/"LUNCH", are skipped
                            automatically.</p>
                        <a href="{{ route('timetable.import.template') }}"
                            class="inline-block mt-2 text-blue-600 hover:underline">Download a blank CSV template
                            &rarr;</a>
                    </div>
                </div>

                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                    <a href="{{ route('timetable.index') }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50" wire:navigate>
                        Cancel
                    </a>
                    <flux:button variant="primary" type="submit">Upload &amp; Merge</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
