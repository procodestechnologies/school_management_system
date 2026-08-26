<?php

use Athwari\LaravelZktecoAdms\Models\ZktecoAttendanceLog;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Attendance\Services\AttendanceLogQuery;
use Modules\Institution\Models\Institution;

new #[Title('Attendance')] class extends Component
{
    use WithPagination;

    /** @var string[] */
    public const SORTABLE = ['occurred_at', 'status'];

    #[Url]
    public string $search = '';

    #[Url]
    public string $from = '';

    #[Url]
    public string $to = '';

    #[Url]
    public string $sort = 'occurred_at';

    #[Url]
    public string $direction = 'desc';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('view attendance'), 403);
    }

    public function updating(string $property): void
    {
        if (in_array($property, ['search', 'from', 'to'], true)) {
            $this->resetPage();
        }
    }

    public function sortBy(string $column): void
    {
        if (! in_array($column, self::SORTABLE, true)) {
            return;
        }

        if ($this->sort === $column) {
            $this->direction = $this->direction === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sort = $column;
        $this->direction = 'asc';
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'from', 'to']);
        $this->resetPage();
    }

    #[Computed]
    public function institution(): ?Institution
    {
        return currentInstitution();
    }

    #[Computed]
    public function logs()
    {
        $institution = $this->institution;

        if (! $institution) {
            return null;
        }

        $sort = in_array($this->sort, self::SORTABLE, true) ? $this->sort : 'occurred_at';
        $direction = $this->direction === 'asc' ? 'asc' : 'desc';

        $logs = AttendanceLogQuery::build($institution->id, auth()->user(), [
            'search' => $this->search,
            'from' => $this->from,
            'to' => $this->to,
        ])
            ->orderBy($sort, $direction)
            ->paginate(25);

        return $logs->through(fn (ZktecoAttendanceLog $log) => AttendanceLogQuery::toDisplayRow($log));
    }
}; ?>

<div class="p-4">
    <flux:card>
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="lg">Attendance</flux:heading>
                @if ($this->logs)
                    <flux:text class="text-zinc-500">
                        Showing {{ $this->logs->count() }} of {{ $this->logs->total() }} record(s)
                    </flux:text>
                @endif
            </div>

            @if ($this->logs)
                <div class="flex flex-wrap items-center gap-2">
                    <flux:input type="search" icon="magnifying-glass" placeholder="Search attendance…"
                        wire:model.live.debounce.400ms="search" class="w-56" />
                    <flux:input type="date" wire:model.live="from" />
                    <flux:input type="date" wire:model.live="to" />
                    @if ($search !== '' || $from !== '' || $to !== '')
                        <flux:button type="button" icon="x-mark" variant="ghost" wire:click="clearFilters">
                            Clear
                        </flux:button>
                    @endif
                </div>
            @endif
        </div>

        @if (! $this->institution)
            <flux:callout variant="warning" icon="information-circle">
                <flux:callout.heading>No institution found</flux:callout.heading>
                <flux:callout.text>
                    Attendance records could not be loaded because no institution is linked to your account.
                </flux:callout.text>
            </flux:callout>
        @else
            <div class="overflow-x-auto" wire:loading.class="opacity-60">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Student</flux:table.column>
                        <flux:table.column>Admission No.</flux:table.column>
                        <flux:table.column>Device</flux:table.column>
                        <flux:table.column sortable :sorted="$sort === 'status'" :direction="$direction"
                            wire:click="sortBy('status')">Status</flux:table.column>
                        <flux:table.column>Verify Mode</flux:table.column>
                        <flux:table.column sortable :sorted="$sort === 'occurred_at'" :direction="$direction"
                            wire:click="sortBy('occurred_at')">Date/Time</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($this->logs as $log)
                            <flux:table.row :key="$log['id']">
                                <flux:table.cell>{{ $log['student_name'] }}</flux:table.cell>
                                <flux:table.cell>{{ $log['admission_number'] }}</flux:table.cell>
                                <flux:table.cell>{{ $log['device_name'] }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge size="sm" color="{{ $log['status_color'] }}">
                                        {{ $log['status_label'] }}
                                    </flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>{{ $log['verify_mode_label'] }}</flux:table.cell>
                                <flux:table.cell>{{ $log['occurred_at'] }}</flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="6" class="text-center text-zinc-500">
                                    No attendance records found.
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <div class="mt-4">
                {{ $this->logs->links() }}
            </div>
        @endif
    </flux:card>
</div>
