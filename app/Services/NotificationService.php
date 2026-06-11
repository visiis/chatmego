<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public static function sendFriendRequest($toUserId, $fromUserId)
    {
        Notification::create([
            'user_id' => $toUserId,
            'type' => 'friend_request',
            'from_user_id' => $fromUserId,
            'data' => []
        ]);
    }

    public static function sendMessage($toUserId, $fromUserId, $message)
    {
        Notification::create([
            'user_id' => $toUserId,
            'type' => 'message',
            'from_user_id' => $fromUserId,
            'data' => ['message' => $message]
        ]);
    }

    public static function sendLike($toUserId, $fromUserId)
    {
        Notification::create([
            'user_id' => $toUserId,
            'type' => 'like',
            'from_user_id' => $fromUserId,
            'data' => []
        ]);
    }

    public static function sendComment($toUserId, $fromUserId, $comment)
    {
        Notification::create([
            'user_id' => $toUserId,
            'type' => 'comment',
            'from_user_id' => $fromUserId,
            'data' => ['comment' => $comment]
        ]);
    }
}