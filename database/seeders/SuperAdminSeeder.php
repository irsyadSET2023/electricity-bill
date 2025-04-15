<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Create Super Admin Role if it doesn't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);

        // Create SuperAdmin user
        $user = User::firstOrCreate([
            'email' => 'superadmin@example.com',
        ], [
            'name' => 'super-admin',
            'password' => Hash::make('abcd1234'), // change to secure password
            'email_verified_at' => Carbon::now(),
        ]);

        // Assign role to user
        $user->assignRole($superAdminRole);
    }
}
