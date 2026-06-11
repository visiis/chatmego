<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DiscoverController extends Controller
{
    /**
     * 获取推荐用户
     */
    public function recommend()
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        // 获取未喜欢/未跳过的用户
        $excludedIds = DB::table('user_interactions')
            ->where('user_id', $user->id)
            ->pluck('target_user_id')
            ->toArray();
        $excludedIds[] = $user->id;

        $users = User::whereNotIn('id', $excludedIds)
            ->where('is_active', true)
            ->where('status', 'active')
            ->inRandomOrder()
            ->limit(10)
            ->get();

        $result = $users->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'avatar' => $item->avatar_url,
                'gender' => $item->gender,
                'age' => $item->age,
                'height' => $item->height,
                'hobbies' => $item->hobbies,
                'specialty' => $item->specialty,
                'love_declaration' => $item->love_declaration,
                'distance' => rand(1, 10) . 'km'
            ];
        });

        return response()->json([
            'message' => 'success',
            'data' => $result
        ]);
    }

    /**
     * 喜欢用户
     */
    public function like($userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        if ($user->id == $userId) {
            return response()->json(['message' => '不能喜欢自己'], 400);
        }

        // 记录喜欢
        DB::table('user_interactions')->updateOrInsert(
            ['user_id' => $user->id, 'target_user_id' => $userId],
            ['type' => 'like', 'created_at' => now()]
        );

        // 检查是否匹配（对方也喜欢了自己）
        $matched = DB::table('user_interactions')
            ->where('user_id', $userId)
            ->where('target_user_id', $user->id)
            ->where('type', 'like')
            ->exists();

        if ($matched) {
            // 创建匹配记录
            DB::table('matches')->updateOrInsert(
                ['user1_id' => min($user->id, $userId), 'user2_id' => max($user->id, $userId)],
                ['created_at' => now()]
            );

            return response()->json([
                'message' => '匹配成功！',
                'data' => [
                    'matched' => true,
                    'user' => $this->formatUser(User::find($userId))
                ]
            ]);
        }

        return response()->json([
            'message' => '已喜欢',
            'data' => ['matched' => false]
        ]);
    }

    /**
     * 跳过用户
     */
    public function dislike($userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        if ($user->id == $userId) {
            return response()->json(['message' => '不能跳过自己'], 400);
        }

        // 记录跳过
        DB::table('user_interactions')->updateOrInsert(
            ['user_id' => $user->id, 'target_user_id' => $userId],
            ['type' => 'dislike', 'created_at' => now()]
        );

        return response()->json([
            'message' => '已跳过',
            'data' => []
        ]);
    }

    /**
     * 获取匹配列表
     */
    public function matches()
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        // 获取匹配的用户
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
                'name' => $item->name,
                'avatar' => $item->avatar_url,
                'gender' => $item->gender,
                'age' => $item->age,
                'last_message' => '来聊聊吧',
                'last_time' => '刚刚'
            ];
        });

        return response()->json([
            'message' => 'success',
            'data' => $result
        ]);
    }

    protected function formatUser($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar_url,
            'gender' => $user->gender,
            'age' => $user->age,
            'height' => $user->height,
            'hobbies' => $user->hobbies,
            'specialty' => $user->specialty,
            'love_declaration' => $user->love_declaration
        ];
    }
}
