<?php

namespace App\Api\Users\Queries;

use Domain\Users\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class UserIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $query = User::query()->select([
            'id',
            'name',
            'email',
            'created_at',
        ]);

        parent::__construct($query, $request);

        $this->allowedFilters(['id', 'name'])
            ->allowedIncludes('quotes')
            ->defaultSort('created_at')
            ->allowedSorts('id', 'name', 'created_at');
    }
}
