<?php

namespace App\ApiAnalytics;

use App\ApiAnalytics\Quotes\QuotesQuery;
use App\ApiAnalytics\Quotes\QuoteType;
use App\ApiAnalytics\Users\UsersQuery;
use App\ApiAnalytics\Users\UserType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class DefaultSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                UsersQuery::class,
                QuotesQuery::class,
            ],
            'mutation' => [
                // ...
            ],
            'types' => [
                UserType::class,
                QuoteType::class,
            ],
        ];
    }
}
