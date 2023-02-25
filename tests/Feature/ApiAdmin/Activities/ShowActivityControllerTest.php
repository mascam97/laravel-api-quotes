<?php

use Domain\Users\Models\User;
use function Pest\Laravel\getJson;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->user = User::factory()->create();

    giveRoleWithPermission($this->user, 'view activities');

    login($this->user);
});

it('can show', function () {
    activity()
        ->causedBy($this->user)
        ->performedOn($this->user)
        ->log('deleted');

    /** @var Activity $activity */
    $activity = Activity::query()->first();

    getJson(route('admin.activities.show', ['activity' => $activity->id]))
        ->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
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
            ],
        ])->assertOk();
});
