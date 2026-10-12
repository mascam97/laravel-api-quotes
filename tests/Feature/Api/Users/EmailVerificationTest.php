<?php

use Database\Seeders\PassportClientsSeeder;
use Domain\Users\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->seed(PassportClientsSeeder::class);
    $this->user = User::factory()->create(['email_verified_at' => null]);
    Notification::fake();
});

it('sends a verification notification after registration', function () {
    postJson(route('api.register'), [
        'name' => 'New user',
        'email' => 'new-user@mail.com',
        'password' => 'Strong.Pa55word!',
    ])->assertOk();

    $user = User::query()->where('email', 'new-user@mail.com')->firstOrFail();

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('verifies an email with a valid signed URL', function () {
    $url = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(10),
        ['id' => $this->user->getKey(), 'hash' => sha1($this->user->getEmailForVerification())]
    );

    getJson($url)->assertOk()
        ->assertJson([
            'message' => 'Your email has been verified successfully',
            'verified' => true,
        ]);

    expect($this->user->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('rejects an invalid verification signature', function () {
    getJson(route('verification.verify', [
        'id' => $this->user->getKey(),
        'hash' => sha1($this->user->getEmailForVerification()),
    ]))->assertForbidden();
});

it('resends a verification notification', function () {
    loginApi($this->user);

    postJson(route('api.verification.send'))->assertStatus(202)
        ->assertJson(['message' => 'The verification link has been sent to your email']);

    Notification::assertSentTo($this->user, VerifyEmail::class);
});

it('does not resend verification after the email is verified', function () {
    $this->user->markEmailAsVerified();
    loginApi($this->user);

    postJson(route('api.verification.send'))->assertOk()
        ->assertJson(['message' => 'Your email is already verified']);

    Notification::assertNothingSent();
});
