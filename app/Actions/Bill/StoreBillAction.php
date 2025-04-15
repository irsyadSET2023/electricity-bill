<?php

namespace App\Actions\Bill;

use App\Models\Bill;

final class StoreBillAction
{
    public function handle(array $data): array
    {
        try {
            Bill::create($data);
            return
                ["status_code" => 200, "data" => [
                    'success' => true,
                    'message' => 'Bill Create Successfully',
                    'data' => null
                ]];
        } catch (\Throwable $th) {
            return [
                "status_code" => 500,
                "data" => [
                    'success' => false,
                    'message' => $th->getMessage(),
                    'data' => null
                ]
            ];
        }
    }
}
