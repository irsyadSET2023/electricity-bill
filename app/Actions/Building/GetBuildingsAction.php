<?php

namespace App\Actions\Building;

use App\Models\Building;
use Illuminate\Http\Request;

final class GetBuildingsAction
{
    private const RESIDENTIAL_FIRST_BLOCK_LIMIT = 200;

    private const COMMERCIAL_FIRST_BLOCK_LIMIT = 200;

    private const RESIDENTIAL_FIRST_BLOCK_RATE = 0.45;

    private const RESIDENTIAL_SECOND_BLOCK_RATE = 0.97;

    private const COMMERCIAL_FIRST_BLOCK_RATE = 0.89;

    private const COMMERCIAL_SECOND_BLOCK_RATE = 1.13;

    public function handle(Request $request): array
    {
        $query = Building::query()
            ->with(['user', 'bills']); // Eager load the user relationship

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
        } else {
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
                'bills_summary' => [
                    'total_bills' => $building->bills->count(),
                    'total_usage' => $building->bills->sum('usability'),
                    'total_amount' => $building->bills->sum(function ($bill) use ($building) {
                        return $this->calculateBillAmount($bill->usability, $building->building_type);
                    }),
                    'latest_bill_month' => $building->bills->sortByDesc('month')->first()?->month,
                ],
            ]),
            'filters' => [
                'search' => $search,
                'sort_by' => $sortField,
                'sort_order' => $sortOrder,
            ],
        ];
    }

    private function calculateBillAmount(float $usability, string $buildingType): float
    {
        if ($buildingType === 'residential') {
            if ($usability <= self::RESIDENTIAL_FIRST_BLOCK_LIMIT) {
                return $usability * self::RESIDENTIAL_FIRST_BLOCK_RATE;
            }

            $firstBlockAmount = self::RESIDENTIAL_FIRST_BLOCK_LIMIT * self::RESIDENTIAL_FIRST_BLOCK_RATE;
            $remainingUsage = $usability - self::RESIDENTIAL_FIRST_BLOCK_LIMIT;
            $secondBlockAmount = $remainingUsage * self::RESIDENTIAL_SECOND_BLOCK_RATE;

            return $firstBlockAmount + $secondBlockAmount;
        }

        // Commercial calculation
        if ($usability <= self::COMMERCIAL_FIRST_BLOCK_LIMIT) {
            return $usability * self::COMMERCIAL_FIRST_BLOCK_RATE;
        }

        $firstBlockAmount = self::COMMERCIAL_FIRST_BLOCK_LIMIT * self::COMMERCIAL_FIRST_BLOCK_RATE;
        $remainingUsage = $usability - self::COMMERCIAL_FIRST_BLOCK_LIMIT;
        $secondBlockAmount = $remainingUsage * self::COMMERCIAL_SECOND_BLOCK_RATE;

        return $firstBlockAmount + $secondBlockAmount;
    }
}
