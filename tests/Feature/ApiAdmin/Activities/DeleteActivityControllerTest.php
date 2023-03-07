<?php

use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\deleteJson;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->user = User::factory()->create();

    giveRoleWithPermission($this->user, 'delete activities');

    activity()
        ->causedBy($this->user)
        ->performedOn($this->user)
        ->log('deleted');

    $this->activity = Activity::query()->first();

    login($this->user);
});

it('can delete an activity', function () {
    deleteJson(route('admin.activities.show', ['activity' => $this->activity->id]))
        ->assertSuccessful();

    $this->activity->refresh();
})->throws(ModelNotFoundException::class);

test('sql queries optimization test', function () {
    DB::enableQueryLog();

    deleteJson(route('admin.activities.show', ['activity' => $this->activity->id]))->assertSuccessful();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(5)
        ->sequence(
            fn ($query) => $query->toBe('select * from `activity_log` where `id` = ? limit 1'),
            fn ($query) => $query->toBe('select * from `permissions`'),
            fn ($query) => $query->toContain('select `roles`.*, `role_has_permissions`.`permission_id` as `pivot_permission_id`, `role_has_permissions`.`role_id` as `pivot_role_id` from `roles` inner join `role_has_permissions` on `roles`.`id` = `role_has_permissions`.`role_id` where `role_has_permissions`.`permission_id` in'), // TODO: Validate `in (1)` `in (2)` at the final of this query
            fn ($query) => $query->toBe('select `permissions`.*, `model_has_permissions`.`model_id` as `pivot_model_id`, `model_has_permissions`.`permission_id` as `pivot_permission_id`, `model_has_permissions`.`model_type` as `pivot_model_type` from `permissions` inner join `model_has_permissions` on `permissions`.`id` = `model_has_permissions`.`permission_id` where `model_has_permissions`.`model_id` = ? and `model_has_permissions`.`model_type` = ?'),
            fn ($query) => $query->toBe('delete from `activity_log` where `id` = ?'),
        );

    DB::disableQueryLog();
});
