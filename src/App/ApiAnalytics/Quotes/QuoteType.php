<?php

namespace App\ApiAnalytics\Quotes;

use Domain\Quotes\Models\Quote;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class QuoteType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'Quote',
        'description'   => 'A quote',
        // Note: only necessary if you use `SelectFields`
        'model'         => Quote::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the quote',
            ],
            'title' => [
                'type' => Type::string(),
                'description' => 'The title of quote',
            ],
            'content' => [
                'type' => Type::string(),
                'description' => 'The content of quote',
            ],
            'state' => [
                'type' => Type::string(),
                'description' => 'The state of quote',
            ],
            'user_id' => [
                'type' => Type::int(),
                'description' => 'The user_id of quote',
            ],
            'user' => [
                'type' => GraphQL::type('User'),
                'description' => 'The user of quote',
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The created_at of quote',
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The updated_at of quote',
            ],
        ];
    }
}
