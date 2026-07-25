<?php

namespace Modules\Curriculum\Http\Controllers;

use App\Http\Controllers\Controller;
use Flux\Flux;
use Illuminate\Http\Request;
use Modules\Curriculum\Models\Curriculum;

class CurriculumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $curricula = Curriculum::all();
        return view('curriculum::index', compact('curricula'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('curriculum::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required'
        ]);
        Curriculum::create($data);
        return back()->with('success');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('curriculum::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Curriculum $curriculum)
    {

        return view('curriculum::edit', compact('curriculum'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Curriculum $curriculum)
    {
        $data = $request->validate([
            "name" => 'required'
        ]);
        $curriculum->update($data);
        return redirect()->route('curriculum.index')->with('success', 'Curriculum updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $curriculum)
    {
        Curriculum::destroy($curriculum);
        return back()->with('success', 'Curriculum deleted successfully!');
    }
}
