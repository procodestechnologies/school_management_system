<?php

namespace Modules\Staff\Http\Controllers;

use App\Http\Controllers\Concerns\Sortable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Staff\Models\StaffDetails;
use Modules\Staff\Models\StaffPayment;

/**
 * Payroll: what each staff member is paid, month by month. Owned by the
 * Accountant (and the Director), alongside fee management.
 */
class StaffPaymentController extends Controller
{
    use Sortable;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_unless(Auth::user()->can('view payroll'), 403);

        $query = StaffPayment::with(['staff', 'institution']);

        if (! isAdmin()) {
            $query->where('institution_id', currentInstitution()?->id ?? 0);
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($period = $request->string('period')->toString()) {
            $query->whereYear('period', Carbon::parse($period.'-01')->year)
                ->whereMonth('period', Carbon::parse($period.'-01')->month);
        }

        $totals = (clone $query)->selectRaw('SUM(net_amount) as total, SUM(CASE WHEN status = ? THEN net_amount ELSE 0 END) as paid', ['paid'])->first();

        $payments = $this->applySort(
            $query,
            sortable: ['period', 'net_amount', 'status'],
            defaultColumn: 'period',
            defaultDirection: 'desc',
        )->paginate(10)->withQueryString();

        return view('staff::payments.index', [
            'payments' => $payments,
            'totalPayroll' => (float) ($totals->total ?? 0),
            'totalPaid' => (float) ($totals->paid ?? 0),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(Auth::user()->can('create payroll'), 403);

        return view('staff::payments.create', [
            'staffMembers' => $this->payableStaff(),
            'payment' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->can('create payroll'), 403);

        $validated = $request->validate($this->rules());

        $staff = $this->findStaff($validated['staff_details_id']);
        $period = Carbon::parse($validated['period'].'-01')->startOfMonth();

        if ($this->periodAlreadyRecorded($staff, $period)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A payment for '.$staff->name.' in '.$period->format('F Y').' already exists.');
        }

        try {
            StaffPayment::create($this->payload($validated, $staff, $period) + [
                'recorded_by' => Auth::id(),
            ]);

            return redirect()->route('staff.payments.index')->with('success', 'Staff payment recorded successfully!');
        } catch (\Exception $e) {
            Log::error('Staff payment creation failed: '.$e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to record staff payment: '.$e->getMessage());
        }
    }

    /**
     * Show the specified resource.
     */
    public function show(StaffPayment $payment)
    {
        abort_unless(Auth::user()->can('view payroll'), 403);

        $this->authorizeAccessTo($payment);

        $payment->load(['staff', 'institution', 'recordedBy']);

        return view('staff::payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StaffPayment $payment)
    {
        abort_unless(Auth::user()->can('edit payroll'), 403);

        $this->authorizeAccessTo($payment);

        return view('staff::payments.edit', [
            'payment' => $payment,
            'staffMembers' => $this->payableStaff(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StaffPayment $payment)
    {
        abort_unless(Auth::user()->can('update payroll'), 403);

        $this->authorizeAccessTo($payment);

        $validated = $request->validate($this->rules());

        $staff = $this->findStaff($validated['staff_details_id']);
        $period = Carbon::parse($validated['period'].'-01')->startOfMonth();

        if ($this->periodAlreadyRecorded($staff, $period, $payment)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A payment for '.$staff->name.' in '.$period->format('F Y').' already exists.');
        }

        try {
            $payment->update($this->payload($validated, $staff, $period, $payment));

            return redirect()->route('staff.payments.show', $payment)->with('success', 'Staff payment updated successfully!');
        } catch (\Exception $e) {
            Log::error('Staff payment update failed: '.$e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update staff payment: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StaffPayment $payment)
    {
        abort_unless(Auth::user()->can('delete payroll'), 403);

        $this->authorizeAccessTo($payment);

        $payment->delete();

        return redirect()->route('staff.payments.index')->with('success', 'Staff payment removed successfully!');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'staff_details_id' => 'required|exists:staff_details,id',
            'period' => ['required', 'date_format:Y-m'],
            'gross_amount' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,mobile_money,cheque',
            'reference' => 'nullable|string|max:255',
            'status' => 'required|in:pending,paid,cancelled',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Build the stored attributes, with the net worked out from the parts
     * rather than trusted from the form.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function payload(array $validated, StaffDetails $staff, Carbon $period, ?StaffPayment $payment = null): array
    {
        $gross = (float) $validated['gross_amount'];
        $allowances = (float) ($validated['allowances'] ?? 0);
        $deductions = (float) ($validated['deductions'] ?? 0);

        return [
            'staff_details_id' => $staff->id,
            'institution_id' => $staff->institution_id,
            'period' => $period,
            'gross_amount' => $gross,
            'allowances' => $allowances,
            'deductions' => $deductions,
            'net_amount' => StaffPayment::calculateNet($gross, $allowances, $deductions),
            'payment_method' => $validated['payment_method'],
            'reference' => $validated['reference'] ?? null,
            'status' => $validated['status'],
            // Marking it paid stamps the moment it happened; moving it back
            // to pending/cancelled clears that stamp.
            'paid_at' => $validated['status'] === 'paid'
                ? ($payment?->paid_at ?? now())
                : null,
            'notes' => $validated['notes'] ?? null,
        ];
    }

    /**
     * Staff the current viewer is allowed to pay - their own institution's,
     * and only those still employed.
     */
    private function payableStaff()
    {
        $query = StaffDetails::query()->orderBy('name');

        if (! isAdmin()) {
            $query->where('institution_id', currentInstitution()?->id ?? 0);
        }

        return $query->get();
    }

    /**
     * Resolve the selected staff member, refusing anyone outside the
     * viewer's institution - 'exists' alone would let a crafted request
     * pay another school's staff.
     */
    private function findStaff(int|string $id): StaffDetails
    {
        $staff = StaffDetails::findOrFail($id);

        if (! isAdmin()) {
            abort_unless($staff->institution_id === currentInstitution()?->id, 403);
        }

        return $staff;
    }

    /**
     * Whether this staff member already has a payslip for the given month.
     */
    private function periodAlreadyRecorded(StaffDetails $staff, Carbon $period, ?StaffPayment $ignoring = null): bool
    {
        return StaffPayment::where('staff_details_id', $staff->id)
            ->whereDate('period', $period->toDateString())
            ->when($ignoring, fn ($query) => $query->whereKeyNot($ignoring->getKey()))
            ->exists();
    }

    /**
     * Ensure a non-admin viewer only manages payroll from their currently
     * active institution.
     */
    private function authorizeAccessTo(StaffPayment $payment): void
    {
        if (isAdmin()) {
            return;
        }

        abort_unless($payment->institution_id === currentInstitution()?->id, 403);
    }
}
