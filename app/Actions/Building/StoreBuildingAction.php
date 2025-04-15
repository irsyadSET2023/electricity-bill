<?php

namespace App\Actions\Building;

use App\Models\Building;
use Illuminate\Support\Facades\Log;

final class StoreBuildingAction
{
    public function handle(array $data): void
    {
        try {
            Building::create($data);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
   
        }
    }
}
