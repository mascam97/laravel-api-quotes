<?php

namespace App\Listeners;

use App\Events\ModelRated;
use App\Models\Quote;
use App\Models\User;
use App\Notifications\ModelRatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailModelRatedNotification
{
    /**
     * Handle the event.
     *
     * @param  ModelRated  $event
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
