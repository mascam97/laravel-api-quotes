<?php

namespace Domain\Rating\Utils;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait CanBeRated
{
    /**
     * @param string|null $model
     */
    public function qualifiers(string $model = null): MorphToMany
    {
        $modelClass = $model ? (new $model)->getMorphClass() : $this->getMorphClass(); /* @phpstan-ignore-line */

        return $this->morphToMany($modelClass, 'rateable', 'ratings', 'rateable_id', 'qualifier_id')
            ->withPivot('qualifier_type', 'score')
            ->wherePivot('qualifier_type', $modelClass)
            ->wherePivot('rateable_type', $this->getMorphClass());
    }

    /**
     * @param string|null $model
     */
    public function averageRating(string $model = null): float
    {
        return round($this->qualifiers($model)->avg('score'), 1) ?: 0.0;
    }
}
