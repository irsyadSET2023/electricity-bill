<?php

namespace App\Actions\Bill;

use App\Models\Bill;
use Illuminate\Support\Facades\Log;

final class UpdateBillAction
{
    public function handle(Bill $bill, array $data): void
    {
        // Will have better error handling
        try {
            activity()
            ->performedOn($bill)
            ->causedBy(auth()->user())
            ->withProperties(['data' => $data])
            ->log('Bill updated');
            $bill->update($data);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
