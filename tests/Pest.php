<?php

use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use function Pest\Laravel\actingAs;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\CreatesApplication;

uses(TestCase::class, CreatesApplication::class, RefreshDatabase::class, WithFaker::class)->in('Feature', 'Unit');

function login(?User $user = null): void
{
    actingAs($user ?? User::factory()->create(), 'sanctum');
}

function giveRoleWithPermission(User $user, string $permissionName): void
{
    /** @var Role $role */
    $role = Role::create(['name' => 'admin']);
    $permission = Permission::create(['name' => $permissionName]);

    $role->givePermissionTo($permission);

    $user->assignRole($role);
}
