<?php

namespace Domain\Rating\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModelRated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        private Model $qualifier,
        private Model $rateable,
        private float $score
    ) {
    }

    public function getQualifier(): Model
    {
        return $this->qualifier;
    }

    public function getRateable(): Model
    {
        return $this->rateable;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel|array
    {
        return new PrivateChannel('channel-name');
    }
}