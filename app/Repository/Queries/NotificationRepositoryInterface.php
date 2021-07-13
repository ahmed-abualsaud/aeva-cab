<?php

namespace App\Repository\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

interface NotificationRepositoryInterface
{
    public function notifications($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo);
    public function unreadNotifications($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo);
}