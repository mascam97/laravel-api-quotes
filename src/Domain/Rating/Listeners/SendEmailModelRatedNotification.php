<?php

namespace Domain\Rating\Listeners;

use Domain\Quotes\Models\Quote;
use Domain\Rating\Events\ModelRated;
use Domain\Rating\Notifications\ModelRatedNotification;
use Domain\Users\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailModelRatedNotification
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(ModelRated $event)
    {
        $rateable = $event->getRateable();
        if ($rateable instanceof Quote) {
            $notification = new ModelRatedNotification(
                $event->getQualifier()->name, /* @phpstan-ignore-line */
                $rateable->title,
                $event->getScore()
            );
            $user = User::query()->find($rateable->user_id);
            $user->notify($notification);
        }
    }
}
