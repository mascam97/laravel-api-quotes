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
        Permission::create(['name' => 'view any users']);
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'delete users']);

        Permission::create(['name' => 'view any activities']);
        Permission::create(['name' => 'view activities']);
        Permission::create(['name' => 'delete activities']);

        /** @var Role $role */
        $role = Role::create(['name' => 'Administrator']);
        $role->givePermissionTo(['view any users', 'view users', 'delete users']);
        $role->givePermissionTo(['view any activities', 'view activities', 'delete activities']);
    }
}
