<?php

namespace Support\Metadata;

use Illuminate\Database\Query\Builder;

class GetQueryMetaDataAction
{
    /**
     * @return array{current_sort: array<int, mixed>|null}
     */
    public function __invoke(Builder $query): array
    {
        return [
            'current_sort' => $query->orders,
        ];
    }
}
