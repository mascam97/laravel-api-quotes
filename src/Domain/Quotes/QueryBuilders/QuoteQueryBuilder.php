<?php

namespace Domain\Quotes\QueryBuilders;

use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Quote>
 *
 * @method select($columns = ['*'])
 * @method count()
 * @method Quote firstOrFail($columns = ['*'])
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

    public function whereState(string $state): self
    {
        return $this->where('state', $state);
    }
}
