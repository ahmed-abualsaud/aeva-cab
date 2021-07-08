<?php

namespace App\Repository\Eloquent\Queries;   

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Repository\Queries\NotificationRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;   

class NotificationRepository implements NotificationRepositoryInterface
{
   
    public function notifications($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $notifications = auth('partner')->user()
            ->notifications()
            ->select('id', 'data', 'read_at', 'created_at')
            ->get();

        return $notifications;   
    }

    public function unreadNotifications($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $unreadNotifications = tap(auth('partner')->user()
            ->unreadNotifications()
            ->select('id', 'data', 'created_at')
            ->get())->markAsRead();

        return $unreadNotifications;
    }
}

