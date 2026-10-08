<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create or retrieve the super_admin role
        $role = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        // 2. Check if the Platform Super Admin exists
        $user = User::where('user_type', 'super_admin')
            ->whereNull('school_id')
            ->first();

        if (!$user) {
            $user = User::create([
                'id' => (string) Str::uuid(),
                'school_id' => null,
                'username' => 'SUPER-ADMIN',
                'email' => 'admin@platform.com',
                'phone_number' => '+966500000000',
                'password' => 'SuperAdmin@2026!',
                'avatar_url' => null,
                'user_type' => 'super_admin',
                'status' => 'active',
                'metadata' => [
                    'title' => 'Platform Super Admin',
                    'is_system_owner' => true,
                ],
            ]);
        }

        // 3. Assign role to the user
        if (!$user->hasRole('super_admin')) {
            $user->assignRole($role);
        }
    }
}
