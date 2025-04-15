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
            $bill->update($data);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
