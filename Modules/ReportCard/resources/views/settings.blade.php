<x-layouts::app :title="__('Report Card Settings')">
    <div class="p-4">

        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (isAdmin())
            <flux:card class="mb-6">
                <form method="GET" action="{{ route('reportcard.settings') }}" class="flex items-center gap-2">
                    <flux:select name="institution_id" onchange="this.form.submit()">
                        <flux:select.option value="">Select Institution</flux:select.option>
                        @foreach ($institutions as $inst)
                            <flux:select.option value="{{ $inst->id }}"
                                :selected="$institution?->id === $inst->id">
                                {{ $inst->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </form>
            </flux:card>
        @endif

        @if (! $institution)
            <flux:card class="text-center py-10">
                <flux:text class="text-zinc-500">
                    @if (isAdmin())
                        Select an institution above to manage its report card settings.
                    @else
                        No institution found for your account.
                    @endif
                </flux:text>
            </flux:card>
        @else
            <flux:card class="mb-6">
                <flux:heading size="lg" class="mb-4">Grading Scale</flux:heading>
                <flux:text class="text-zinc-500 mb-4">
                    Define the percentage bands used to compute each result's grade automatically.
                </flux:text>

                @if ($gradingBands->isNotEmpty())
                    <flux:table class="mb-4">
                        <flux:table.columns>
                            <flux:table.column>Min %</flux:table.column>
                            <flux:table.column>Max %</flux:table.column>
                            <flux:table.column>Grade</flux:table.column>
                            <flux:table.column>Remark</flux:table.column>
                            <flux:table.column>Actions</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($gradingBands as $band)
                                <flux:table.row>
                                    <flux:table.cell>{{ $band->min_percentage }}</flux:table.cell>
                                    <flux:table.cell>{{ $band->max_percentage }}</flux:table.cell>
                                    <flux:table.cell>{{ $band->grade }}</flux:table.cell>
                                    <flux:table.cell>{{ $band->remark ?? '—' }}</flux:table.cell>
                                    <flux:table.cell>
                                        <form action="{{ route('reportcard.gradingbands.destroy', $band->id) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirm('Remove this grading band?');">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" size="sm" icon="trash"
                                                variant="ghost">delete</flux:button>
                                        </form>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                @else
                    <flux:text class="text-zinc-500 mb-4">No grading bands set up yet.</flux:text>
                @endif

                <form action="{{ route('reportcard.gradingbands.store') }}" method="POST"
                    class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    @csrf
                    <input type="hidden" name="institution_id" value="{{ $institution->id }}">

                    <flux:input type="number" step="0.01" name="min_percentage" label="Min %" min="0"
                        max="100" required />
                    <flux:input type="number" step="0.01" name="max_percentage" label="Max %" min="0"
                        max="100" required />
                    <flux:input type="text" name="grade" label="Grade" maxlength="10" required />
                    <flux:input type="text" name="remark" label="Remark" placeholder="e.g. Excellent" />
                    <flux:button type="submit" variant="primary">Add Band</flux:button>
                </form>
            </flux:card>

            <flux:card>
                <flux:heading size="lg" class="mb-2">Report Card Template</flux:heading>
                <flux:text class="text-zinc-500 mb-4">
                    Customize the opening and closing text on generated report cards. Available placeholders:
                    <code>@{{student_name}}</code>, <code>@{{institution_name}}</code>,
                    <code>@{{class_name}}</code>, <code>@{{term}}</code>,
                    <code>@{{mean_percentage}}</code>, <code>@{{mean_grade}}</code>.
                </flux:text>

                <form action="{{ route('reportcard.settings.template') }}" method="POST"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="institution_id" value="{{ $institution->id }}">

                    <div class="md:col-span-2">
                        <flux:textarea name="opening_text" rows="3" label="Opening Text">{{ old('opening_text', $template->opening_text) }}</flux:textarea>
                    </div>
                    <div class="md:col-span-2">
                        <flux:textarea name="closing_text" rows="3" label="Closing Text">{{ old('closing_text', $template->closing_text) }}</flux:textarea>
                    </div>
                    <flux:input type="text" name="signatory_name" label="Signatory Name"
                        value="{{ old('signatory_name', $template->signatory_name) }}" placeholder="e.g. Jane Doe" />
                    <flux:input type="text" name="signatory_title" label="Signatory Title"
                        value="{{ old('signatory_title', $template->signatory_title) }}"
                        placeholder="e.g. Principal" />

                    <div class="md:col-span-2 flex justify-end">
                        <flux:button type="submit" variant="primary">Save Template</flux:button>
                    </div>
                </form>
            </flux:card>
        @endif
    </div>
</x-layouts::app>
