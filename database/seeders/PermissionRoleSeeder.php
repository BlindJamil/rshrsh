<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\Role;

class PermissionRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Define permissions
        $permissions = [
            // Super Admin
            ['name' => 'manage_admins', 'display_name' => 'Manage Administrators', 'group' => 'admin'],
            ['name' => 'manage_roles', 'display_name' => 'Manage Roles', 'group' => 'admin'],
            
            // Donations
            ['name' => 'view_donations', 'display_name' => 'View Donations', 'group' => 'donations'],
            ['name' => 'manage_donations', 'display_name' => 'Manage Donations', 'group' => 'donations'],
            
            // Causes
            ['name' => 'view_causes', 'display_name' => 'View Causes', 'group' => 'causes'],
            ['name' => 'manage_causes', 'display_name' => 'Manage Causes', 'group' => 'causes'],
            ['name' => 'approve_causes', 'display_name' => 'Approve Causes', 'group' => 'causes'],
            
            // Volunteers
            ['name' => 'view_volunteers', 'display_name' => 'View Volunteers', 'group' => 'volunteers'],
            ['name' => 'manage_volunteers', 'display_name' => 'Manage Volunteers', 'group' => 'volunteers'],
            
            // Departments
            ['name' => 'view_departments', 'display_name' => 'View Departments', 'group' => 'departments'],
            ['name' => 'manage_departments', 'display_name' => 'Manage Departments', 'group' => 'departments'],
            
            // Contact Messages
            ['name' => 'view_messages', 'display_name' => 'View Messages', 'group' => 'messages'],
            ['name' => 'manage_messages', 'display_name' => 'Manage Messages', 'group' => 'messages'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }

        // Define roles with their permissions
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Full access to all features',
                'permissions' => ['manage_admins', 'manage_roles', 'view_donations', 'manage_donations', 
                                'view_causes', 'manage_causes', 'approve_causes', 'view_volunteers', 
                                'manage_volunteers', 'view_departments', 'manage_departments', 
                                'view_messages', 'manage_messages']
            ],
            [
                'name' => 'accounting_admin',
                'display_name' => 'Accounting Administrator',
                'description' => 'Manages cash donations',
                'permissions' => ['view_donations', 'manage_donations']
            ],
            [
                'name' => 'content_manager',
                'display_name' => 'Content Manager',
                'description' => 'Manages causes and volunteers',
                'permissions' => ['view_causes', 'manage_causes', 'view_volunteers', 'manage_volunteers']
            ],
            [
                'name' => 'message_manager',
                'display_name' => 'Message Manager',
                'description' => 'Manages contact messages',
                'permissions' => ['view_messages', 'manage_messages']
            ],
            [
                'name' => 'viewer_donations',
                'display_name' => 'Donations Viewer',
                'description' => 'Can view donation information',
                'permissions' => ['view_donations']
            ],
            [
                'name' => 'viewer_causes',
                'display_name' => 'Causes Viewer',
                'description' => 'Can view causes information',
                'permissions' => ['view_causes']
            ],
            [
                'name' => 'viewer_volunteers',
                'display_name' => 'Volunteers Viewer',
                'description' => 'Can view volunteer information',
                'permissions' => ['view_volunteers']
            ],
            [
                'name' => 'viewer_departments',
                'display_name' => 'Departments Viewer',
                'description' => 'Can view department information',
                'permissions' => ['view_departments']
            ],
        ];

        foreach ($roles as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);
            
            $role = Role::firstOrCreate([
                'name' => $roleData['name']
            ], $roleData);

            $permissionModels = Permission::whereIn('name', $permissions)->get();
            $role->permissions()->sync($permissionModels);
        }
    }
} 