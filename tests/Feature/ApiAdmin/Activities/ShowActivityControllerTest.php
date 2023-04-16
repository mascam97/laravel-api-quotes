<?php

use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
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

test('sql queries optimization test', function () {
    DB::enableQueryLog();

    getJson(route('admin.activities.show', ['activity' => $this->activity->id]))->assertSuccessful();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(4)
        ->sequence(
            fn ($query) => $query->toBe('select * from `permissions`'),
            fn ($query) => $query->toContain('select `roles`.*, `role_has_permissions`.`permission_id` as `pivot_permission_id`, `role_has_permissions`.`role_id` as `pivot_role_id` from `roles` inner join `role_has_permissions` on `roles`.`id` = `role_has_permissions`.`role_id` where `role_has_permissions`.`permission_id` in'),
            fn ($query) => $query->toBe('select `permissions`.*, `model_has_permissions`.`model_id` as `pivot_model_id`, `model_has_permissions`.`permission_id` as `pivot_permission_id`, `model_has_permissions`.`model_type` as `pivot_model_type` from `permissions` inner join `model_has_permissions` on `permissions`.`id` = `model_has_permissions`.`permission_id` where `model_has_permissions`.`model_id` = ? and `model_has_permissions`.`model_type` = ?'),
            fn ($query) => $query->toBe('select `id`, `log_name`, `description`, `subject_type`, `subject_id`, `causer_type`, `causer_id`, `event`, `created_at`, `updated_at` from `activity_log` where `id` = ? limit 1'),
        );

    DB::disableQueryLog();
});
