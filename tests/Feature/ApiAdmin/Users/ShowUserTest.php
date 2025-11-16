<?php

use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new UserFactory)->setAmount(4)->create();

    giveRoleWithPermission($this->user, 'view users');

    loginApiAdmin($this->user);
});

it('can show', function () {
    /** @var User $user */
    $user = (new UserFactory)->create();
    $user->delete();

    getJson(route('admin.users.show', ['user' => $user->id]))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', fn ($json) => $json
                ->has('id')
                ->has('name')
                ->has('email')
                ->has('created_at')
                ->has('updated_at')
                ->has('deleted_at')
            )->etc();
        });
});
