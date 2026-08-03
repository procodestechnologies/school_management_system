<x-layouts::app :title="__('Report Card')">
    <div class="p-4">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-0">{{ $reportCard->student?->name }}</h4>
                <small class="text-sm text-gray-500">
                    {{ $reportCard->schoolClass?->name }} &middot; {{ $reportCard->term }}
                </small>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Status</p>
                    <p class="text-sm">
                        <flux:badge :color="$reportCard->isSent() ? 'emerald' : 'amber'">
                            {{ $reportCard->isSent() ? 'Sent' : 'Ready' }}
                        </flux:badge>
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Mean Percentage</p>
                    <p class="text-sm text-gray-900">
                        {{ $reportCard->mean_percentage !== null ? number_format($reportCard->mean_percentage, 2).'%' : 'Not yet computed' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Mean Grade</p>
                    <p class="text-sm text-gray-900">{{ $reportCard->mean_grade ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Became Ready</p>
                    <p class="text-sm text-gray-900">{{ $reportCard->completed_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Sent</p>
                    <p class="text-sm text-gray-900">
                        {{ $reportCard->sent_at ? $reportCard->sent_at->format('d M Y H:i') : 'Not sent yet' }}
                    </p>
                </div>
                @if ($reportCard->pdf_path)
                    <div>
                        <p class="text-xs text-gray-500 uppercase">PDF</p>
                        <p class="text-sm">
                            <a href="{{ Storage::url($reportCard->pdf_path) }}" target="_blank"
                                class="text-blue-600 hover:underline">View PDF</a>
                        </p>
                    </div>
                @endif
            </div>

            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                <a href="{{ route('reportcard.index') }}"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Back to List
                </a>
            </div>
        </div>
    </div>
</x-layouts::app>
