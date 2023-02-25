<?php

use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use function Pest\Laravel\deleteJson;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->user = User::factory()->create();

    giveRoleWithPermission($this->user, 'delete activities');

    login($this->user);
});

it('can delete an activity', function () {
    activity()
        ->causedBy($this->user)
        ->performedOn($this->user)
        ->log('deleted');

    /** @var Activity $activity */
    $activity = Activity::query()->first();

    deleteJson(route('admin.activities.show', ['activity' => $activity->id]))
        ->assertSuccessful();

    $activity->refresh();
})->throws(ModelNotFoundException::class);
