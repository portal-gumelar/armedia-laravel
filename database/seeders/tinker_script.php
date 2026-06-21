use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

// Force generate permissions using Shield's underlying command if possible,
// or just manually generate the basic ones for all resources.
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
// Add widget/page permissions if necessary

// 1. Super Admin
$superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
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

echo "Roles and basic permissions seeded successfully!\n";
