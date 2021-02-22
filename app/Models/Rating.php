<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Rating extends Pivot
{
    public $incrementing = true;

    protected $table = 'ratings';

    public function rateable()
    {
        return $this->morphTo();
    }

    public function qualifier()
    {
        return $this->morphTo();
    }
}