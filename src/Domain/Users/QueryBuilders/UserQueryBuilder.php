<?php

namespace Domain\Users\QueryBuilders;

use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<User>
 *
 * @method User find($columns = ['*'])
 * @method User findOrFail($columns = ['*'])
 * @method select($columns = ['*'])
 * @method count()
 */
class UserQueryBuilder extends Builder
{
    public function whereId(int $id): self
    {
        return $this->where('id', $id);
    }

    /**
     * @param array<string> $emails
     */
    public function whereEmailIn(array $emails): self
    {
        return $this->whereIn('email', $emails);
    }

    public function whereEmailIsVerified(): self
    {
        return $this->whereNotNull('email_verified_at');
    }

    public function whereHasEmailSubscribed(): self
    {
        return $this->whereNotNull('email_subscribed_at');
    }
}
