<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        $admin = [
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Check if admin already exists
        $existingAdmin = DB::table('admin_users')->where('email', $admin['email'])->first();
        
        // Only create the admin if it doesn't already exist
        if (!$existingAdmin) {
            // Insert admin user and get the ID
            $adminId = DB::table('admin_users')->insertGetId($admin);
        } else {
            $adminId = $existingAdmin->id;
        }

        // Handle role assignment for admin_roles table (new schema)
        if (Schema::hasTable('admin_roles')) {
            // Get the super_admin role ID
            $roleId = DB::table('admin_roles')->where('name', 'super_admin')->value('id');

            // Assign role to admin if not already assigned
            if ($roleId) {
                $existingRole = DB::table('admin_role_user')
                    ->where('admin_user_id', $adminId)
                    ->where('admin_role_id', $roleId)
                    ->first();
                    
                if (!$existingRole) {
                    DB::table('admin_role_user')->insert([
                        'admin_user_id' => $adminId,
                        'admin_role_id' => $roleId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
} 