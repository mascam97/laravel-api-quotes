<?php

namespace App\Listeners;

use App\Models\Quote;
use App\Events\ModelRated;
use App\Notifications\ModelRatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailModelRatedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

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
            $user = \App\Models\User::find($rateable->user_id);
            $user->notify($notification);
        }
    }
}
