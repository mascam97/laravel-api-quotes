<?php

use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new UserFactory)->setAmount(4)->create();

    giveRoleWithPermission($this->user, 'view users');

    loginApiAdmin($this->user);
});

it('can show', function () {
    getJson(route('admin.users.show', ['user' => $this->user->id]))
        ->assertOk()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'created_at'],
        ]);
});
