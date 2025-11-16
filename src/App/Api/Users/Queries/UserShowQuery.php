<?php

namespace App\Api\Users\Queries;

use Domain\Users\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

class UserShowQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $query = User::query()
            ->select([
                'id',
                'name',
                'email',
                'created_at',
            ]);

        parent::__construct($query, $request);

        $this->allowedIncludes([
            'quotes',
            AllowedInclude::count('quotesCount'),
            AllowedInclude::exists('quotesExists'),
        ]);
    }
}
