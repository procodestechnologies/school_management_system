<x-layouts::app :title="__('Review Receipt')">
    <div class="p-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-4xl">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                    <h4 class="text-lg font-semibold text-gray-900 mb-0">Receipt</h4>
                </div>
                <div class="p-6">
                    @if ($isPdf)
                        <embed src="{{ $receiptUrl }}" type="application/pdf" class="w-full h-96 rounded-md border border-gray-200">
                        <a href="{{ $receiptUrl }}" target="_blank" class="mt-2 inline-block text-sm text-blue-600 hover:underline">
                            Open PDF in new tab
                        </a>
                    @else
                        <img src="{{ $receiptUrl }}" alt="Uploaded receipt" class="w-full rounded-md border border-gray-200">
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 rounded-t-lg">
                    <h4 class="text-lg font-semibold text-gray-900 mb-0">Review &amp; Confirm</h4>
                    <small class="text-sm text-gray-500">
                        @if ($matchedStudent)
                            Matched by admission number - check the details below before saving.
                        @else
                            No admission number matched a student automatically - pick one below.
                        @endif
                    </small>
                </div>

                <form action="{{ route('feemanagement.receipts.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="p-6 space-y-4">
                        <div>
                            <flux:select name="student_id" id="student_id" label="Student" required>
                                <flux:select.option value="">Select student</flux:select.option>
                                @foreach ($students as $studentDetails)
                                    <flux:select.option value="{{ $studentDetails->student_id }}"
                                        {{ old('student_id', $matchedStudent?->student_id) == $studentDetails->student_id ? 'selected' : '' }}>
                                        {{ $studentDetails->student?->name }} ({{ $studentDetails->admission_number ?? '—' }})
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            @error('student_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @if ($extracted['admission_number'] && ! $matchedStudent)
                                <p class="mt-1 text-xs text-amber-600">
                                    Receipt showed admission number "{{ $extracted['admission_number'] }}" - no match found.
                                </p>
                            @endif
                        </div>

                        <div>
                            <flux:select name="fee_id" id="fee_id" label="Apply payment to">
                                @forelse ($fees as $fee)
                                    <flux:select.option value="{{ $fee->id }}">
                                        {{ $fee->title }} — balance {{ number_format($fee->balance, 2) }}
                                    </flux:select.option>
                                @empty
                                    <flux:select.option value="">No fee records for this student yet</flux:select.option>
                                @endforelse
                            </flux:select>
                            @error('fee_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <flux:input type="number" step="0.01" min="0.01" name="amount" label="Amount"
                                value="{{ old('amount', $extracted['amount']) }}" required />
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <flux:input type="date" name="paid_at" label="Date Paid"
                                    value="{{ old('paid_at', $extracted['paid_at']) }}" />
                            </div>
                            <div>
                                <flux:input type="text" name="payment_method" label="Method"
                                    value="{{ old('payment_method', $extracted['payment_method']) }}" />
                            </div>
                        </div>

                        <div>
                            <flux:input type="text" name="reference" label="Reference"
                                value="{{ old('reference', $extracted['reference']) }}" />
                        </div>

                        @if ($extracted['student_name'])
                            <p class="text-xs text-gray-500">Receipt payer/student name: {{ $extracted['student_name'] }}</p>
                        @endif
                    </div>

                    <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                        <flux:button href="{{ route('feemanagement.index') }}" wire:navigate>Cancel</flux:button>
                        <flux:button type="submit" variant="primary">Save Payment</flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('student_id')?.addEventListener('change', function () {
            const feeSelect = document.getElementById('fee_id');
            feeSelect.innerHTML = '';

            if (! this.value) {
                feeSelect.innerHTML = '<option value="">No fee records for this student yet</option>';
                return;
            }

            fetch(@json(route('feemanagement.receipts.student-fees')) + '?student_id=' + encodeURIComponent(this.value))
                .then(response => response.json())
                .then(fees => {
                    if (fees.length === 0) {
                        feeSelect.innerHTML = '<option value="">No fee records for this student yet</option>';
                        return;
                    }

                    fees.forEach(fee => {
                        const option = document.createElement('option');
                        option.value = fee.id;
                        option.textContent = fee.label;
                        feeSelect.appendChild(option);
                    });
                });
        });
    </script>
</x-layouts::app>
