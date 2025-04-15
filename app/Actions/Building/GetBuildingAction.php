<?php

namespace App\Actions\User;

use App\Models\Building;

final class GetBuildingAction
{
    public function handle(string $buildingId): ?Building
    {
        $building = Building::whereId($buildingId)->with('bills')->first();

        return $building;
    }
}
