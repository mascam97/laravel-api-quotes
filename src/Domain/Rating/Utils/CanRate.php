<?php

namespace Domain\Rating\Utils;

use Domain\Quotes\Models\Quote;
use Domain\Rating\Events\ModelRated;
use Domain\Rating\Exceptions\InvalidScoreException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait CanRate
{
    public function ratings(null|string|Model $model = null): MorphToMany
    {
        $modelClass = $model ? (new $model())->getMorphClass() : $this->getMorphClass(); /* @phpstan-ignore-line */

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
     *
     * @throws InvalidScoreException
     */
    public function rate(Model $model, int|null $score): bool
    {
        $min = (int) config('rating.min');
        $max = (int) config('rating.max');
        if ($score < $min || $score > $max) {
            throw new InvalidScoreException($min, $max);
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
