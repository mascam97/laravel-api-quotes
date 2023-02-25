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

    // TODO: Create Factory for Activity
    activity()
        ->causedBy($this->user)
        ->performedOn($this->user)
        ->log('deleted');

    giveRoleWithPermission($this->user, 'view any activities');

    login($this->user);
});

it('can index', function () {
    getJson(route('admin.activities.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => ['*' => [
                'id',
                'log_name',
                'description',
                'subject_type',
                'subject_id',
                'causer_type',
                'causer_id',
                'event',
                'created_at',
                'updated_at',
            ]],
        ]);
});

it('can include subject', function () {
    $responseData = getJson(route('admin.activities.index', ['include' => 'subject']))
        ->assertOk()
        ->json('data');

    assertCount(1, $responseData);
    assertArrayHasKey('subject', $responseData[0]);
    assertEquals($this->user->id, $responseData[0]['subject']['id']);
});

it('can include causer', function () {
    $responseData = getJson(route('admin.activities.index', ['include' => 'causer']))
        ->assertOk()
        ->json('data');

    assertCount(1, $responseData);
    assertArrayHasKey('causer', $responseData[0]);
    assertEquals($this->user->id, $responseData[0]['causer']['id']);
});
