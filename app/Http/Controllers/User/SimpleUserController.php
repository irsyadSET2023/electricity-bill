<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SimpleUserController extends Controller
{
    //Will Implement pagination later
    public function __invoke()
    {
        $users = User::select('id', 'name')->get();
        return response()->json($users);
    }
}

