<?php

namespace App\Actions\Building;

use App\Models\Building;
use Illuminate\Support\Facades\Log;

final class UpdateBuildingAction
{
    public function handle(Building $building, array $data): void
    {
        try {
            activity()
            ->performedOn($building)
            ->causedBy(auth()->user())
            ->withProperties(['data' => $data])
            ->log('Building updated');
            
            $building->update($data);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

        }
    }
}
