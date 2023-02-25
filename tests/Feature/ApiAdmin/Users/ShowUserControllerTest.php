<?php

use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use function Pest\Laravel\getJson;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertLessThan;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new UserFactory)->setAmount(4)->create();

    giveRoleWithPermission($this->user, 'view users');

    login($this->user);
});

it('can show', function () {
    getJson(route('admin.users.show', ['user' => $this->user->id]))
        ->assertSuccessful()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'created_at'],
        ])->assertOk();
});
