<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //

    public function sendSuccess(int $status, array $data)
    {

        response()->json($data, $status);
    }

    public function sendError(int $status, array $data)
    {
        response()->json($data, $status);
    }
}
