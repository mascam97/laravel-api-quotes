<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Users\Models\User;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->user = User::factory()->create();

    activity()
        ->causedBy($this->user)
        ->performedOn($this->user)
        ->log('deleted');

    (new QuoteFactory)->withUser($this->user)->create();
});

it('cannot authorize guest', function () {
    /** @var Activity $activity */
    $activity = Activity::query()->first();

    getJson(route('admin.activities.index'))
        ->assertUnauthorized();

    getJson(route('admin.activities.show', ['activity' => $activity->id]))
        ->assertUnauthorized();

    deleteJson(route('admin.activities.show', ['activity' => $activity->id]))
        ->assertUnauthorized();
});

it('requires permission', function () {
    login($this->user);

    /** @var Activity $activity */
    $activity = Activity::query()->first();

    getJson(route('admin.activities.index'))
        ->assertForbidden();

    getJson(route('admin.activities.show', ['activity' => $activity->id]))
        ->assertForbidden();

    deleteJson(route('admin.activities.show', ['activity' => $activity->id]))
        ->assertForbidden();
});

it('cannot show undefined data', function () {
    giveRoleWithPermission($this->user, 'view activities');
    login($this->user);

    getJson(route('admin.activities.show', ['activity' => 100000]))
        ->assertNotFound();

    deleteJson(route('admin.activities.show', ['activity' => 100000]))
        ->assertNotFound();
});
