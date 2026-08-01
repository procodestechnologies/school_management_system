<?php

namespace Modules\Curriculum\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Curriculum\Models\Curriculum;

class CurriculumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(Auth::user()->can('view curriculum'), 403);

        $curricula = Curriculum::all();

        return view('curriculum::index', compact('curricula'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(Auth::user()->can('create curriculum'), 403);

        return view('curriculum::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->can('create curriculum'), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,dismissed',
        ]);
        Curriculum::create($data);

        return redirect()->route('curriculum.index')->with('success', 'Curriculum created successfully!');
    }

    /**
     * Show the specified resource.
     */
    public function show(Curriculum $curriculum)
    {
        abort_unless(Auth::user()->can('view curriculum'), 403);

        $curriculum->load('institutions');

        return view('curriculum::show', compact('curriculum'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Curriculum $curriculum)
    {
        abort_unless(Auth::user()->can('edit curriculum'), 403);

        return view('curriculum::edit', compact('curriculum'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Curriculum $curriculum)
    {
        abort_unless(Auth::user()->can('update curriculum'), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,dismissed',
        ]);
        $curriculum->update($data);

        return redirect()->route('curriculum.index')->with('success', 'Curriculum updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $curriculum)
    {
        abort_unless(Auth::user()->can('delete curriculum'), 403);

        Curriculum::destroy($curriculum);

        return back()->with('success', 'Curriculum deleted successfully!');
    }
}
