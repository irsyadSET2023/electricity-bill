<?php

namespace App\Http\Controllers\Building;

use App\Actions\Building\DeleteBuildingAction;
use App\Actions\Building\GetBuildingsAction;
use App\Actions\Building\StoreBuildingAction;
use App\Actions\Building\UpdateBuildingAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Building\StoreBuildingRequest;
use App\Http\Requests\Building\UpdateBuildingRequest;
use App\Models\Building;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BuildingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, GetBuildingsAction $action)
    {
        $response = $action->handle($request);
       return Inertia::render('building/Index', $response);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBuildingRequest $request, StoreBuildingAction $action)
    {
        //

        $storeBuildingData = $request->validated();
        $action->handle($storeBuildingData);
        return back()->with('success', 'Building created successfully');

      
    }

    /**
     * Display the specified resource.
     */
    public function show(Building $building)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Building $building)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBuildingRequest $request, Building $building, UpdateBuildingAction $action)
    {
        //
        $updateBuildingData = $request->validated();
        $action->handle($building, $updateBuildingData);
        return back()->with('success', 'Building updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Building $building, DeleteBuildingAction $action)
    {
        //
        $action->handle($building);
        return back()->with('success', 'Building deleted successfully');
    }
}
