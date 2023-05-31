<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsAndRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws \Exception
     */
    public function run(): void
    {
        Permission::create(['name' => 'view any users', 'guard_name' => 'api-admin']);
        Permission::create(['name' => 'view users', 'guard_name' => 'api-admin']);
        Permission::create(['name' => 'delete users', 'guard_name' => 'api-admin']);

        Permission::create(['name' => 'view any activities', 'guard_name' => 'api-admin']);
        Permission::create(['name' => 'view activities', 'guard_name' => 'api-admin']);
        Permission::create(['name' => 'delete activities', 'guard_name' => 'api-admin']);
        Permission::create(['name' => 'export activities', 'guard_name' => 'api-admin']);

        /** @var Role $role */
        $role = Role::create(['name' => 'Administrator', 'guard_name' => 'api-admin']);
        $role->givePermissionTo(['view any users', 'view users', 'delete users']);
        $role->givePermissionTo(['view any activities', 'view activities', 'delete activities']);
    }
}
