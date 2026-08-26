<?php

use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Modules\Classes\Models\SchoolClass;
use Modules\Student\Models\StudentDetails;
use Modules\Timetable\Actions\SaveTimetableEntry;
use Modules\Timetable\Models\TimetableEntry;

new #[Title('Timetable')] class extends Component
{
    #[Url(as: 'class_id')]
    public string $classId = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('view timetable'), 403);

        // Land on a class rather than an empty screen when there's an
        // obvious one to show.
        if ($this->classId === '' && ! $this->isTeacher) {
            $this->classId = (string) ($this->classes->first()?->id ?? '');
        }
    }

    #[Computed]
    public function isTeacher(): bool
    {
        return auth()->user()->hasRole('Teacher');
    }

    #[Computed]
    public function isStudent(): bool
    {
        return auth()->user()->hasRole('Student');
    }

    #[Computed]
    public function isParent(): bool
    {
        return auth()->user()->hasRole('Parent');
    }

    /**
     * Classes this viewer may look at: a student only ever their own, a
     * parent their children's, everyone else the school's.
     *
     * @return Collection<int, SchoolClass>
     */
    #[Computed]
    public function classes(): Collection
    {
        $user = auth()->user();

        if ($this->isStudent || $this->isParent) {
            $classIds = $this->isParent
                ? StudentDetails::where('parent_id', $user->id)->pluck('class_id')
                : collect([StudentDetails::where('student_id', $user->id)->value('class_id')]);

            $classIds = $classIds->filter()->map(fn ($id) => (int) $id)->unique()->values();

            return SchoolClass::whereIn('id', $classIds)->orderBy('name')->get();
        }

        $query = SchoolClass::query();

        if (! isAdmin()) {
            $query->where('institution_id', currentInstitution()?->id ?? 0);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Hide the picker whenever there's nothing to actually choose between:
     * always for a student, and for a parent whose children share a class.
     */
    #[Computed]
    public function showPicker(): bool
    {
        return ! $this->isStudent && ! ($this->isParent && $this->classes->count() <= 1);
    }

    #[Computed]
    public function selectedClass(): ?SchoolClass
    {
        if ($this->classId === '') {
            return null;
        }

        return $this->classes->firstWhere('id', (int) $this->classId);
    }

    /**
     * @return Collection<int, TimetableEntry>
     */
    #[Computed]
    public function entries(): Collection
    {
        if ($this->isTeacher) {
            // A teacher doesn't pick a class - they see every lesson they're
            // assigned to teach, across all their classes, for the week.
            return TimetableEntry::with('schoolClass')
                ->where('teacher_id', auth()->id())
                ->get();
        }

        $class = $this->selectedClass;

        return $class
            ? TimetableEntry::with('teacher')->where('class_id', $class->id)->get()
            : collect();
    }

    /**
     * The distinct period columns of the week, in time order.
     *
     * @return Collection<int, string>
     */
    #[Computed]
    public function periods(): Collection
    {
        return $this->entries
            ->map(fn (TimetableEntry $entry) => $this->periodKey($entry))
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * Entries indexed by [day][period], which is what the grid reads.
     *
     * @return array<string, array<string, TimetableEntry>>
     */
    #[Computed]
    public function grid(): array
    {
        $grid = [];

        foreach ($this->entries as $entry) {
            $grid[$entry->day_of_week][$this->periodKey($entry)] = $entry;
        }

        return $grid;
    }

    /**
     * @return string[]
     */
    public function days(): array
    {
        return SaveTimetableEntry::DAYS;
    }

    private function periodKey(TimetableEntry $entry): string
    {
        return $entry->start_time?->format('H:i').'-'.$entry->end_time?->format('H:i');
    }
}; ?>

<div class="p-4">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        @if ($this->isTeacher)
            <flux:heading size="lg">My Timetable</flux:heading>
        @elseif ($this->showPicker)
            <flux:select wire:model.live="classId" class="max-w-xs">
                <flux:select.option value="">Select a class&hellip;</flux:select.option>
                @foreach ($this->classes as $schoolClass)
                    <flux:select.option value="{{ $schoolClass->id }}">{{ $schoolClass->name }}</flux:select.option>
                @endforeach
            </flux:select>
        @elseif ($this->isStudent)
            <flux:heading size="lg">My Timetable</flux:heading>
        @elseif ($this->isParent)
            <flux:heading size="lg">My Child's Timetable</flux:heading>
        @endif

        <div class="flex gap-2">
            @can('create timetable')
                <flux:button href="{{ route('timetable.import') }}" icon="arrow-up-tray" variant="ghost" wire:navigate>
                    Import Timetable
                </flux:button>
                <flux:button href="{{ route('timetable.create') }}" wire:navigate>Add Timetable Entry</flux:button>
            @endcan
        </div>
    </div>

    @php($grid = $this->grid)
    @php($periods = $this->periods)

    @if ($this->isTeacher && $periods->isEmpty())
        <flux:card class="py-10 text-center">
            <flux:text class="text-zinc-500">
                You have no timetable entries assigned yet. Contact your school administrator.
            </flux:text>
        </flux:card>
    @elseif (($this->isStudent || $this->isParent) && $this->classes->isEmpty())
        <flux:card class="py-10 text-center">
            <flux:text class="text-zinc-500">
                @if ($this->isStudent)
                    You are not assigned to a class yet. Contact your school administrator.
                @else
                    No child is assigned to a class yet. Contact your school administrator.
                @endif
            </flux:text>
        </flux:card>
    @elseif (! $this->isTeacher && ! $this->isStudent && ! $this->isParent && $this->classes->isEmpty())
        <flux:card class="py-10 text-center">
            <flux:text class="text-zinc-500">No classes available yet.</flux:text>
        </flux:card>
    @elseif (! $this->isTeacher && ! $this->selectedClass)
        <flux:card class="py-10 text-center">
            <flux:text class="text-zinc-500">Select a class above to view its timetable.</flux:text>
        </flux:card>
    @else
        <flux:card wire:loading.class="opacity-60">
            <div class="mb-4 flex items-center justify-between">
                <flux:heading size="lg">
                    {{ $this->isTeacher ? 'Weekly Timetable' : $this->selectedClass->name.' Timetable' }}
                </flux:heading>
                @if (! $this->isTeacher && $this->selectedClass?->classTeacher)
                    <flux:text class="text-zinc-500">
                        Class Teacher: {{ $this->selectedClass->classTeacher->name }}
                    </flux:text>
                @endif
            </div>

            @if ($periods->isEmpty())
                <flux:text class="text-zinc-500">
                    No timetable entries for this class yet.
                    @can('create timetable')
                        Use "Add Timetable Entry" or "Import Timetable" above to get started.
                    @endcan
                </flux:text>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr>
                                <th
                                    class="border border-zinc-200 bg-zinc-50 px-3 py-2 text-left dark:border-zinc-700 dark:bg-zinc-800">
                                    Day
                                </th>
                                @foreach ($periods as $period)
                                    <th
                                        class="whitespace-nowrap border border-zinc-200 bg-zinc-50 px-3 py-2 text-center dark:border-zinc-700 dark:bg-zinc-800">
                                        {{ $period }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->days() as $day)
                                @continue(! isset($grid[$day]))
                                <tr>
                                    <td
                                        class="border border-zinc-200 bg-zinc-50 px-3 py-2 font-medium dark:border-zinc-700 dark:bg-zinc-800">
                                        {{ $day }}
                                    </td>
                                    @foreach ($periods as $period)
                                        @php($entry = $grid[$day][$period] ?? null)
                                        <td class="border border-zinc-200 px-3 py-2 text-center dark:border-zinc-700">
                                            @if ($entry)
                                                <a href="{{ route('timetable.show', $entry->id) }}"
                                                    class="block hover:underline" wire:navigate>
                                                    <span class="font-medium">{{ $entry->subject }}</span>
                                                    @if ($this->isTeacher)
                                                        <span class="block text-xs text-zinc-500">
                                                            {{ $entry->schoolClass?->name ?? $entry->class_name }}
                                                        </span>
                                                    @elseif ($entry->teacher)
                                                        <span class="block text-xs text-zinc-500">
                                                            {{ $entry->teacher->name }}
                                                        </span>
                                                    @endif
                                                    @if ($entry->room)
                                                        <span class="block text-xs text-zinc-500">{{ $entry->room }}</span>
                                                    @endif
                                                </a>
                                            @else
                                                <span class="text-zinc-300 dark:text-zinc-600">&mdash;</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </flux:card>
    @endif
</div>
