<?php

namespace App\ApiAdmin\Users\Queries;

use Domain\Users\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $query = User::query()->select([
            'id',
            'name',
            'email',
            'pocket_id',
            'deleted_at',
            'created_at',
            'updated_at',
        ]);

        parent::__construct($query, $request);

        $this->allowedFilters(['id', 'name', AllowedFilter::trashed()])
            // TODO: Support error when there is no pocket
            ->allowedIncludes(['permissions', 'roles', 'pocket'])
            ->defaultSort('created_at')
            ->allowedSorts('id', 'name', 'created_at');
    }
}
