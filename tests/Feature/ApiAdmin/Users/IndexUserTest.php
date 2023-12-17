<?php

use Domain\Pockets\Models\Pocket;
use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\getJson;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertLessThan;

beforeEach(function () {
    $this->pocket = Pocket::factory()->create();
    $this->user = User::factory()->create(['pocket_id' => $this->pocket->getKey()]);

    (new UserFactory)->setAmount(4)->create(['pocket_id' => $this->pocket->getKey()]);

    giveRoleWithPermission($this->user, 'view any users');

    loginApiAdmin($this->user);
});

it('can index', function () {
    getJson(route('admin.users.index'))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', 5, fn ($json) => $json
                ->has('id')
                ->has('name')
                ->has('email')
                ->has('created_at')
                ->has('updated_at')
                ->has('deleted_at')
            )->etc();
        });
});

it('can filter by id', function () {
    /** @var User $newUser */
    $newUser = User::factory()->create();

    $responseData = getJson(route('admin.users.index', ['filter[id]' => $newUser->id]))
        ->assertOk()
        ->json('data');

    assertCount(1, $responseData);
    assertEquals($newUser->getKey(), $responseData[0]['id']);
});

it('can filter by name', function () {
    $newUser = User::factory()->create([
        'name' => 'Shakespeare',
    ]);

    $responseData = getJson(route('admin.users.index', ['filter[name]' => 'shakespeare']))
        ->assertOk()
        ->json('data');

    assertCount(1, $responseData);
    assertEquals($newUser->getKey(), $responseData[0]['id']);
});

it('can filter by trashed', function () {
    User::factory(6)->deleted()->create();

    getJson(route('admin.users.index', ['filter[trashed]' => 'only']))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->count('data', 6)->etc();
        });

    getJson(route('admin.users.index', ['filter[trashed]' => 'with']))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->count('data', 11)->etc();
        });
});

it('can sort by id', function () {
    $responseData = getJson(route('admin.users.index', ['sort' => 'id']))
        ->assertOk()
        ->json('data');

    assertLessThan($responseData[4]['id'], $responseData[0]['id']);

    $responseDataTwo = getJson(route('admin.users.index', ['sort' => '-id']))
        ->assertOk()
        ->json('data');

    assertLessThan($responseDataTwo[0]['id'], $responseDataTwo[4]['id']);
});

it('can sort by name', function () {
    $this->user->name = 'AAA';
    $this->user->update();

    $responseData = getJson(route('admin.users.index', ['sort' => 'name']))
        ->assertOk()
        ->json('data');

    assertEquals('AAA', $responseData[0]['name']);

    $responseDataTwo = getJson(route('admin.users.index', ['sort' => '-name']))
        ->assertOk()
        ->json('data');

    assertEquals('AAA', $responseDataTwo[4]['name']);
});

it('can include permissions', function () {
    $this->user->givePermissionTo('view any users');

    $responseData = getJson(route('admin.users.index', ['include' => 'permissions']))
        ->assertOk()
        ->json('data');

    assertCount(5, $responseData);
    assertArrayHasKey('permissions', $responseData[0]);
    assertEquals('view any users', $responseData[0]['permissions'][0]['name']);
});

it('can include roles', function () {
    $responseData = getJson(route('admin.users.index', ['include' => 'roles']))
        ->assertOk()
        ->json('data');

    assertCount(5, $responseData);
    assertArrayHasKey('roles', $responseData[0]);
    assertEquals('admin', $responseData[0]['roles'][0]['name']);
});

it('can include pocket', function () {
    $responseData = getJson(route('admin.users.index', ['include' => 'pocket']))
        ->assertOk()
        ->json('data');

    assertArrayHasKey('pocket', $responseData[0]);
    assertEquals($this->pocket->getKey(), $responseData[0]['pocket']['id']);
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    getJson(route('admin.users.index'))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(5)
        ->sequence(
            fn ($query) => $query->toBe('select * from `permissions`'),
            fn ($query) => $query->toContain('select `roles`.*, `role_has_permissions`.`permission_id` as `pivot_permission_id`, `role_has_permissions`.`role_id` as `pivot_role_id` from `roles` inner join `role_has_permissions` on `roles`.`id` = `role_has_permissions`.`role_id` where `role_has_permissions`.`permission_id` in'),
            fn ($query) => $query->toBe('select `permissions`.*, `model_has_permissions`.`model_id` as `pivot_model_id`, `model_has_permissions`.`permission_id` as `pivot_permission_id`, `model_has_permissions`.`model_type` as `pivot_model_type` from `permissions` inner join `model_has_permissions` on `permissions`.`id` = `model_has_permissions`.`permission_id` where `model_has_permissions`.`model_id` = ? and `model_has_permissions`.`model_type` = ?'),
            fn ($query) => $query->toBe('select count(*) as aggregate from `users` where `users`.`deleted_at` is null'),
            fn ($query) => $query->toBe('select `id`, `name`, `email`, `deleted_at`, `created_at`, `updated_at` from `users` where `users`.`deleted_at` is null order by `created_at` asc limit 20 offset 0'),
        );

    DB::disableQueryLog();
});
