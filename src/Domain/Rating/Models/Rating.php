<?php

namespace Domain\Rating\Models;

use Domain\Quotes\Models\Quote;
use Domain\Rating\QueryBuilders\RatingQueryBuilder;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $score
 * @property int $qualifier_id
 * @property string $qualifier_type
 * @property User $qualifier
 * @property int $rateable_id
 * @property string $rateable_type
 * @property Quote $rateable
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static RatingQueryBuilder query()
 */
class Rating extends Pivot
{
    public $incrementing = true;

    protected $table = 'ratings';

    public function newEloquentBuilder($query): RatingQueryBuilder
    {
        return new RatingQueryBuilder($query);
    }

    /**
     * @return  MorphTo<Model, Rating>
     */
    public function rateable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return  MorphTo<Model, Rating>
     */
    public function qualifier(): MorphTo
    {
        return $this->morphTo();
    }
}
