<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->user = User::factory()->create();

    activity()
        ->causedBy($this->user)
        ->performedOn($this->user)
        ->log('deleted');

    $this->activity = Activity::query()->first();

    (new QuoteFactory)->withUser($this->user)->create();
});

it('cannot authorize guest', function () {
    getJson(route('admin.activities.index'))
        ->assertUnauthorized();

    getJson(route('admin.activities.show', ['activity' => $this->activity->id]))
        ->assertUnauthorized();

    deleteJson(route('admin.activities.show', ['activity' => $this->activity->id]))
        ->assertUnauthorized();

    postJson(route('admin.activities.export'))
        ->assertUnauthorized();
});

it('requires permission', function () {
    loginApiAdmin($this->user);

    getJson(route('admin.activities.index'))
        ->assertForbidden();

    getJson(route('admin.activities.show', ['activity' => $this->activity->id]))
        ->assertForbidden();

    deleteJson(route('admin.activities.show', ['activity' => $this->activity->id]))
        ->assertForbidden();

    postJson(route('admin.activities.export'))
        ->assertForbidden();
});

it('cannot show undefined data', function () {
    giveRoleWithPermission($this->user, 'view activities');
    loginApiAdmin($this->user);

    getJson(route('admin.activities.show', ['activity' => 100000]))
        ->assertNotFound();

    deleteJson(route('admin.activities.show', ['activity' => 100000]))
        ->assertNotFound();
});
