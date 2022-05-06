<?php

namespace Support\Listeners;

use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Support\Events\ModelRated;
use Support\Notifications\ModelRatedNotification;

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
                $event->getQualifier()->name,
                $rateable->title,
                $event->getScore()
            );
            $user = User::find($rateable->user_id);
            $user->notify($notification);
        }
    }
}
