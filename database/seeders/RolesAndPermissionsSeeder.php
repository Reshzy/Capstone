<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Purchase Request permissions
        Permission::create(['name' => 'create purchase requests']);
        Permission::create(['name' => 'view purchase requests']);
        Permission::create(['name' => 'edit purchase requests']);
        Permission::create(['name' => 'delete purchase requests']);
        Permission::create(['name' => 'approve purchase requests']);
        Permission::create(['name' => 'reject purchase requests']);
        
        // Supplier permissions
        Permission::create(['name' => 'create suppliers']);
        Permission::create(['name' => 'view suppliers']);
        Permission::create(['name' => 'edit suppliers']);
        Permission::create(['name' => 'delete suppliers']);
        
        // Document permissions
        Permission::create(['name' => 'generate rfq']);
        Permission::create(['name' => 'generate aoq']);
        Permission::create(['name' => 'generate po']);
        Permission::create(['name' => 'generate dv']);
        
        // Create roles and assign permissions
        $requestorRole = Role::create(['name' => 'requestor']);
        $requestorRole->givePermissionTo([
            'create purchase requests',
            'view purchase requests',
            'edit purchase requests',
            'delete purchase requests',
            'view suppliers',
        ]);
        
        $approverRole = Role::create(['name' => 'approver']);
        $approverRole->givePermissionTo([
            'view purchase requests',
            'approve purchase requests',
            'reject purchase requests',
            'view suppliers',
        ]);
        
        $procurementOfficerRole = Role::create(['name' => 'procurement_officer']);
        $procurementOfficerRole->givePermissionTo([
            'view purchase requests',
            'edit purchase requests',
            'create suppliers',
            'view suppliers',
            'edit suppliers',
            'delete suppliers',
            'generate rfq',
            'generate aoq',
            'generate po',
            'generate dv',
        ]);
        
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
    }
}
