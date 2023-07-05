<?php

use App\Console\Commands\SendNewsletterCommand;
use Domain\Users\Models\User;
use Domain\Users\Notifications\NewsletterNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\Console\Command\Command as CommandExitCode;

beforeEach(function () {
    Notification::fake();
});

it('does not send newsletters if there is not user', function () {
    $this->artisan(SendNewsletterCommand::class)
        ->expectsOutput('0 emails were sent.')
        ->assertExitCode(CommandExitCode::SUCCESS);

    Notification::assertNothingSent();
});

it('does not send newsletter if user cancel the process', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this->artisan(SendNewsletterCommand::class, ['emails' => [$user->email]])
        ->expectsConfirmation('Are you sure to send an email to 1 users?', 'no')
        ->expectsOutput('0 emails were sent.')
        ->assertExitCode(CommandExitCode::SUCCESS);

    Notification::assertNothingSent();
});

it('sends newsletter only to verified users', function () {
    /** @var User $verifiedUser */
    $verifiedUser = User::factory()->create();
    /** @var User $unverifiedUser */
    $unverifiedUser = User::factory()->unverified()->create();

    $this->artisan(SendNewsletterCommand::class, ['emails' => [$verifiedUser->email, $unverifiedUser->email]])
        ->expectsConfirmation('Are you sure to send an email to 1 users?', 'yes')
        ->expectsOutput('1 emails were sent.')
        ->assertExitCode(CommandExitCode::SUCCESS);

    Notification::assertSentTo($verifiedUser, NewsletterNotification::class);
    Notification::assertNotSentTo($unverifiedUser, NewsletterNotification::class);
});

it('sends newsletter only to email subscribers', function () {
    /** @var User $subscribedUser */
    $subscribedUser = User::factory()->create();
    /** @var User $unsubscribedUser */
    $unsubscribedUser = User::factory()->notEmailSubscribed()->create();

    $this->artisan(SendNewsletterCommand::class, ['emails' => [$subscribedUser->email, $unsubscribedUser->email]])
        ->expectsConfirmation('Are you sure to send an email to 1 users?', 'yes')
        ->expectsOutput('1 emails were sent.')
        ->assertExitCode(CommandExitCode::SUCCESS);

    Notification::assertSentTo($subscribedUser, NewsletterNotification::class);
    Notification::assertNotSentTo($unsubscribedUser, NewsletterNotification::class);
});

it('sends newsletter to selected users', function () {
    /** @var User $userA */
    $userA = User::factory()->create();
    /** @var User $userB */
    $userB = User::factory()->create();

    $this->artisan(SendNewsletterCommand::class, ['emails' => [$userA->email, $userB->email]])
        ->expectsConfirmation('Are you sure to send an email to 2 users?', 'yes')
        ->expectsOutput('2 emails were sent.')
        ->assertExitCode(CommandExitCode::SUCCESS);

    Notification::assertSentTo([$userA, $userB], NewsletterNotification::class);
});

it('sends newsletter to all users', function () {
    User::factory(10)->create();

    $this->artisan(SendNewsletterCommand::class)
        ->expectsConfirmation('Are you sure to send an email to 10 users?', 'yes')
        ->expectsOutput('10 emails were sent.')
        ->assertExitCode(CommandExitCode::SUCCESS);

    Notification::assertCount(10);
});

// TODO: Test schedule option

test('sql queries optimization test', function () {
    User::factory()->create();

    DB::enableQueryLog();

    $this->artisan(SendNewsletterCommand::class)
        ->expectsConfirmation('Are you sure to send an email to 1 users?', 'yes')
        ->assertExitCode(CommandExitCode::SUCCESS);

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(2)
        ->sequence(
            fn ($query) => $query->toBe('select count(*) as aggregate from `users` where `email_verified_at` is not null and `email_subscribed_at` is not null and `users`.`deleted_at` is null'),
            fn ($query) => $query->toBe('select * from `users` where `email_verified_at` is not null and `email_subscribed_at` is not null and `users`.`deleted_at` is null order by `id` asc limit 10'),
        );

    DB::disableQueryLog();
});
