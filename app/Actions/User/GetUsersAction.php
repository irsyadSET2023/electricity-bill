<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;

final class GetUsersAction
{
    public function handle(Request $request): array
    {
        $query = User::query()->whereHas('roles', function (Builder $query) {
            $query->where('name', '!=', 'super-admin');
        });

        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortField = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');

        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['name', 'email', 'created_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortOrder);
        }

        // Get paginated results
        $users = $query->paginate(10)->withQueryString();

        return [
            'users' => $users->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
            ]),
            'filters' => [
                'search' => $search,
                'sort_by' => $sortField,
                'sort_order' => $sortOrder,
            ],
        ];
    }
}
