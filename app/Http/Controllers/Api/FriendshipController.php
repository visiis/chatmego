<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Friendship;
use App\Services\NotificationService;

class FriendshipController extends Controller
{
    /**
     * 获取好友列表
     */
    public function friends()
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权'], 401);
        }

        $friendships = Friendship::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)->orWhere('friend_id', $user->id);
        })
        ->where('status', 'accepted')
        ->with(['user', 'friend'])
        ->get();

        $friends = $friendships->map(function ($friendship) use ($user) {
            if ($friendship->user_id === $user->id) {
                $friendUser = $friendship->friend;
            } else {
                $friendUser = $friendship->user;
            }
            
            return [
                'id' => $friendUser->id,
                'name' => $friendUser->name ?: $friendUser->nickname,
                'nickname' => $friendUser->nickname ?: $friendUser->name,
                'avatar' => $friendUser->avatar ?: '',
                'gender' => $friendUser->gender ?: 0,
                'love_declaration' => '',
                'status' => 'online'
            ];
        });

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => ['friends' => $friends]
        ]);
    }

    /**
     * 获取好友申请列表
     */
    public function requests()
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权'], 401);
        }

        $friendships = Friendship::where('friend_id', $user->id)
            ->where('status', 'pending')
            ->with('user')
            ->get();

        $requests = $friendships->map(function ($friendship) {
            return [
                'id' => $friendship->id,
                'user' => [
                    'id' => $friendship->user->id,
                    'name' => $friendship->user->name ?: $friendship->user->nickname,
                    'nickname' => $friendship->user->nickname ?: $friendship->user->name,
                    'avatar' => $friendship->user->avatar ?: ''
                ],
                'message' => '',
                'created_at' => $friendship->created_at->toDateTimeString()
            ];
        });

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $requests
        ]);
    }

    /**
     * 发送好友申请
     */
    public function sendRequest($userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权'], 401);
        }

        if ($user->id == $userId) {
            return response()->json(['code' => 400, 'message' => '不能添加自己为好友'], 400);
        }

        $exists = Friendship::where(function ($query) use ($user, $userId) {
            $query->where('user_id', $user->id)->where('friend_id', $userId)
                  ->orWhere('user_id', $userId)->where('friend_id', $user->id);
        })->exists();

        if ($exists) {
            return response()->json(['code' => 400, 'message' => '已发送过好友请求'], 400);
        }

        Friendship::create([
            'user_id' => $user->id,
            'friend_id' => $userId,
            'status' => 'pending'
        ]);

        NotificationService::sendFriendRequest($userId, $user->id);

        return response()->json([
            'code' => 200,
            'message' => '申请已发送',
            'data' => []
        ]);
    }

    /**
     * 接受好友申请
     */
    public function acceptRequest($userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权'], 401);
        }

        $friendship = Friendship::where('user_id', $userId)
            ->where('friend_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$friendship) {
            return response()->json(['code' => 400, 'message' => '好友请求不存在'], 400);
        }

        $friendship->update(['status' => 'accepted']);

        return response()->json([
            'code' => 200,
            'message' => '已添加好友',
            'data' => []
        ]);
    }

    /**
     * 拒绝好友申请
     */
    public function rejectRequest($userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权'], 401);
        }

        $friendship = Friendship::where('user_id', $userId)
            ->where('friend_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$friendship) {
            return response()->json(['code' => 400, 'message' => '好友请求不存在'], 400);
        }

        $friendship->update(['status' => 'rejected']);

        return response()->json([
            'code' => 200,
            'message' => '已拒绝申请',
            'data' => []
        ]);
    }

    /**
     * 获取黑名单列表
     */
    public function blocked()
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权'], 401);
        }

        $friendships = Friendship::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)->orWhere('friend_id', $user->id);
        })
        ->where('status', 'blocked')
        ->with(['user', 'friend'])
        ->get();

        $blocked = $friendships->map(function ($friendship) use ($user) {
            if ($friendship->user_id === $user->id) {
                $friendUser = $friendship->friend;
            } else {
                $friendUser = $friendship->user;
            }
            
            return [
                'id' => $friendUser->id,
                'name' => $friendUser->name ?: $friendUser->nickname,
                'nickname' => $friendUser->nickname ?: $friendUser->name,
                'avatar' => $friendUser->avatar ?: '',
                'gender' => $friendUser->gender ?: 0,
                'love_declaration' => ''
            ];
        });

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => ['blocked' => $blocked]
        ]);
    }

    /**
     * 拉黑好友
     */
    public function block($userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权'], 401);
        }

        $friendship = Friendship::where(function ($query) use ($user, $userId) {
            $query->where('user_id', $user->id)->where('friend_id', $userId)
                  ->orWhere('user_id', $userId)->where('friend_id', $user->id);
        })->first();

        if (!$friendship) {
            return response()->json(['code' => 400, 'message' => '好友关系不存在'], 400);
        }

        $friendship->update(['status' => 'blocked']);

        return response()->json([
            'code' => 200,
            'message' => '已拉黑',
            'data' => []
        ]);
    }

    /**
     * 解除拉黑
     */
    public function unblock($userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权'], 401);
        }

        $friendship = Friendship::where(function ($query) use ($user, $userId) {
            $query->where('user_id', $user->id)->where('friend_id', $userId)
                  ->orWhere('user_id', $userId)->where('friend_id', $user->id);
        })
        ->where('status', 'blocked')
        ->first();

        if (!$friendship) {
            return response()->json(['code' => 400, 'message' => '拉黑关系不存在'], 400);
        }

        $friendship->update(['status' => 'accepted']);

        return response()->json([
            'code' => 200,
            'message' => '已解除拉黑',
            'data' => []
        ]);
    }

    /**
     * 删除好友
     */
    public function delete($userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权'], 401);
        }

        $friendship = Friendship::where(function ($query) use ($user, $userId) {
            $query->where('user_id', $user->id)->where('friend_id', $userId)
                  ->orWhere('user_id', $userId)->where('friend_id', $user->id);
        })
        ->where('status', 'accepted')
        ->first();

        if (!$friendship) {
            return response()->json(['code' => 400, 'message' => '好友关系不存在'], 400);
        }

        $friendship->delete();

        return response()->json([
            'code' => 200,
            'message' => '已删除好友',
            'data' => []
        ]);
    }
}
