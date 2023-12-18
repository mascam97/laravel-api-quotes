<?php

namespace Domain\Gifts\QueryBuilders;

use Domain\Gifts\Models\Gift;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method select($columns = ['*'])
 * @method count()
 * @method Gift firstOrFail($columns = ['*'])
 */
class GiftQueryBuilder extends Builder
{
    public function whereId(int $id): self
    {
        return $this->where('id', $id);
    }

    public function whereUser(User $user): self
    {
        return $this->where('user_id', $user->getKey());
    }

    public function whereSenderUser(User $user): self
    {
        return $this->where('sender_user_id', $user->getKey());
    }
}
