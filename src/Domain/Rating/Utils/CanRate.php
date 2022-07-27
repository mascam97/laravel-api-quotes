<?php

namespace Domain\Rating\Utils;

use Domain\Quotes\Models\Quote;
use Domain\Rating\Events\ModelRated;
use Domain\Rating\Exceptions\InvalidScore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait CanRate
{
    public function ratings(null|string|Model $model = null): MorphToMany
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
     * @param Quote $model
     * @param float $score
     * @return bool
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

        $this->ratings($model)->detach($model->getKey());

        return true;
    }

    public function hasRated(Model $model): bool
    {
        return ! is_null($this->ratings($model)->find($model->getKey()));
    }
}
