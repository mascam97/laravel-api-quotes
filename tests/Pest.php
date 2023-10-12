<?php

use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\CreatesApplication;

uses(TestCase::class, CreatesApplication::class, LazilyRefreshDatabase::class, WithFaker::class)->in('Feature', 'Unit');

function loginApi(?User $user = null): void
{
    Passport::actingAs(user: $user ?? User::factory()->create());
}

function loginApiAdmin(?User $user = null): void
{
    Passport::actingAs(user: $user ?? User::factory()->create(), guard: 'api-admin');
}

function loginExternalApi(?User $user = null): void
{
    Passport::actingAs(user: $user ?? User::factory()->create(), guard: 'external-api');
}

function giveRoleWithPermission(User $user, string $permissionName): void
{
    /** @var Role $role */
    $role = Role::create(['name' => 'admin', 'guard_name' => 'api-admin']);
    $permission = Permission::create(['name' => $permissionName, 'guard_name' => 'api-admin']);

    $role->givePermissionTo($permission);

    $user->assignRole($role);
}

function formatQueries(array $queries): array
{
    return str_replace('"', '`', collect($queries)->pluck('query')->toArray());
}

function fixture(string $name): array
{
    $file = file_get_contents(
        filename: base_path("tests/Fixtures/$name.json"),
    );

    if (! $file) {
        throw new InvalidArgumentException(
            message: "Cannot find fixture: [$name] at tests/Fixtures/$name.json",
        );
    }

    return json_decode(
        json: $file,
        associative: true,
    );
}
