<x-layouts::app :title="__(config('parent.name'))">
    <div class="p-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Header -->
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg flex justify-between items-center">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-0">{{ config('parent.name') }}</h4>
                    <small class="text-sm text-gray-500">Manage parents and their students</small>
                </div>
                <flux:button variant="primary" href="{{ route('parent.create') }}">
                    Add Parent
                </flux:button>
            </div>

            <!-- Table -->
            <div class="p-6">
                @if($parents->count() > 0)
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Parent</flux:table.column>
                            <flux:table.column>Contact</flux:table.column>
                            <flux:table.column>Students</flux:table.column>
                            <flux:table.column>Status</flux:table.column>
                            <flux:table.column align="end">Actions</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach($parents as $parent)
                                <flux:table.row>
                                    <!-- Parent Info -->
                                    <flux:table.cell>
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                                                {{ $parent->initials() }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $parent->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $parent->email }}</div>
                                            </div>
                                        </div>
                                    </flux:table.cell>

                                    <!-- Contact -->
                                    <flux:table.cell>
                                        <div class="text-sm">{{ $parent->parent->parent_phone ?? 'No phone' }}</div>
                                        <div class="text-xs text-gray-500">{{ $parent->parent->parent_occupation ?? 'N/A' }}</div>
                                    </flux:table.cell>

                                    <!-- Students -->
                                    <flux:table.cell>
                                        @if($parent->children->count() > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($parent->children as $student)
                                                    <flux:badge color="blue" size="sm">
                                                        {{ $student->student->name ?? 'Unknown' }}
                                                    </flux:badge>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">No students</span>
                                        @endif
                                    </flux:table.cell>

                                    <!-- Status -->
                                    <flux:table.cell>
                                        @if($parent->children->count() > 0)
                                            <flux:badge color="green" size="sm">Active</flux:badge>
                                        @else
                                            <flux:badge color="gray" size="sm">Inactive</flux:badge>
                                        @endif
                                    </flux:table.cell>

                                    <!-- Actions -->
                                    <flux:table.cell align="end">
                                        <div class="flex justify-end gap-2">
                                            <flux:button variant="ghost" size="sm" href="{{ route('parent.show', $parent->id) }}">
                                                View
                                            </flux:button>
                                            <flux:button variant="ghost" size="sm" href="{{ route('parent.edit', $parent->id) }}">
                                                Edit
                                            </flux:button>
                                            <form action="{{ route('parent.destroy', $parent->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <flux:button variant="danger" size="sm" type="submit" onclick="return confirm('Are you sure?')">
                                                    Delete
                                                </flux:button>
                                            </form>
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>

                    <!-- Pagination -->
                    @if(method_exists($parents, 'links'))
                        <div class="mt-4">
                            {{ $parents->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 mb-2">No parents found</h3>
                        <p class="text-sm text-gray-500 mb-6">Get started by creating a new parent.</p>
                        <flux:button variant="primary" href="{{ route('parent.create') }}">
                            Add Parent
                        </flux:button>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            @if($parents->count() > 0)
                <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-between text-sm text-gray-500">
                    <span>Showing {{ $parents->count() }} parent(s)</span>
                    <span>Total students: {{ $parents->sum(fn($p) => $p->children->count()) }}</span>
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>