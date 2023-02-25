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

        /** @var Role $role */
        $role = Role::create(['name' => 'User administrator']);
        $role->givePermissionTo(['view any users', 'view users', 'delete users']);
    }
}
