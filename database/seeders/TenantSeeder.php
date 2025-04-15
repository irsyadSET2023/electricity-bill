<?php

namespace Database\Seeders;

use App\Enums\BuildingType;
use App\Enums\Month;
use App\Models\Bill;
use App\Models\Building;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 'tenant' role if it doesn't exist
        $tenantRole = Role::firstOrCreate(['name' => 'tenant', 'guard_name' => 'web']);

        // Create 10 tenants
        for ($i = 1; $i <= 10; $i++) {
            $user = User::firstOrCreate([
                'email' => "tenant{$i}@example.com",
            ], [
                'name' => "Tenant {$i}",
                'password' => Hash::make('password123'), // Use a secure password in production
                'email_verified_at' => Carbon::now(),
            ]);

            // Assign 'tenant' role to user
            $user->assignRole($tenantRole);

            // Determine number of buildings for this tenant (some with multiple, some with single)
            $buildingCount = $i % 3 === 0 ? 2 : 1;

            for ($j = 1; $j <= $buildingCount; $j++) {
                /** @var Building $building */
                $building = Building::create([
                    'name' => "Building {$j} of Tenant {$i}",
                    'state' => 'Selangor',
                    'building_type' => BuildingType::cases()[array_rand(BuildingType::cases())]->value,
                    'user_id' => $user->id,
                ]);

                // Create a bill for each building

                Bill::create([
                    'month' => Month::cases()[array_rand(Month::cases())]->value,
                    'usability' => rand(100, 500), // Random usage value
                    'building_id' => $building->id,
                ]);
            }
        }
    }
}
