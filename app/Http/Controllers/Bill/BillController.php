<?php

namespace App\Http\Controllers\Bill;

use App\Actions\Bill\DeleteBillAction;
use App\Actions\Bill\GetBillsAction;
use App\Actions\Bill\StoreBillAction;
use App\Actions\Bill\UpdateBillAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bill\StoreBillRequest;
use App\Http\Requests\Bill\UpdateBillRequest;
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
    public function store(StoreBillRequest $request, StoreBillAction $action)
    {
        $action->handle($request->validated());

        return back()->with('success', 'Bill created successfully');
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
    public function edit() {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Bill $bill, UpdateBillRequest $request, UpdateBillAction $action)
    {
        //
        $action->handle($bill, $request->validated());

        return back()->with('success', 'Bill updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bill $bill, DeleteBillAction $action)
    {
        //
        $action->handle($bill);

        return back()->with('success', 'Bill deleted successfully');
    }
}
