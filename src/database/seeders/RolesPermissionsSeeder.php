<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // Products
            'view products',
            'create products',
            'edit products',
            'delete products',
            // Sales
            'view sales',
            'create sales',
            'cancel sales',
            // Customers
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',
            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',
            // Dashboard
            'view dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin - full access
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // Editor - can manage products and view sales/customers
        $editor = Role::firstOrCreate(['name' => 'editor']);
        $editor->syncPermissions([
            'view dashboard',
            'view products', 'create products', 'edit products',
            'view sales', 'create sales',
            'view customers', 'create customers', 'edit customers',
        ]);

        // User - read-only + create sales
        $user = Role::firstOrCreate(['name' => 'user']);
        $user->syncPermissions([
            'view dashboard',
            'view products',
            'view sales', 'create sales',
            'view customers',
        ]);
    }
}
