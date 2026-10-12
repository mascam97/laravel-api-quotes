<?php

use Domain\Users\Enums\SexEnum;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\Auth;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    loginApi($this->user);
});

it('updates the authenticated profile', function () {
    patchJson(route('api.profile.update'), [
        'name' => 'Updated name',
        'sex' => SexEnum::FEMININE->value,
        'birthday' => '1990-05-20',
    ])->assertOk()
        ->assertJsonPath('data.name', 'Updated name')
        ->assertJsonPath('data.sex', SexEnum::FEMININE->value)
        ->assertJsonPath('data.birthday', '1990-05-20');

    expect($this->user->fresh()->name)->toBe('Updated name');
});

it('validates profile fields', function () {
    patchJson(route('api.profile.update'), [
        'name' => '',
        'sex' => 'invalid',
        'birthday' => 'tomorrow',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'sex', 'birthday']);
});

it('requires authentication', function () {
    Auth::forgetGuards();

    patchJson(route('api.profile.update'))->assertUnauthorized();
});
