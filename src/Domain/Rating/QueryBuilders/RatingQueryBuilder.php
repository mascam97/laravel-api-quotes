<?php

namespace Domain\Rating\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method select($columns = ['*'])
 */
class RatingQueryBuilder extends Builder
{
    public function whereId(int $id): self
    {
        return $this->where('id', $id);
    }

    public function whereQualifier(Model $qualifier): self
    {
        return $this->where('qualifier_id', $qualifier->getKey())
            ->where('qualifier_type', $qualifier->getMorphClass());
    }

    public function whereRateable(Model $rateable): self
    {
        return $this->where('rateable_id', $rateable->getKey())
            ->where('rateable_type', $rateable->getMorphClass());
    }
}
