<?php

namespace App\ApiAdmin\Users\Queries;

use Domain\Users\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class UserIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $query = User::query();

        parent::__construct($query, $request);

        $this->allowedFilters(['id', 'name'])
            ->allowedIncludes(['permissions', 'roles'])
            ->allowedSorts('id', 'name');
    }
}
