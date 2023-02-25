<?php

namespace App\ApiAdmin\Activities\Queries;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\QueryBuilder;

class ActivityIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $query = Activity::query();

        parent::__construct($query, $request);

        $this->allowedFilters(['log_name'])
            ->allowedIncludes(['subject', 'causer']);
    }
}
