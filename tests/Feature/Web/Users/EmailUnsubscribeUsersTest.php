<?php

use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('cannot unsubscribe without signature', function () {
    get(route('web.email-unsubscribe-users', ['userId' => $this->user->getKey()]))->assertForbidden();
});

it('cannot unsubscribe with invalid signature', function () {
    get(route(
        name: 'web.email-unsubscribe-users',
        parameters: ['userId' => $this->user->getKey(), 'signature' => 'invalid-signature'],
    ))->assertForbidden();
});

it('can unsubscribe a user with signature', function () {
    get(URL::signedRoute('web.email-unsubscribe-users', ['userId' => $this->user->getKey()]))
        ->assertOk()
        ->assertSee('Email unsubscribed.');

    $this->user->refresh();

    expect($this->user->email_subscribed_at)->toBeNull();

    get(URL::signedRoute('web.email-unsubscribe-users', ['userId' => $this->user->getKey()]))
        ->assertOk()
        ->assertSee('Email already unsubscribed.');
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();

    get(URL::signedRoute('web.email-unsubscribe-users', ['userId' => $this->user->getKey()]))->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(2)
        ->sequence(
            fn ($query) => $query->toBe('select * from `users` where `users`.`id` = ? and `users`.`deleted_at` is null limit 1'),
            fn ($query) => $query->toBe('update `users` set `email_subscribed_at` = ?, `users`.`updated_at` = ? where `id` = ?'),
        );

    DB::disableQueryLog();
});
