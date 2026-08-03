<x-layouts::app :title="__(config('result.name'))">
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

        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('result.index') }}" class="flex flex-wrap items-center gap-2">
                <flux:select name="class_id"
                    onchange="document.getElementById('examination_id').value=''; this.form.submit()">
                    <flux:select.option value="">All Classes</flux:select.option>
                    @foreach ($classes as $schoolClass)
                        <flux:select.option value="{{ $schoolClass->id }}"
                            :selected="request('class_id') == $schoolClass->id">
                            {{ $schoolClass->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select id="examination_id" name="examination_id" onchange="this.form.submit()">
                    <flux:select.option value="">
                        {{ request('class_id') && $examinations->isEmpty() ? 'No examinations for this class' : 'All Examinations' }}
                    </flux:select.option>
                    @foreach ($examinations as $examination)
                        <flux:select.option value="{{ $examination->id }}"
                            :selected="request('examination_id') == $examination->id">
                            {{ $examination->title }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </form>

            @can('create result')
                <flux:button href="{{ route('result.create') }}">Add Result</flux:button>
            @endcan
        </div>

        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Student</flux:table.column>
                    <flux:table.column>Class</flux:table.column>
                    <flux:table.column>Examination</flux:table.column>
                    <flux:table.column>Marks</flux:table.column>
                    <flux:table.column>Grade</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($results as $result)
                        <flux:table.row>
                            <flux:table.cell>{{ $result->student?->name }}</flux:table.cell>
                            <flux:table.cell>{{ $result->schoolClass?->name }}</flux:table.cell>
                            <flux:table.cell>{{ $result->examination?->title }}</flux:table.cell>
                            <flux:table.cell>
                                {{ $result->marks_obtained }} / {{ $result->examination?->total_marks }}
                            </flux:table.cell>
                            <flux:table.cell>{{ $result->grade ?? '—' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:button href="{{ route('result.show', $result->id) }}" icon="eye"
                                    variant="primary" color="emerald">view</flux:button>
                                @can('edit result')
                                    <flux:button href="{{ route('result.edit', $result->id) }}" icon="pencil"
                                        variant="primary" color="yellow">edit</flux:button>
                                @endcan
                                @can('delete result')
                                    <form action="{{ route('result.destroy', $result->id) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Remove this result?');">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" icon="trash" variant="primary"
                                            color="red">delete</flux:button>
                                    </form>
                                @endcan
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center text-gray-500">
                                No results found.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</x-layouts::app>
