<?php

namespace App\Console\Commands;

use Domain\Users\Models\User;
use Domain\Users\Notifications\NewsletterNotification;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandExitCode;

class SendNewsletterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:newsletter
    {emails?*} : Email address of users to send the newsletter
    {--s|schedule} : If the command is executed directly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an newsletter to verified users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $emails = (array) $this->argument('emails');
        $schedule = $this->option('schedule');
        $builder = User::query()->whereEmailIsVerified()->whereHasEmailSubscribed();

        if ($emails !== []) {
            $builder->whereEmailIn($emails);
        }

        $countUsers = $builder->count();

        if ($countUsers &&
            ($this->confirm("Are you sure to send an email to {$countUsers} users?") || $schedule)
        ) {
            $this->output->progressStart($countUsers);

            $builder->chunkById(10, function ($users) {
                foreach ($users as $user) {
                    $user->notify(new NewsletterNotification()); /* @phpstan-ignore-line */
                    $this->output->progressAdvance();
                }
            });

            $this->output->progressFinish();
            $this->info("{$countUsers} emails were sent.");

            return CommandExitCode::SUCCESS;
        }

        $this->info('0 emails were sent.');

        return CommandExitCode::SUCCESS;
    }
}
