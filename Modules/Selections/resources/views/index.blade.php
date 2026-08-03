<x-layouts::app :title="__('My Subjects')">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if (! $studentDetails)
            <flux:card class="text-center py-10">
                <flux:text class="text-zinc-500">
                    Your student profile isn't linked to an institution yet. Please contact your school.
                </flux:text>
            </flux:card>
        @else
            <flux:card class="mb-6">
                <flux:heading size="lg" class="mb-2">Compulsory Subjects</flux:heading>
                <flux:text class="text-zinc-500 mb-4">
                    Every student at {{ $institution->name }} takes these subjects.
                </flux:text>

                @if ($compulsorySubjects->isEmpty())
                    <flux:text class="text-zinc-500">No compulsory subjects have been set up yet.</flux:text>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach ($compulsorySubjects as $subject)
                            <flux:badge color="amber">{{ $subject->name }}</flux:badge>
                        @endforeach
                    </div>
                @endif
            </flux:card>

            <flux:card>
                <flux:heading size="lg" class="mb-2">Elective Subjects</flux:heading>
                <flux:text class="text-zinc-500 mb-4">
                    Choose your favorite subjects &mdash;
                    @if ($institution->max_electives)
                        between {{ $institution->min_electives }} and {{ $institution->max_electives }}.
                    @else
                        at least {{ $institution->min_electives }}.
                    @endif
                </flux:text>

                @if ($electiveSubjects->isEmpty())
                    <flux:text class="text-zinc-500">No elective subjects are available yet.</flux:text>
                @else
                    <form action="{{ route('selections.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                            @foreach ($electiveSubjects as $subject)
                                <flux:checkbox name="subject_ids[]" value="{{ $subject->id }}"
                                    label="{{ $subject->name }}" :checked="in_array($subject->id, $selectedIds)" />
                            @endforeach
                        </div>

                        <div class="flex justify-end">
                            <flux:button type="submit" variant="primary">Save My Subjects</flux:button>
                        </div>
                    </form>
                @endif
            </flux:card>
        @endif
    </div>
</x-layouts::app>
