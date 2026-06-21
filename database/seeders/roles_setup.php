<?php

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

// Ensure permissions exist before assignment
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

// 1. Super Admin
$superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
// Super admin gets everything, so no need to explicitly sync permissions if using Gate::before, but Shield does it automatically via trait or policy. We'll give it all permissions just in case.
$superAdminRole->syncPermissions(Permission::all());

// 2. Customer Service (CS)
$csRole = Role::firstOrCreate(['name' => 'Customer Service', 'guard_name' => 'web']);
$csPermissions = Permission::where('name', 'like', '%registration%')
    ->orWhere('name', 'like', '%contact_message%')
    ->orWhere('name', 'like', '%acr_member%')
    ->get();
$csRole->syncPermissions($csPermissions);

// 3. Editor / Tim Marketing
$editorRole = Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'web']);
$editorPermissions = Permission::where('name', 'like', '%article%')
    ->orWhere('name', 'like', '%testimonial%')
    ->orWhere('name', 'like', '%acr_rewards_catalog%')
    ->get();
$editorRole->syncPermissions($editorPermissions);

// 4. Teknisi
$teknisiRole = Role::firstOrCreate(['name' => 'Teknisi', 'guard_name' => 'web']);
$teknisiPermissions = Permission::where('name', 'like', '%acr_point_transaction%')
    ->get();
$teknisiRole->syncPermissions($teknisiPermissions);

// Assign super admin to the first user (current admin)
$user = User::first();
if ($user) {
    $user->assignRole('super_admin');
}

echo "Roles seeded successfully!\n";
