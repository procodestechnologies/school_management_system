<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions for each module
        $this->createPermissions();

        // Create roles and assign permissions
        $this->createRolesAndAssignPermissions();

        $this->command->info('Permissions and roles created successfully!');
    }

    /**
     * Create all permissions
     */
    private function createPermissions(): void
    {
        // Define modules and their permissions
        $modules = [
            'parent' => ['view', 'create', 'edit', 'update', 'delete'],
            'student' => ['view', 'create', 'edit', 'update', 'delete'],
            'teacher' => ['view', 'create', 'edit', 'update', 'delete'],
            'institution' => ['view', 'create', 'edit', 'update', 'delete'],
            'curriculum' => ['view', 'create', 'edit', 'update', 'delete'],
            'attendance' => ['view', 'create', 'edit', 'update', 'delete'],
            'examination' => ['view', 'create', 'edit', 'update', 'delete'],
            'feemanagement' => ['view', 'create', 'edit', 'update', 'delete'],
            'timetable' => ['view', 'create', 'edit', 'update', 'delete'],
            'user' => ['view', 'create', 'edit', 'update', 'delete'],
            'role' => ['view', 'create', 'edit', 'update', 'delete'],
            'permission' => ['view', 'create', 'edit', 'update', 'delete'],
            'setting' => ['view', 'create', 'edit', 'update', 'delete'],
            'dashboard' => ['view'],
            'report' => ['view', 'create', 'export'],
            'account' => ['view', 'create', 'edit', 'update', 'delete'],
            'finance' => ['view', 'create', 'edit', 'update', 'delete'],
        ];

        // Create permissions for each module
        foreach ($modules as $module => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission . ' ' . $module,
                    'guard_name' => 'web'
                ]);
            }
        }
    }

    /**
     * Create roles and assign permissions
     */
    private function createRolesAndAssignPermissions(): void
    {
        // 1. Admin Role (Full access - all permissions)
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // 2. Director Role (Management access - view and edit most things)
        $director = Role::firstOrCreate(['name' => 'Director', 'guard_name' => 'web']);
        $directorPermissions = Permission::whereIn('name', [
            // View permissions
            'view parent',
            'view student',
            'view teacher',
            'view institution',
            'view curriculum',
            'view attendance',
            'view examination',
            'view feemanagement',
            'view timetable',
            'view dashboard',
            'view report',
            'view account',
            'view finance',

            // Edit/Update permissions
            'edit parent',
            'update parent',
            'edit student',
            'update student',
            'edit teacher',
            'update teacher',
            'edit curriculum',
            'update curriculum',
            'edit attendance',
            'update attendance',
            'edit examination',
            'update examination',
            'edit feemanagement',
            'update feemanagement',
            'edit timetable',
            'update timetable',

            // Create permissions (limited)
            'create parent',
            'create student',
            'create teacher',
            'create curriculum',
            'create attendance',
            'create examination',
            'create timetable',

            // Report permissions
            'create report',
            'export report',
        ])->get();
        $director->syncPermissions($directorPermissions);

        // 3. Accountant Role (Finance and fee management)
        $accountant = Role::firstOrCreate(['name' => 'Accountant', 'guard_name' => 'web']);
        $accountantPermissions = Permission::whereIn('name', [
            'view feemanagement',
            'create feemanagement',
            'edit feemanagement',
            'update feemanagement',
            'delete feemanagement',
            'view student',
            'view parent',
            'view account',
            'create account',
            'edit account',
            'update account',
            'view finance',
            'create finance',
            'edit finance',
            'update finance',
            'view report',
            'create report',
            'export report',
            'view dashboard',
        ])->get();
        $accountant->syncPermissions($accountantPermissions);

        // 4. Parent Role (Limited access - view their children's information)
        $parent = Role::firstOrCreate(['name' => 'Parent', 'guard_name' => 'web']);
        $parentPermissions = Permission::whereIn('name', [
            'view student',
            'view attendance',
            'view examination',
            'view timetable',
            'view feemanagement',
            'view dashboard',
            'view report',
        ])->get();
        $parent->syncPermissions($parentPermissions);

        // 5. Student Role (Very limited access)
        $student = Role::firstOrCreate(['name' => 'Student', 'guard_name' => 'web']);
        $studentPermissions = Permission::whereIn('name', [
            'view attendance',
            'view examination',
            'view timetable',
            'view dashboard',
        ])->get();
        $student->syncPermissions($studentPermissions);
    }
}
