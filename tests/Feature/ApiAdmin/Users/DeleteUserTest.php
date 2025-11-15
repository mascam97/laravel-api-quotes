<?php

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Rating\Models\Rating;
use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new UserFactory)->setAmount(4)->create();

    giveRoleWithPermission($this->user, 'delete users');

    loginApiAdmin($this->user);
});

it('can delete an user', function () {
    /** @var User $user */
    $user = User::factory()->create();
    /** @var Quote $quote */
    $quote = (new QuoteFactory)->withUser($user)->create();
    $rating = new Rating();
    $rating->qualifier()->associate($user);
    $rating->rateable()->associate($quote);
    $rating->score = 5;
    $rating->save();

    deleteJson(route('admin.users.show', ['user' => $user->id]))
        ->assertSuccessful();

    assertSoftDeleted($user);
    assertDatabaseMissing('quotes', ['id' => $quote->id]);
    // assertDatabaseMissing('ratings', ['id' => $rating->id]); TODO: fix this

    /** @var Activity $activity */
    $activity = Activity::query()->first();

    assertTrue($activity->causer()->is($this->user));
    assertTrue($activity->subject()->is($user));
    assertEquals('deleted', $activity->description);
    assertEquals('default', $activity->log_name);
});

// TODO: Add test for roll back if something fails

test('sql queries optimization test', function () {
    /** @var User $user */
    $user = User::factory()->create();

    DB::enableQueryLog();

    deleteJson(route('admin.users.show', ['user' => $user->id]))
        ->assertSuccessful();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(9)
        ->sequence(
            fn ($query) => $query->toBe('select * from `users` where `id` = ? and `users`.`deleted_at` is null limit 1'),
            fn ($query) => $query->toBe('select * from `permissions`'),
            fn ($query) => $query->toContain('select `roles`.*, `role_has_permissions`.`permission_id` as `pivot_permission_id`, `role_has_permissions`.`role_id` as `pivot_role_id` from `roles` inner join `role_has_permissions` on `roles`.`id` = `role_has_permissions`.`role_id` where `role_has_permissions`.`permission_id`'),
            fn ($query) => $query->toContain('select `permissions`.*, `model_has_permissions`.`model_id` as `pivot_model_id`, `model_has_permissions`.`permission_id` as `pivot_permission_id`, `model_has_permissions`.`model_type` as `pivot_model_type` from `permissions` inner join `model_has_permissions` on `permissions`.`id` = `model_has_permissions`.`permission_id` where `model_has_permissions`.`model_id` in'),
            fn ($query) => $query->toContain('select `roles`.*, `model_has_roles`.`model_id` as `pivot_model_id`, `model_has_roles`.`role_id` as `pivot_role_id`, `model_has_roles`.`model_type` as `pivot_model_type` from `roles` inner join `model_has_roles` on `roles`.`id` = `model_has_roles`.`role_id` where `model_has_roles`.`model_id` in'),
            fn ($query) => $query->toBe('delete from `quotes` where `quotes`.`user_id` = ? and `quotes`.`user_id` is not null'),
            fn ($query) => $query->toBe('delete `quotes` from `quotes` inner join `ratings` on `quotes`.`id` = `ratings`.`rateable_id` where `ratings`.`qualifier_id` = ? and `ratings`.`qualifier_type` = ? and `ratings`.`rateable_type` = ? and `ratings`.`qualifier_type` = ?'),
            fn ($query) => $query->toBe('update `users` set `deleted_at` = ?, `users`.`updated_at` = ? where `id` = ?'),
            fn ($query) => $query->toBe('insert into `activity_log` (`log_name`, `properties`, `causer_id`, `causer_type`, `batch_uuid`, `subject_id`, `subject_type`, `description`, `updated_at`, `created_at`) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'),
        );

    DB::disableQueryLog();
});
