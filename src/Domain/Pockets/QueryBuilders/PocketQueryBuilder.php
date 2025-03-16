<?php

namespace Domain\Pockets\QueryBuilders;

use Domain\Pockets\Models\Pocket;
use Domain\Users\Models\User;
use Domain\Users\QueryBuilders\UserQueryBuilder;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Pocket>
 *
 * @method select($columns = ['*'])
 * @method count()
 * @method Pocket firstOrFail($columns = ['*'])
 */
class PocketQueryBuilder extends Builder
{
    public function whereId(int $id): self
    {
        return $this->where('id', $id);
    }

    public function whereUser(User $user): self
    {
        return $this->whereHas('user', fn (UserQueryBuilder $query) => $query->whereId($user->getKey()));
    }

    public function whereCurrency(string $currency): self
    {
        return $this->where('currency', $currency);
    }
}
