<?php

namespace Domain\Rating\Listeners;

use Domain\Quotes\Models\Quote;
use Domain\Rating\Events\ModelRated;
use Domain\Rating\Notifications\ModelRatedNotification;
use Domain\Users\Models\User;

class SendEmailModelRatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(ModelRated $event): void
    {
        $rateable = $event->getRateable();
        if ($rateable instanceof Quote) {
            $notification = new ModelRatedNotification(
                $event->getQualifier()->getKey(),
                $event->getQualifier()->name, /* @phpstan-ignore-line */
                $rateable->title,
                $event->getScore()
            );
            $user = User::query()->find($rateable->user_id);
            $user->notify($notification);
        }
    }
}
