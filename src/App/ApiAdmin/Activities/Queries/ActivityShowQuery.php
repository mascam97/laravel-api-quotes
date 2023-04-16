<?php

namespace App\ApiAdmin\Activities\Queries;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\QueryBuilder;

class ActivityShowQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        $query = Activity::query()
            ->select([
                'id',
                'log_name',
                'description',
                'subject_type',
                'subject_id',
                'causer_type',
                'causer_id',
                'event',
                'created_at',
                'updated_at',
            ]);

        parent::__construct($query, $request);

        $this->allowedIncludes(['subject', 'causer']);
    }
}
