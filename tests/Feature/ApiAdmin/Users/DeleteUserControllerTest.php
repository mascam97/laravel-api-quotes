<?php

use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
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
    $user = User::factory()->create();

    deleteJson(route('admin.users.show', ['user' => $user->id]))
        ->assertSuccessful();

    /** @var Activity $activity */
    $activity = Activity::query()->first();

    assertTrue($activity->causer()->is($this->user));
    assertTrue($activity->subject()->is($user));
    assertEquals('deleted', $activity->description);
    assertEquals('default', $activity->log_name);

    assertSoftDeleted($user);
});
