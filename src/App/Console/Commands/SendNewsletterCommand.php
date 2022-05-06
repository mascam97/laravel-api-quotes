<?php

namespace App\Console\Commands;

use Domain\Users\Models\User;
use Illuminate\Console\Command;
use Support\Notifications\NewsletterNotification;

class SendNewsletterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:newsletter
    {emails?*} : Email address to send directly
    {--s|schedule} : If the command is executed directly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $emails = $this->argument('emails');
        $builder = User::query();
        $schedule = $this->option('schedule');

        if ($emails) {
            $builder->whereIn('email', $emails);
        }

        $count = $builder->count();
        if ($count &&
            ($this->confirm("Are you sure to send an email to $count users?") || $schedule)
        ) {
            $this->output->progressStart($count);
            // TODO: whereNotNull("email_verified_at") should work
            $builder->each(function (User $user) {
                $user->notify(new NewsletterNotification());
                $this->output->progressAdvance();
            });
            $this->output->progressFinish();
            $this->info(" $count emails were sent.");

            return;
        }
        $this->info(' 0 emails were sent.');
    }
}
