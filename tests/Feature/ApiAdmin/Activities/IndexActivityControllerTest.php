<?php

use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\getJson;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

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

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    getJson(route('admin.activities.index'))->assertOk();
    // TODO: Validate cache in roles and permissions
    getJson(route('admin.activities.index'))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(7)
        ->sequence(
            fn ($query) => $query->toBe('select * from `permissions`'),
            fn ($query) => $query->toContain('select `roles`.*, `role_has_permissions`.`permission_id` as `pivot_permission_id`, `role_has_permissions`.`role_id` as `pivot_role_id` from `roles` inner join `role_has_permissions` on `roles`.`id` = `role_has_permissions`.`role_id` where `role_has_permissions`.`permission_id`'),
            fn ($query) => $query->toBe('select `permissions`.*, `model_has_permissions`.`model_id` as `pivot_model_id`, `model_has_permissions`.`permission_id` as `pivot_permission_id`, `model_has_permissions`.`model_type` as `pivot_model_type` from `permissions` inner join `model_has_permissions` on `permissions`.`id` = `model_has_permissions`.`permission_id` where `model_has_permissions`.`model_id` = ? and `model_has_permissions`.`model_type` = ?'),
            fn ($query) => $query->toBe('select count(*) as aggregate from `activity_log`'),
            fn ($query) => $query->toBe('select * from `activity_log` limit 15 offset 0'),
            fn ($query) => $query->toBe('select count(*) as aggregate from `activity_log`'),
            fn ($query) => $query->toBe('select * from `activity_log` limit 15 offset 0'),
        );

    DB::disableQueryLog();
});
