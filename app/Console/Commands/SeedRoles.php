<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class SeedRoles extends Command
{
    protected $signature = 'roles:seed';
    protected $description = 'Seed roles and basic permissions';

    public function handle()
    {
        // 1. Force Super Admin creation and assignment first
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        
        $user = User::first();
        if ($user) {
            $user->assignRole('super_admin');
            $this->info("Assigned super_admin to user: {$user->email}");
        }

        // 2. Generate permissions for resources
        $resources = [
            'AcrMember', 'AcrPointTransaction', 'AcrRewardsCatalog', 
            'Article', 'ContactMessage', 'InternetPackage', 
            'Registration', 'Testimonial', 'WebSetting', 'User', 'Role'
        ];
        
        $actions = ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
        
        foreach ($resources as $res) {
            foreach ($actions as $action) {
                $permName = $action . '_' . \Illuminate\Support\Str::snake($res);
                Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            }
        }

        // 3. Customer Service (CS)
        $csRole = Role::firstOrCreate(['name' => 'Customer Service', 'guard_name' => 'web']);
        $csPermissions = Permission::where('name', 'like', '%registration%')
            ->orWhere('name', 'like', '%contact_message%')
            ->orWhere('name', 'like', '%acr_member%')
            ->get();
        $csRole->syncPermissions($csPermissions);

        // 4. Editor / Tim Marketing
        $editorRole = Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'web']);
        $editorPermissions = Permission::where('name', 'like', '%article%')
            ->orWhere('name', 'like', '%testimonial%')
            ->orWhere('name', 'like', '%acr_rewards_catalog%')
            ->get();
        $editorRole->syncPermissions($editorPermissions);

        // 5. Teknisi
        $teknisiRole = Role::firstOrCreate(['name' => 'Teknisi', 'guard_name' => 'web']);
        $teknisiPermissions = Permission::where('name', 'like', '%acr_point_transaction%')
            ->get();
        $teknisiRole->syncPermissions($teknisiPermissions);

        $this->info('Roles and basic permissions seeded successfully!');
    }
}
