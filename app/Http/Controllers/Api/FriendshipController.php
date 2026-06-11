<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FriendshipController extends Controller
{
    /**
     * 获取好友列表
     */
    public function friends()
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        // 从匹配中获取好友（简化处理）
        $matchIds = DB::table('matches')
            ->where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->get()
            ->map(function ($match) use ($user) {
                return $match->user1_id == $user->id ? $match->user2_id : $match->user1_id;
            })
            ->toArray();

        $users = User::whereIn('id', $matchIds)->get();

        $result = $users->map(function ($item) {
            return [
                'id' => $item->id,
                'nickname' => $item->name,
                'avatar' => $item->avatar_url,
                'gender' => $item->gender,
                'age' => $item->age,
                'love_declaration' => $item->love_declaration,
                'status' => 'online'
            ];
        });

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => ['friends' => $result]
        ]);
    }

    /**
     * 获取好友申请列表
     */
    public function requests()
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        // 模拟好友申请
        $requests = [];

        return response()->json([
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
            return response()->json(['message' => '未授权'], 401);
        }

        if ($user->id == $userId) {
            return response()->json(['message' => '不能添加自己为好友'], 400);
        }

        return response()->json([
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
            return response()->json(['message' => '未授权'], 401);
        }

        return response()->json([
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
            return response()->json(['message' => '未授权'], 401);
        }

        return response()->json([
            'message' => '已拒绝申请',
            'data' => []
        ]);
    }
}
