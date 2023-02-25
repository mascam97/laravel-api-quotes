<?php

use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use function Pest\Laravel\getJson;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertLessThan;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new UserFactory)->setAmount(4)->create();

    giveRoleWithPermission($this->user, 'view any users');

    login($this->user);
});

it('can index', function () {
    getJson(route('admin.users.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => ['*' => ['id', 'name', 'email', 'created_at']],
        ]);
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
