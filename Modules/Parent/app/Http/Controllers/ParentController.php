<?php

namespace Modules\Parent\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Check if user is admin
        if (isAdmin()) {
            // Admin gets all parents from all institutions
            $parents = User::role("Parent")
                ->with(['children' => function ($query) {
                    $query->with('student', 'institution');
                }])
                ->get();

            $institution = null;

            return view('parent::index', compact('parents', 'institution'));
        }

        // Regular user - get parents from their institution
        $institution = $user->institution()->first();

        // Handle case where user has no institution
        if (!$institution) {
            return view('parent::index', [
                'parents' => collect(),
                'institution' => null
            ])->with('error', 'No institution found for this user.');
        }

        // Get parents with their students (children) eager loaded
        $parents = $institution->parents()
            ->with(['children' => function ($query) use ($institution) {
                $query->where('institution_id', $institution->id)
                    ->with('student'); // Load the student user details
            }])
            ->get();

        return view('parent::index', compact('parents', 'institution'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('parent::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('parent::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('parent::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
