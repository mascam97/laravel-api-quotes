<?php

use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Pest\PendingCalls\UsesCall;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\CreatesApplication;

function commonTestSetup(): UsesCall
{
    return uses(TestCase::class, CreatesApplication::class, LazilyRefreshDatabase::class, WithFaker::class);
}

commonTestSetup()->group('api')->in('Feature/Api');
commonTestSetup()->group('apiAdmin')->in('Feature/ApiAdmin');
commonTestSetup()->group('apiAnalytics')->in('Feature/ApiAnalytics');
commonTestSetup()->group('console')->in('Feature/Console');
commonTestSetup()->group('externalApi')->in('Feature/ExternalApi');
commonTestSetup()->group('web')->in('Feature/Web');
commonTestSetup()->in('Feature/OAuth', 'Feature/Support', 'Unit');

function loginApi(?User $user = null): void
{
    Passport::actingAs(user: $user ?? User::factory()->create());
}

function loginApiAdmin(?User $user = null): void
{
    Passport::actingAs(user: $user ?? User::factory()->create(), guard: 'api-admin');
}

function loginApiAnalytics(?User $user = null): void
{
    Passport::actingAs(user: $user ?? User::factory()->create(), guard: 'api-analytics');
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

/**
 * @param array<string, string> $queries
 * @return array<string, mixed>
 */
function formatQueries(array $queries): array
{
    return str_replace('"', '`', collect($queries)->pluck('query')->toArray());
}

/** @return array<string, mixed> */
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
