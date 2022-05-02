<?php

namespace App\Jobs\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Support\Mail\WelcomeEmail;

class SendWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private string $userEmail)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        TODO: check spatie/laravel-queueable-action
        $email = new WelcomeEmail();

        Mail::to($this->userEmail)->send($email);
    }
}
