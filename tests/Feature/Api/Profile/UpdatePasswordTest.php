<?php

use Database\Seeders\PassportClientsSeeder;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Token;
use function Pest\Laravel\patchJson;

beforeEach(function () {
    $this->seed(PassportClientsSeeder::class);
    $this->user = User::factory()->create([
        'password' => Hash::make('current.Password9!'),
    ]);

    loginApi($this->user);
});

it('changes the password and revokes passport tokens', function () {
    $token = $this->user->createToken('test')->token;

    patchJson(route('api.profile.password.update'), [
        'current_password' => 'current.Password9!',
        'password' => 'New.Strong9Password!',
        'password_confirmation' => 'New.Strong9Password!',
    ])->assertOk()
        ->assertJson(['message' => 'The password was updated successfully']);

    expect(Hash::check('New.Strong9Password!', $this->user->fresh()->password))->toBeTrue()
        ->and(Token::query()->whereKey($token->getKey())->exists())->toBeFalse();
});

it('requires the current password and confirmation', function () {
    patchJson(route('api.profile.password.update'), [
        'current_password' => 'wrong-password',
        'password' => 'New.Strong9Password!',
        'password_confirmation' => 'different.Password9!',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['current_password', 'password']);
});

it('requires authentication', function () {
    Auth::forgetGuards();

    patchJson(route('api.profile.password.update'))->assertUnauthorized();
});
