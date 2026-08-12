<x-layouts::app :title="__(config('staff.name'))">
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

        <div class="mb-2 flex flex-row justify-between">
            @can('create staff')
                <flux:button href="{{ route('staff.create') }}">Add Staff</flux:button>
            @endcan
            @can('view payroll')
                <flux:button href="{{ route('staff.payments.index') }}" icon="banknotes" variant="ghost">
                    Payroll
                </flux:button>
            @endcan
        </div>

        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <x-sortable-column column="name">Name</x-sortable-column>
                    <x-sortable-column column="staff_number">Staff No.</x-sortable-column>
                    <x-sortable-column column="job_title">Job Title</x-sortable-column>
                    <x-sortable-column column="department">Department</x-sortable-column>
                    <flux:table.column>Phone</flux:table.column>
                    <x-sortable-column column="salary">Salary</x-sortable-column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($staff as $member)
                        <flux:table.row>
                            <flux:table.cell>
                                {{ $member->name }}
                                @if ($member->hasSystemAccess())
                                    <flux:text class="text-zinc-500 text-xs">
                                        {{ $member->user?->getRoleNames()->first() ?? 'System user' }}
                                    </flux:text>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>{{ $member->staff_number ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $member->job_title ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $member->department ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $member->phone ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $member->salary ? number_format($member->salary, 2) : '—' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$member->is_active ? 'emerald' : 'red'">
                                    {{ ucfirst(str_replace('_', ' ', $member->status)) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button href="{{ route('staff.show', $member) }}" icon="eye" variant="primary"
                                    color="emerald">
                                    view
                                </flux:button>
                                @can('edit staff')
                                    <flux:button href="{{ route('staff.edit', $member) }}" icon="pencil" variant="primary"
                                        color="yellow">
                                        edit
                                    </flux:button>
                                @endcan
                                @can('delete staff')
                                    <form action="{{ route('staff.destroy', $member) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Remove this staff member?');">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" icon="trash" variant="primary" color="red">
                                            delete
                                        </flux:button>
                                    </form>
                                @endcan
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="8" class="text-center text-gray-500">
                                No staff found.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
            <div class="mt-4">
                {{ $staff->links() }}
            </div>
        </flux:card>
    </div>
</x-layouts::app>
