<?php

namespace App\Actions\Bill;

use App\Models\Bill;
use Illuminate\Support\Facades\Log;

final class StoreBillAction
{
    public function handle(array $data): void
    {
        try {
            activity()->log('Bill created', [
                'bill' => $data,
            ])->causedBy(auth()->user());
            Bill::create($data);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
