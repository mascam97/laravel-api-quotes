<?php

namespace Domain\Rating\Utils;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait CanBeRated
{
    public function qualifiers(?string $model = null): MorphToMany
    {
        $modelClass = $model ? (new $model())->getMorphClass() : $this->getMorphClass(); /* @phpstan-ignore-line */

        return $this->morphToMany($modelClass, 'rateable', 'ratings', 'rateable_id', 'qualifier_id')
            ->withPivot('qualifier_type', 'score')
            ->wherePivot('qualifier_type', $modelClass)
            ->wherePivot('rateable_type', $this->getMorphClass());
    }

    public function averageRating(?string $model = null): ?float
    {
        /** @var int|float $modelScore */
        $modelScore = $this->qualifiers($model)->avg('score');

        if ($modelScore === 0.0) {
            return 0.0;
        }

        return round($modelScore, 1) ?: null;
    }
}
