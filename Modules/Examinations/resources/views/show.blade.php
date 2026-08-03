<x-layouts::app :title="__('Examination Details')">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">{{ $examination->title }}</h4>
                <small class="text-sm text-gray-500">{{ $examination->institution?->name }}</small>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Subject</p>
                    <p class="text-sm text-gray-900">{{ $examination->subject }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Class</p>
                    <p class="text-sm text-gray-900">{{ $examination->schoolClass?->name ?? $examination->class_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Term</p>
                    <p class="text-sm text-gray-900">{{ $examination->term ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Date</p>
                    <p class="text-sm text-gray-900">{{ $examination->exam_date?->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Time</p>
                    <p class="text-sm text-gray-900">
                        @if ($examination->start_time)
                            {{ $examination->start_time->format('H:i') }} &ndash;
                            {{ $examination->end_time?->format('H:i') }}
                        @else
                            —
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Marks</p>
                    <p class="text-sm text-gray-900">
                        {{ $examination->passing_marks ?? '—' }} / {{ $examination->total_marks }} to pass
                    </p>
                </div>

                @if ($examination->notes)
                    <div class="md:col-span-3">
                        <p class="text-xs text-gray-500 uppercase">Notes</p>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $examination->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                <a href="{{ route('examinations.index') }}"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Back to List
                </a>
                @can('edit examination')
                    <a href="{{ route('examinations.edit', $examination->id) }}"
                        class="px-4 py-2 bg-yellow-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-yellow-600">
                        Edit
                    </a>
                @endcan
                @can('delete examination')
                    <form action="{{ route('examinations.destroy', $examination->id) }}" method="POST"
                        onsubmit="return confirm('Remove this examination?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</x-layouts::app>
