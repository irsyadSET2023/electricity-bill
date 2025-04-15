<?php

namespace App\Actions\Building;

use App\Models\Building;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;

final class GetBuildingsAction
{
    public function handle(Request $request): array
    {
        $query = Building::query()
            ->with('user'); // Eager load the user relationship

        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('buildings.name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Apply sorting
        $sortField = $request->input('sort_by', 'updated_at');
        $sortOrder = $request->input('sort_order', 'desc');
        
        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['name', 'building_type', 'state', 'created_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortOrder);
        }
        else{
            $query->orderBy('updated_at', 'desc');
        }

        // Get paginated results
        $buildings = $query->paginate(10)->withQueryString();
  
        return [
            'buildings' => $buildings->through(fn (Building $building) => [
                'id' => $building->id,
                'name' => $building->name,
                'building_type' => $building->building_type,
                'state' => $building->state,
                'created_at' => $building->created_at,
                'owner' => [
                    'id' => $building->user->id,
                    'name' => $building->user->name,
                    'email' => $building->user->email,
                ],
            ]),
            'filters' => [
                'search' => $search,
                'sort_by' => $sortField,
                'sort_order' => $sortOrder
            ]
        ];
    }
}
