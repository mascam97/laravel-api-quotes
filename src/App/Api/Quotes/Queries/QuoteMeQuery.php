<?php

namespace App\Api\Quotes\Queries;

use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class QuoteMeQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        /** @var User $authUser */
        $authUser = $request->user();

        $query = Quote::query()
            ->select([
                'id',
                'title',
                'content',
                'state',
                'average_score',
                'user_id',
                'created_at',
                'updated_at',
            ])
            ->whereUser($authUser);

        parent::__construct($query, $request);

        $this->allowedFilters(['title', 'content', 'state'])
            ->allowedSorts('id', 'title', 'created_at');
    }
}
