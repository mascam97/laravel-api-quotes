<?php

namespace App\ApiAdmin\Activities\Queries;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\QueryBuilder;

class ActivityIndexQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $query = Activity::query()->select([
            'id',
            'log_name',
            'description',
            'subject_type',
            'subject_id',
            'subject',
            'causer_type',
            'causer_id',
            'causer',
            'event',
            'created_at',
            'updated_at',
        ]);

        parent::__construct($query, $request);

        $this->allowedFilters(['log_name'])
            ->allowedIncludes(['subject', 'causer']);
    }
}
