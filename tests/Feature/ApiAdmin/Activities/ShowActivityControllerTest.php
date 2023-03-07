<?php

use Domain\Users\Models\User;
use function Pest\Laravel\getJson;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->user = User::factory()->create();

    giveRoleWithPermission($this->user, 'view activities');

    activity()
        ->causedBy($this->user)
        ->performedOn($this->user)
        ->log('deleted');

    $this->activity = Activity::query()->first();

    login($this->user);
});

it('can show', function () {
    getJson(route('admin.activities.show', ['activity' => $this->activity->id]))
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
