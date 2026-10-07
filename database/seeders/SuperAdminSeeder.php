<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $existingAdmin = DB::table('users')
            ->where('user_type', 'super_admin')
            ->whereNull('school_id')
            ->first();

        if (!$existingAdmin) {
            DB::table('users')->insert([
                'id' => (string) Str::uuid(),
                'school_id' => null,
                'username' => 'SUPER-ADMIN',
                'email' => 'admin@platform.com',
                'phone_number' => '+966500000000',
                'password' => Hash::make('SuperAdmin@2026!'),
                'avatar_url' => null,
                'user_type' => 'super_admin',
                'status' => 'active',
                'metadata' => json_encode([
                    'title' => 'Platform Super Admin',
                    'is_system_owner' => true,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
