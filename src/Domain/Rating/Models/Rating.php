<?php

namespace Domain\Rating\Models;

use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property Quote $rateable
 * @property User $qualifier
 */
class Rating extends Pivot
{
    public $incrementing = true;

    protected $table = 'ratings';

    public function rateable(): MorphTo
    {
        return $this->morphTo();
    }

    public function qualifier(): MorphTo
    {
        return $this->morphTo();
    }
}
