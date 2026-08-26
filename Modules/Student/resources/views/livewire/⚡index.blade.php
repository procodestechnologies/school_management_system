<?php

use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Student\Models\StudentDetails;

new #[Title('Students')] class extends Component
{
    use WithPagination;

    /** @var string[] */
    public const SORTABLE = ['admission_number', 'gender', 'phone', 'created_at'];

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $sort = 'created_at';

    #[Url]
    public string $direction = 'desc';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('view student'), 403);
    }

    public function updating(string $property): void
    {
        if (in_array($property, ['search', 'status'], true)) {
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

    /**
     * Removing a student takes their login with them - the same thing the
     * endpoint has always done.
     */
    public function delete(int $studentId): void
    {
        abort_unless(auth()->user()->can('delete student'), 403);

        $details = $this->scoped()->where('student_id', $studentId)->firstOrFail();
        $student = User::findOrFail($details->student_id);

        $details->delete();
        $student->delete();

        Flux::toast(text: 'Student removed from the institution.', variant: 'success');
    }

    #[Computed]
    public function students()
    {
        $sort = in_array($this->sort, self::SORTABLE, true) ? $this->sort : 'created_at';
        $direction = $this->direction === 'asc' ? 'asc' : 'desc';

        return $this->scoped()
            ->with(['student', 'institution'])
            ->when($this->status !== '', fn ($query) => $query->where('enrollment_status', $this->status))
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';

                $query->where(function ($q) use ($term) {
                    $q->where('admission_number', 'like', $term)
                        ->orWhere('student_number', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhereHas('student', fn ($q2) => $q2->where('name', 'like', $term)->orWhere('email', 'like', $term));
                });
            })
            ->orderBy($sort, $direction)
            ->paginate(15);
    }

    private function scoped()
    {
        $query = StudentDetails::query();

        if (auth()->user()->hasRole('Parent')) {
            return $query->where('parent_id', auth()->id());
        }

        if (! isAdmin()) {
            return $query->where('institution_id', currentInstitution()?->id ?? 0);
        }

        return $query;
    }
}; ?>

<div class="p-4">
    <div class="mb-2 flex flex-row flex-wrap items-end justify-between gap-3">
        @can('create student')
            <flux:button href="{{ route('student.create') }}" icon="plus" wire:navigate>Add Student</flux:button>
        @endcan

        <div class="flex flex-wrap items-end gap-2">
            <flux:input type="search" icon="magnifying-glass" placeholder="Search name, admission no. or phone"
                wire:model.live.debounce.400ms="search" class="w-72" />
            <flux:select wire:model.live="status" label="Status">
                <flux:select.option value="">All</flux:select.option>
                @foreach (['active', 'expelled', 'graduated', 'suspended', 'transferred', 'withdrawn', 'dropped'] as $option)
                    <flux:select.option value="{{ $option }}">{{ ucfirst($option) }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <flux:card>
        <flux:table wire:loading.class="opacity-60">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'admission_number'" :direction="$direction"
                    wire:click="sortBy('admission_number')">Admission No.</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'gender'" :direction="$direction"
                    wire:click="sortBy('gender')">Gender</flux:table.column>
                <flux:table.column>Institution</flux:table.column>
                <flux:table.column sortable :sorted="$sort === 'phone'" :direction="$direction"
                    wire:click="sortBy('phone')">Phone</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->students as $details)
                    <flux:table.row :key="$details->id">
                        <flux:table.cell>{{ $details->student?->name }}</flux:table.cell>
                        <flux:table.cell>{{ $details->admission_number ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ ucfirst($details->gender ?? '') ?: '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $details->institution?->name }}</flux:table.cell>
                        <flux:table.cell>{{ $details->phone ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$details->is_active ? 'emerald' : 'red'">
                                {{ ucfirst($details->enrollment_status ?? '') ?: 'Unknown' }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($details->student)
                                <flux:button href="{{ route('student.show', $details->student->id) }}" icon="eye"
                                    variant="primary" color="emerald" wire:navigate>view</flux:button>
                                @can('edit student')
                                    <flux:button href="{{ route('student.edit', $details->student->id) }}" icon="pencil"
                                        variant="primary" color="yellow" wire:navigate>edit</flux:button>
                                @endcan
                                @can('delete student')
                                    <flux:button type="button" icon="trash" variant="primary" color="red"
                                        wire:click="delete({{ $details->student->id }})"
                                        wire:confirm="Remove this student and their login?">delete</flux:button>
                                @endcan
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-gray-500">
                            No students found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <div class="mt-4">
        {{ $this->students->links() }}
    </div>
</div>
