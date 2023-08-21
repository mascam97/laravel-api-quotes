<?php

use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\deleteJson;

beforeEach(function () {
    $this->user = User::factory()->create(['password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi']);

    (new UserFactory)->setAmount(4)->create();

    loginApi($this->user);
});

it('requires password', function () {
    deleteJson(route('api.profile.destroy'))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password' => 'The password field is required.']);

    assertModelExists($this->user);
});

it('validates password', function () {
    deleteJson(route('api.profile.destroy'), ['password' => 'wrong-password'])
        ->assertStatus(400)
        ->assertJson(function (AssertableJson $json) {
            $json->where('message', 'These credentials do not match our records.');
        });

    assertModelExists($this->user);
});
