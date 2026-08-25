<?php

namespace Modules\Expenditure\Http\Controllers;

use App\Http\Controllers\Concerns\Sortable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Expenditure\Models\Expenditure;
use Modules\Expenditure\Models\ExpenditureCategory;

/**
 * What the school spends. Kept by the Accountant and read by the Director -
 * the outgoing counterpart to fee collection.
 */
class ExpenditureController extends Controller
{
    use Sortable;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_unless(Auth::user()->can('view expenditure'), 403);

        $query = Expenditure::with(['category', 'institution', 'recordedBy']);
        $this->scopeToViewer($query);

        $query
            ->when($request->filled('category_id'), fn ($q) => $q->where('expenditure_category_id', $request->integer('category_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('spent_on', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('spent_on', '<=', $request->date('to')));

        // Totals describe the filtered set, not the page - an Accountant
        // filtering to "Utilities, this term" wants that subtotal, not the
        // ten rows that happen to fit on screen.
        $totals = (clone $query)
            ->selectRaw('SUM(amount) as total, SUM(CASE WHEN status = ? THEN amount ELSE 0 END) as settled', ['paid'])
            ->first();

        $expenditures = $this->applySort(
            $query,
            sortable: ['spent_on', 'amount', 'status', 'title'],
            defaultColumn: 'spent_on',
            defaultDirection: 'desc',
        )->paginate(15)->withQueryString();

        return view('expenditure::index', [
            'expenditures' => $expenditures,
            'categories' => $this->scopedCategories(),
            'totalSpend' => (float) ($totals->total ?? 0),
            'totalSettled' => (float) ($totals->settled ?? 0),
            'byCategory' => $this->spendByCategory($request),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(Auth::user()->can('create expenditure'), 403);

        return view('expenditure::create', [
            'expenditure' => null,
            'categories' => $this->scopedCategories(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->can('create expenditure'), 403);

        $validated = $this->validated($request);

        Expenditure::create($validated + ['recorded_by' => Auth::id()]);

        return redirect()->route('expenditure.index')->with('success', 'Expenditure recorded successfully!');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        abort_unless(Auth::user()->can('view expenditure'), 403);

        $expenditure = $this->scopedExpenditure($id);

        return view('expenditure::show', compact('expenditure'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        abort_unless(Auth::user()->can('edit expenditure'), 403);

        return view('expenditure::edit', [
            'expenditure' => $this->scopedExpenditure($id),
            'categories' => $this->scopedCategories(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        abort_unless(Auth::user()->can('edit expenditure') || Auth::user()->can('update expenditure'), 403);

        $expenditure = $this->scopedExpenditure($id);
        $validated = $this->validated($request, $expenditure);

        $expenditure->update($validated);

        return redirect()->route('expenditure.show', $expenditure->id)->with('success', 'Expenditure updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        abort_unless(Auth::user()->can('delete expenditure'), 403);

        $expenditure = $this->scopedExpenditure($id);
        $expenditure->delete();

        return redirect()->route('expenditure.index')->with('success', 'Expenditure removed!');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Expenditure $expenditure = null): array
    {
        $validated = $request->validate([
            'expenditure_category_id' => 'nullable|exists:expenditure_categories,id',
            'title' => 'required|string|max:255',
            'payee' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'spent_on' => 'required|date',
            'payment_method' => 'required|in:'.implode(',', Expenditure::PAYMENT_METHODS),
            'reference' => 'nullable|string|max:255',
            'status' => 'required|in:'.implode(',', Expenditure::STATUSES),
            'notes' => 'nullable|string',
        ]);

        // Never trust a client-submitted institution - spending is always
        // filed against whichever school is currently active for the
        // Accountant recording it.
        $institutionId = isAdmin()
            ? ($expenditure?->institution_id ?? currentInstitution()?->id)
            : currentInstitution()?->id;

        abort_unless($institutionId, 422, 'No institution selected.');

        $validated['institution_id'] = $institutionId;

        // A nullable field that wasn't submitted at all is simply absent
        // from $validated, so it has to be normalised before it's read or
        // written.
        $validated['expenditure_category_id'] = $validated['expenditure_category_id'] ?? null;

        // 'exists' alone would let a crafted request file a spend under
        // another school's category.
        if ($validated['expenditure_category_id']) {
            $category = ExpenditureCategory::findOrFail($validated['expenditure_category_id']);
            abort_unless($category->institution_id === $institutionId, 403);
        }

        // Marking it paid stamps the moment the money left; moving it back
        // to pending/approved/cancelled clears that stamp.
        $validated['paid_at'] = $validated['status'] === 'paid'
            ? ($expenditure?->paid_at ?? now())
            : null;

        return $validated;
    }

    /**
     * Spend per category for the filtered period, for the summary strip on
     * the index - the question "where is the money going?" asked in the one
     * place an Accountant is already looking.
     *
     * @return Collection<int, object>
     */
    private function spendByCategory(Request $request)
    {
        $query = Expenditure::query();
        $this->scopeToViewer($query);

        return $query
            ->when($request->filled('from'), fn ($q) => $q->whereDate('spent_on', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('spent_on', '<=', $request->date('to')))
            ->where('status', '!=', 'cancelled')
            ->selectRaw('expenditure_category_id, SUM(amount) as total')
            ->groupBy('expenditure_category_id')
            ->orderByDesc('total')
            ->with('category')
            ->get();
    }

    private function scopeToViewer($query): void
    {
        if (isAdmin()) {
            return;
        }

        $query->where('institution_id', currentInstitution()?->id ?? 0);
    }

    private function scopedExpenditure(int $id): Expenditure
    {
        $query = Expenditure::with(['category', 'institution', 'recordedBy']);
        $this->scopeToViewer($query);

        return $query->findOrFail($id);
    }

    private function scopedCategories()
    {
        $query = ExpenditureCategory::query()->where('is_active', true);

        if (! isAdmin()) {
            $query->where('institution_id', currentInstitution()?->id ?? 0);
        }

        return $query->orderBy('name')->get();
    }
}
