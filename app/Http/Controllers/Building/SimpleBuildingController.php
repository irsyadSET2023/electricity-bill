<?php

namespace App\Http\Controllers\Building;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\Request;

class SimpleBuildingController extends Controller
{
    //
    //Will Implement pagination later

    public function __invoke()
    {
        $buildings = Building::select('id', 'name')->get();
        return response()->json($buildings);
    }
}
