<?php

namespace App\Actions\Bill;

use App\Models\Bill;
use Illuminate\Support\Facades\Log;

final class DeleteBillAction
{
    public function handle(Bill $bill): void
    {
        // Will have better error handling
        try {
            activity()
            ->performedOn($bill)
            ->causedBy(auth()->user())
            ->log('Bill deleted');
            $bill->delete();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
