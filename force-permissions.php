<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

$role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

$permissions = [
    'view_acr::member', 'view_any_acr::member', 'create_acr::member', 'update_acr::member', 'restore_acr::member', 'restore_any_acr::member', 'replicate_acr::member', 'reorder_acr::member', 'delete_acr::member', 'delete_any_acr::member', 'force_delete_acr::member', 'force_delete_any_acr::member',
    'view_acr::point::transaction', 'view_any_acr::point::transaction', 'create_acr::point::transaction', 'update_acr::point::transaction', 'restore_acr::point::transaction', 'restore_any_acr::point::transaction', 'replicate_acr::point::transaction', 'reorder_acr::point::transaction', 'delete_acr::point::transaction', 'delete_any_acr::point::transaction', 'force_delete_acr::point::transaction', 'force_delete_any_acr::point::transaction',
    'view_acr::rewards::catalog', 'view_any_acr::rewards::catalog', 'create_acr::rewards::catalog', 'update_acr::rewards::catalog', 'restore_acr::rewards::catalog', 'restore_any_acr::rewards::catalog', 'replicate_acr::rewards::catalog', 'reorder_acr::rewards::catalog', 'delete_acr::rewards::catalog', 'delete_any_acr::rewards::catalog', 'force_delete_acr::rewards::catalog', 'force_delete_any_acr::rewards::catalog',
    'view_activity', 'view_any_activity', 'create_activity', 'update_activity', 'restore_activity', 'restore_any_activity', 'replicate_activity', 'reorder_activity', 'delete_activity', 'delete_any_activity', 'force_delete_activity', 'force_delete_any_activity',
    'view_article', 'view_any_article', 'create_article', 'update_article', 'restore_article', 'restore_any_article', 'replicate_article', 'reorder_article', 'delete_article', 'delete_any_article', 'force_delete_article', 'force_delete_any_article',
    'view_contact::message', 'view_any_contact::message', 'create_contact::message', 'update_contact::message', 'restore_contact::message', 'restore_any_contact::message', 'replicate_contact::message', 'reorder_contact::message', 'delete_contact::message', 'delete_any_contact::message', 'force_delete_contact::message', 'force_delete_any_contact::message',
    'view_customer', 'view_any_customer', 'create_customer', 'update_customer', 'restore_customer', 'restore_any_customer', 'replicate_customer', 'reorder_customer', 'delete_customer', 'delete_any_customer', 'force_delete_customer', 'force_delete_any_customer',
    'view_device', 'view_any_device', 'create_device', 'update_device', 'restore_device', 'restore_any_device', 'replicate_device', 'reorder_device', 'delete_device', 'delete_any_device', 'force_delete_device', 'force_delete_any_device',
    'view_internet::package', 'view_any_internet::package', 'create_internet::package', 'update_internet::package', 'restore_internet::package', 'restore_any_internet::package', 'replicate_internet::package', 'reorder_internet::package', 'delete_internet::package', 'delete_any_internet::package', 'force_delete_internet::package', 'force_delete_any_internet::package',
    'view_invoice', 'view_any_invoice', 'create_invoice', 'update_invoice', 'restore_invoice', 'restore_any_invoice', 'replicate_invoice', 'reorder_invoice', 'delete_invoice', 'delete_any_invoice', 'force_delete_invoice', 'force_delete_any_invoice',
    'view_marketing::fee', 'view_any_marketing::fee', 'create_marketing::fee', 'update_marketing::fee', 'restore_marketing::fee', 'restore_any_marketing::fee', 'replicate_marketing::fee', 'reorder_marketing::fee', 'delete_marketing::fee', 'delete_any_marketing::fee', 'force_delete_marketing::fee', 'force_delete_any_marketing::fee',
    'view_odp', 'view_any_odp', 'create_odp', 'update_odp', 'restore_odp', 'restore_any_odp', 'replicate_odp', 'reorder_odp', 'delete_odp', 'delete_any_odp', 'force_delete_odp', 'force_delete_any_odp',
    'view_registration', 'view_any_registration', 'create_registration', 'update_registration', 'restore_registration', 'restore_any_registration', 'replicate_registration', 'reorder_registration', 'delete_registration', 'delete_any_registration', 'force_delete_registration', 'force_delete_any_registration',
    'view_role', 'view_any_role', 'create_role', 'update_role', 'delete_role', 'delete_any_role',
    'view_testimonial', 'view_any_testimonial', 'create_testimonial', 'update_testimonial', 'restore_testimonial', 'restore_any_testimonial', 'replicate_testimonial', 'reorder_testimonial', 'delete_testimonial', 'delete_any_testimonial', 'force_delete_testimonial', 'force_delete_any_testimonial',
    'view_web::setting', 'view_any_web::setting', 'create_web::setting', 'update_web::setting', 'restore_web::setting', 'restore_any_web::setting', 'replicate_web::setting', 'reorder_web::setting', 'delete_web::setting', 'delete_any_web::setting', 'force_delete_web::setting', 'force_delete_any_web::setting',
    
    'view_ticket', 'view_any_ticket', 'create_ticket', 'update_ticket', 'restore_ticket', 'restore_any_ticket', 'replicate_ticket', 'reorder_ticket', 'delete_ticket', 'delete_any_ticket', 'force_delete_ticket', 'force_delete_any_ticket',
    'view_mikrotik::server', 'view_any_mikrotik::server', 'create_mikrotik::server', 'update_mikrotik::server', 'delete_mikrotik::server', 'delete_any_mikrotik::server', 'force_delete_mikrotik::server', 'force_delete_any_mikrotik::server',
    'view_mitra', 'view_any_mitra', 'create_mitra', 'update_mitra', 'delete_mitra', 'delete_any_mitra',
    'view_netwatch::monitoring', 'view_any_netwatch::monitoring', 'create_netwatch::monitoring', 'update_netwatch::monitoring', 'delete_netwatch::monitoring', 'delete_any_netwatch::monitoring',

    'page_CsrReport', 'page_NetwatchImport',
    
    'widget_StatsOverview', 'widget_IspStatsOverview', 'widget_PackageDistributionChart', 'widget_RegistrationChart', 'widget_OfflineCustomersTable', 'widget_PointTransactionChart', 'widget_LatestRegistrationsWidget', 'widget_LatestContactMessagesWidget'
];

$existing = Permission::pluck('name')->toArray();
$toInsert = [];
$now = now();
foreach ($permissions as $pName) {
    if (!in_array($pName, $existing)) {
        $toInsert[] = ['name' => $pName, 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now];
    }
}
if(count($toInsert) > 0) {
    DB::table('permissions')->insert($toInsert);
}
// Sync all permissions
app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
$role->syncPermissions(Permission::all());

echo "Successfully forced ".count($permissions)." permissions to DB and assigned to super_admin.\n";
