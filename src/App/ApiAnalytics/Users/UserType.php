<?php

namespace App\ApiAnalytics\Users;

use Domain\Users\Models\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'User',
        'description'   => 'A user',
        // Note: only necessary if you use `SelectFields`
        'model'         => User::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the user',
                // Use 'alias', if the database column is different from the type name.
                // This is supported for discrete values as well as relations.
                // - you can also use `DB::raw()` to solve more complex issues
                // - or a callback returning the value (string or `DB::raw()` result)
                // 'alias' => 'user_id',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of user',
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'The email of user',
                'resolve' => fn ($root, array $args): string => // If you want to resolve the field yourself,
// it can be done here
strtolower((string) $root->email),
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The created_at of user',
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The updated_at of user',
            ],
            'deleted_at' => [
                'type' => Type::string(),
                'description' => 'The deleted_at of user',
            ],
            // Uses the 'getIsMeAttribute' function on our custom User model
            'isMe' => [
                'type' => Type::boolean(),
                'description' => 'True, if the queried user is the current user',
                'selectable' => false, // Does not try to query this from the database
            ],
        ];
    }

    // You can also resolve a field by declaring a method in the class
    // with the following format resolve[FIELD_NAME]Field()
    /** @param array<string,string> $args */
    protected function resolveEmailField(User $root, array $args): string
    {
        return strtolower($root->email);
    }
}
