<?php

namespace Domain\Users\QueryBuilders;

use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method User find($columns = ['*'])
 */
class UserQueryBuilder extends Builder
{
    public function whereId(int $id): self
    {
        return $this->where('id', $id);
    }
}
