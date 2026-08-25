<x-layouts::app :title="__('Subject Teachers')">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @can('edit subject')
            {{-- The picker is plain checkboxes underneath, so it still submits
            with JavaScript off; Alpine only adds the search, the running
            count and the "already assigned" hint on top. --}}
            <flux:card class="mb-6" x-data="subjectTeacherPicker()">
                <flux:heading size="lg" class="mb-2">Assign a Subject Teacher</flux:heading>
                <flux:text class="mb-6 text-zinc-500">
                    A teacher can enter results for the subjects they're assigned here - and only those. A class
                    teacher can enter results for every subject in their own class.
                </flux:text>

                <form action="{{ route('subject.teachers.store') }}" method="POST">
                    @csrf

                    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <flux:select name="class_id" label="Class" x-model="classId" required>
                            <flux:select.option value="">Select Class</flux:select.option>
                            @foreach ($classes as $schoolClass)
                                <flux:select.option value="{{ $schoolClass->id }}"
                                    :selected="old('class_id') == $schoolClass->id">
                                    {{ $schoolClass->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:select name="subject_id" label="Subject" x-model="subjectId" required>
                            <flux:select.option value="">Select Subject</flux:select.option>
                            @foreach ($subjects as $subject)
                                <flux:select.option value="{{ $subject->id }}"
                                    :selected="old('subject_id') == $subject->id">
                                    {{ $subject->name }}{{ $subject->code ? ' (' . $subject->code . ')' : '' }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div
                            class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-200 px-4 py-3 dark:border-zinc-700">
                            <div class="flex items-center gap-2">
                                <flux:heading>Teachers</flux:heading>
                                <flux:badge size="sm" color="zinc" x-show="selected.length === 0"
                                    style="display: none">
                                    None selected
                                </flux:badge>
                                <flux:badge size="sm" color="emerald" x-show="selected.length > 0"
                                    style="display: none">
                                    <span x-text="selected.length"></span> selected
                                </flux:badge>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <flux:input type="search" size="sm" icon="magnifying-glass"
                                    placeholder="Search teachers" x-model="search" class="w-56" />
                                <flux:button type="button" size="sm" variant="ghost" @click="selectVisible()">
                                    Select all
                                </flux:button>
                                <flux:button type="button" size="sm" variant="ghost" @click="clear()"
                                    x-show="selected.length > 0" style="display: none">
                                    Clear
                                </flux:button>
                            </div>
                        </div>

                        {{-- What's been picked so far, so a long list never hides
                        the answer to "who did I choose?". --}}
                        <div class="flex flex-wrap gap-2 border-b border-zinc-200 px-4 py-3 dark:border-zinc-700"
                            x-show="selected.length > 0" style="display: none">
                            <template x-for="id in selected" :key="id">
                                <button type="button" @click="toggle(id)"
                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-sm font-medium text-emerald-700 transition hover:bg-emerald-100 dark:bg-emerald-500/15 dark:text-emerald-300 dark:hover:bg-emerald-500/25">
                                    <span x-text="nameOf(id)"></span>
                                    <span aria-hidden="true">&times;</span>
                                    <span class="sr-only">Remove</span>
                                </button>
                            </template>
                        </div>

                        <div class="p-4">
                            @if ($teacherCards === [])
                                <flux:text class="text-zinc-500">
                                    No teachers on staff yet.
                                    <a href="{{ route('teacher.index') }}" class="underline">Add one first</a>.
                                </flux:text>
                            @else
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                    @foreach ($teacherCards as $card)
                                        <label x-show="matches('{{ $card['id'] }}')"
                                            :class="selected.includes('{{ $card['id'] }}') ?
                                                'border-emerald-500 bg-emerald-50/60 ring-1 ring-emerald-500 dark:border-emerald-500 dark:bg-emerald-500/10' :
                                                'border-zinc-200 bg-white hover:border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600'"
                                            class="flex cursor-pointer items-start gap-3 rounded-lg border p-3 transition has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-emerald-500">
                                            <input type="checkbox" name="teacher_ids[]"
                                                value="{{ $card['id'] }}" x-model="selected" class="sr-only" />

                                            <span
                                                :class="selected.includes('{{ $card['id'] }}') ?
                                                    'bg-emerald-500 text-white' :
                                                    'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300'"
                                                class="flex size-9 shrink-0 items-center justify-center rounded-full text-xs font-semibold">
                                                {{ $card['initials'] }}
                                            </span>

                                            <span class="min-w-0 flex-1">
                                                <span
                                                    class="block truncate text-sm font-medium text-zinc-900 dark:text-white">
                                                    {{ $card['name'] }}
                                                </span>
                                                <span class="block truncate text-xs text-zinc-500">
                                                    {{ $card['meta'] ?: 'Teacher' }}
                                                    @if ($card['load'] > 0)
                                                        · {{ $card['load'] }} subject{{ $card['load'] === 1 ? '' : 's' }}
                                                    @endif
                                                </span>
                                                <span class="mt-1 inline-flex text-xs font-medium text-amber-600 dark:text-amber-400"
                                                    x-show="isAssigned('{{ $card['id'] }}')" style="display: none">
                                                    Already teaches this
                                                </span>
                                            </span>

                                            <span
                                                :class="selected.includes('{{ $card['id'] }}') ?
                                                    'border-emerald-500 bg-emerald-500 text-white' :
                                                    'border-zinc-300 text-transparent dark:border-zinc-600'"
                                                class="flex size-5 shrink-0 items-center justify-center rounded-md border">
                                                <svg class="size-3.5" viewBox="0 0 20 20" fill="none"
                                                    stroke="currentColor" stroke-width="3"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 10.5 8 14.5 16 6" />
                                                </svg>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>

                                <flux:text class="mt-4 block text-center text-sm text-zinc-500"
                                    x-show="visible.length === 0" style="display: none">
                                    No teacher matches <span class="font-medium" x-text="search"></span>.
                                </flux:text>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                        <flux:text class="text-xs text-zinc-500">
                            Pick as many teachers as share the subject - assigning one never removes another.
                        </flux:text>
                        <flux:button type="submit" variant="primary" icon="plus"
                            ::disabled="selected.length === 0">
                            Assign
                        </flux:button>
                    </div>
                </form>
            </flux:card>
        @endcan

        <flux:card>
            <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                <flux:heading size="lg">Current Assignments</flux:heading>

                <form action="{{ route('subject.teachers.index') }}" method="GET" class="flex items-end gap-2">
                    <flux:select name="class_id" label="Class">
                        <flux:select.option value="">All classes</flux:select.option>
                        @foreach ($classes as $schoolClass)
                            <flux:select.option value="{{ $schoolClass->id }}"
                                :selected="request('class_id') == $schoolClass->id">
                                {{ $schoolClass->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:select name="teacher_id" label="Teacher">
                        <flux:select.option value="">All teachers</flux:select.option>
                        @foreach ($teachers as $teacher)
                            <flux:select.option value="{{ $teacher->id }}"
                                :selected="request('teacher_id') == $teacher->id">
                                {{ $teacher->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:button type="submit" icon="funnel" variant="ghost">Filter</flux:button>
                </form>
            </div>

            @forelse ($grouped as $className => $classAssignments)
                <div class="mb-6 last:mb-0">
                    <div class="mb-2 flex items-center gap-2">
                        <flux:heading>{{ $className }}</flux:heading>
                        <flux:badge size="sm" color="zinc">
                            {{ $classAssignments->count() }} assignment{{ $classAssignments->count() === 1 ? '' : 's' }}
                        </flux:badge>
                    </div>

                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Subject</flux:table.column>
                            <flux:table.column>Teacher</flux:table.column>
                            <flux:table.column>Actions</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($classAssignments as $assignment)
                                <flux:table.row>
                                    <flux:table.cell>{{ $assignment->subject?->name ?? '—' }}</flux:table.cell>
                                    <flux:table.cell>{{ $assignment->teacher?->name ?? '—' }}</flux:table.cell>
                                    <flux:table.cell>
                                        @can('edit subject')
                                            <form action="{{ route('subject.teachers.destroy', $assignment->id) }}"
                                                method="POST" onsubmit="return confirm('Remove this assignment?');">
                                                @csrf
                                                @method('DELETE')
                                                <flux:button type="submit" size="sm" icon="trash"
                                                    variant="ghost">remove</flux:button>
                                            </form>
                                        @endcan
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>
            @empty
                <flux:text class="text-zinc-500">No subject teachers assigned yet.</flux:text>
            @endforelse
        </flux:card>
    </div>

    {{-- Defined here rather than inline on the element: a long x-data
    attribute trips up Livewire's morph-aware Blade compilation. Runs before
    @fluxScripts boots Alpine at the end of the body. --}}
    <script>
        window.subjectTeacherPicker = () => ({
            search: '',
            selected: [],
            classId: @js(old('class_id', '')),
            subjectId: @js(old('subject_id', '')),
            teachers: @js($teacherCards),
            assigned: @js($alreadyAssigned),

            /** Teachers matching the current search, by name or by their department/staff number. */
            get visible() {
                const needle = this.search.trim().toLowerCase();

                if (needle === '') {
                    return this.teachers;
                }

                return this.teachers.filter(teacher => (teacher.name + ' ' + teacher.meta).toLowerCase().includes(needle));
            },

            matches(id) {
                return this.visible.some(teacher => teacher.id === id);
            },

            nameOf(id) {
                const teacher = this.teachers.find(teacher => teacher.id === id);

                return teacher ? teacher.name : '';
            },

            /** Whether this teacher already teaches the chosen class/subject pair. */
            isAssigned(id) {
                if (! this.classId || ! this.subjectId) {
                    return false;
                }

                return (this.assigned[this.classId + '-' + this.subjectId] || []).includes(id);
            },

            toggle(id) {
                this.selected = this.selected.includes(id)
                    ? this.selected.filter(value => value !== id)
                    : [...this.selected, id];
            },

            selectVisible() {
                this.selected = [...new Set([...this.selected, ...this.visible.map(teacher => teacher.id)])];
            },

            clear() {
                this.selected = [];
            },
        });
    </script>
</x-layouts::app>
