<x-layouts::app :title="__('Report Cards')">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-2 flex flex-row justify-between">
            @can('edit reportcard')
                <flux:button href="{{ route('reportcard.settings') }}" icon="cog-6-tooth" variant="ghost">
                    Settings</flux:button>
            @endcan
        </div>

        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Student</flux:table.column>
                    <flux:table.column>Class</flux:table.column>
                    <x-sortable-column column="term">Term</x-sortable-column>
                    <x-sortable-column column="mean_grade">Mean Grade</x-sortable-column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($reportCards as $reportCard)
                        <flux:table.row>
                            <flux:table.cell>{{ $reportCard->student?->name }}</flux:table.cell>
                            <flux:table.cell>{{ $reportCard->schoolClass?->name }}</flux:table.cell>
                            <flux:table.cell>{{ $reportCard->term }}</flux:table.cell>
                            <flux:table.cell>{{ $reportCard->mean_grade ?? '—' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$reportCard->isSent() ? 'emerald' : 'amber'">
                                    {{ $reportCard->isSent() ? 'Sent' : 'Ready' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button href="{{ route('reportcard.show', $reportCard->id) }}" icon="eye"
                                    variant="primary" color="emerald">view</flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center text-gray-500">
                                No report cards yet.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</x-layouts::app>
