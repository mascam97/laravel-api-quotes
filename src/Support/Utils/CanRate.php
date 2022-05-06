<?php

namespace Support\Utils;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Support\Events\ModelRated;
use Support\Exceptions\InvalidScore;

trait CanRate
{
    public function ratings($model = null): MorphToMany
    {
        $modelClass = $model ? (new $model)->getMorphClass() : $this->getMorphClass();

        $morphToMany = $this->morphToMany(
            $modelClass,
            'qualifier',
            'ratings',
            'qualifier_id',
            'rateable_id'
        );

        $morphToMany
            ->as('rating')
            ->withTimestamps()
            ->withPivot('rateable_type', 'score')
            ->wherePivot('rateable_type', $modelClass)
            ->wherePivot('qualifier_type', $this->getMorphClass());

        return $morphToMany;
    }

    /**
     * @throws InvalidScore
     */
    public function rate(Model $model, float $score): bool
    {
        $min = config('rating.min');
        $max = config('rating.max');
        if ($score < $min || $score > $max) {
            throw new InvalidScore($min, $max);
        }

        $this->ratings($model)->attach($model->getKey(), [
            'score' => $score,
            'rateable_type' => $model::class,
        ]);

        // if the user is not the creator of the quote
        if ($model->user_id !== $this->id) {
            event(new ModelRated($this, $model, $score));
        }

        return true;
    }

    public function unrate(Model $model): bool
    {
        if (! $this->hasRated($model)) {
            return false;
        }

        $this->ratings($model->getMorphClass())->detach($model->getKey());

        return true;
    }

    public function hasRated(Model $model): bool
    {
        return ! is_null($this->ratings($model->getMorphClass())->find($model->getKey()));
    }
}
