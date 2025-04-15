<?php

namespace App\Http\Controllers\Bill;

use App\Actions\Bill\GetBillsAction;
use App\Actions\Bill\StoreBillAction;
use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, GetBillsAction $action): Response
    {
        $response = $action->handle($request);
        return Inertia::render('bill/Index', $response);
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
    public function store(Request $request, StoreBillAction $action)
    {
        $response = $action->handle($request->validate());
        return Inertia::render('bill/Index', $response);
    }

    /**
     * Display the specified resource.
     */
    public function show(Bill $bill)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bill $bill)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bill $bill)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bill $bill)
    {
        //
    }
}
