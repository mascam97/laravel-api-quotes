<?php

namespace Domain\Quotes\QueryBuilders;

use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method select($columns = ['*'])
 * @method count()
 */
class QuoteQueryBuilder extends Builder
{
    public function whereId(int $id): self
    {
        return $this->where('id', $id);
    }

    public function whereUser(User $user): self
    {
        return $this->where('user_id', $user->getKey());
    }
}
