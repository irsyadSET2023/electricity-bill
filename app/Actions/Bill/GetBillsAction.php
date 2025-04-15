<?php

namespace App\Actions\Bill;

use App\Models\Bill;
use Illuminate\Http\Request;

final class GetBillsAction
{
    private const RESIDENTIAL_FIRST_BLOCK_LIMIT = 200;
    private const COMMERCIAL_FIRST_BLOCK_LIMIT = 200;
    
    private const RESIDENTIAL_FIRST_BLOCK_RATE = 0.45;
    private const RESIDENTIAL_SECOND_BLOCK_RATE = 0.97;
    private const COMMERCIAL_FIRST_BLOCK_RATE = 0.89;
    private const COMMERCIAL_SECOND_BLOCK_RATE = 1.13;

    public function handle(Request $request): array
    {
        $query = Bill::query()
            ->with(['building.user']); // Eager load building and its user relationship

        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('bills.month', 'like', "%{$search}%")
                    ->orWhereHas('building', function ($buildingQuery) use ($search) {
                        $buildingQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('building.user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Apply sorting
        $sortField = $request->input('sort_by', 'month');
        $sortOrder = $request->input('sort_order', 'asc');
        
        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['month', 'usability', 'created_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->orderBy('month', 'desc');
        }

        // Get paginated results
        $bills = $query->paginate(10)->withQueryString();
  
        return [
            'bills' => $bills->through(fn (Bill $bill) => [
                'id' => $bill->id,
                'month' => $bill->month,
                'usability' => $bill->usability,
                'building' => [
                    'id' => $bill->building->id,
                    'name' => $bill->building->name,
                    'building_type' => $bill->building->building_type,
                ],
                'owner' => [
                    'id' => $bill->building->user->id,
                    'name' => $bill->building->user->name,
                    'email' => $bill->building->user->email,
                ],
                'created_at' => $bill->created_at,
                'total_amount' => $this->calculateBillAmount($bill->usability, $bill->building->building_type),
            ]),
            'filters' => [
                'search' => $search,
                'sort_by' => $sortField,
                'sort_order' => $sortOrder
            ]
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
