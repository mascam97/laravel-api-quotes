<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

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
