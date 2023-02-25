<?php

use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use function Pest\Laravel\deleteJson;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new UserFactory)->setAmount(4)->create();

    giveRoleWithPermission($this->user, 'delete users');

    login($this->user);
});

it('can delete an user', function () {
    $user = User::factory()->create();

    deleteJson(route('admin.users.show', ['user' => $user->id]))
        ->assertSuccessful();

    $user->refresh();
})->throws(ModelNotFoundException::class);
