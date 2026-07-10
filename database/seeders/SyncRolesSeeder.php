<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SyncRolesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure core 'user' role exists
        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            ['description' => 'Standard user account with general application access and chat capabilities.']
        );

        // 2. Ensure core 'User' role exists or points to the same logic
        Role::firstOrCreate(
            ['name' => 'User'],
            ['description' => 'Standard end-user with chat and profile privileges.']
        );

        // 3. Ensure 'Super Admin' exists
        Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['description' => 'Unrestricted access to all modules and configurations.']
        );

        // 4. Ensure 'Admin' exists
        Role::firstOrCreate(
            ['name' => 'Admin'],
            ['description' => 'Manage system settings, AI providers, and prompts.']
        );

        // 5. Sync all existing users' primary role into the role_user table
        foreach (User::all() as $u) {
            $roleRecord = Role::where(DB::raw('LOWER(name)'), strtolower($u->role ?: 'user'))->first();
            if (!$roleRecord) {
                $roleRecord = $userRole;
            }
            if ($roleRecord) {
                $u->roles()->syncWithoutDetaching([$roleRecord->id]);
            }
        }

        $this->command->info("Role sync completed. Total users synced: " . User::count() . " | Total pivot records: " . DB::table('role_user')->count());
    }
}
