<?php

use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->user = User::factory()->create();

    (new UserFactory)->setAmount(4)->create();
});

it('requires authentication', function () {
    postJson(route('graphql'), [])
        ->assertUnauthorized();
});
