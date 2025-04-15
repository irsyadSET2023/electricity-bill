<?php

namespace App\Actions\Building;

use App\Models\Building;
use Illuminate\Support\Facades\Log;

final class DeleteBuildingAction
{
    public function handle(Building $building): void
    {
        try {
            activity()
            ->performedOn($building)
            ->causedBy(auth()->user())
            ->log('Building deleted');
            $building->delete();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

        }
    }
}
