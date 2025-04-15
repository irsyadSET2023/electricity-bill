<?php

namespace App\Actions\User;

use App\Models\Building;

final class StoreBuildingAction
{
    public function handle(array $data): array
    {
        try {
            Building::create($data);
            return
                ["status_code" => 200, "data" => [
                    'success' => true,
                    'message' => 'Building Create Successfully',
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
