<?php

namespace App\Actions\Bill;

use App\Models\Bill;

final class GetBillAction
{
    public function handle(string $billId): ?Bill
    {
        $bill = Bill::whereId($billId)->with('building')->first();

        return $bill;
    }
}
