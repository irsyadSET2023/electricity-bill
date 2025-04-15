<?php

namespace App\Actions\Building;

use App\Models\Building;
use Illuminate\Support\Facades\Log;

final class UpdateBuildingAction
{
    public function handle(Building $building, array $data): void
    {
        try {
            $building->update($data);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

        }
    }
}
