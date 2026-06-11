<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAlbum;
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
            return response()->json(['code' => 401, 'message' => '未授权']);
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
            // 获取用户相册照片
            $albums = UserAlbum::where('user_id', $item->id)
                ->where('status', true)
                ->get();
            
            $photos = [];
            foreach ($albums as $album) {
                foreach ($album->photos as $photo) {
                    if (!$photo->is_premium) {
                        $photos[] = [
                            'id' => $photo->id,
                            'url' => $photo->photo_url,
                            'blur_url' => '',
                            'is_main' => $photo->is_cover ? 1 : 0,
                            'is_premium' => $photo->is_premium,
                            'points_price' => $photo->points_price ?: 0
                        ];
                    }
                }
            }

            // 如果没有相册照片，使用默认图片
            if (empty($photos)) {
                $photos = [
                    [
                        'id' => 0,
                        'url' => $item->avatar_url ?: 'https://neeko-copilot.bytedance.net/api/text_to_image?prompt=beautiful%20portrait%20dating%20app&image_size=portrait_16_9',
                        'blur_url' => '',
                        'is_main' => 1,
                        'is_premium' => 0,
                        'points_price' => 0
                    ]
                ];
            }

            return [
                'id' => $item->id,
                'phone' => $item->phone,
                'nickname' => $item->name,
                'avatar' => $item->avatar_url ?: 'https://neeko-copilot.bytedance.net/api/text_to_image?prompt=avatar%20cute&image_size=square',
                'gender' => $item->gender === 'male' ? 1 : ($item->gender === 'female' ? 2 : 0),
                'birthday' => $item->birthday,
                'age' => $item->age,
                'height' => $item->height,
                'weight' => $item->weight,
                'hobbies' => $item->hobbies,
                'love_declaration' => $item->love_declaration,
                'location' => $item->location ?: '附近',
                'is_vip' => $item->hasActiveMembership() ? 1 : 0,
                'photos' => $photos,
                'distance' => rand(1, 10) . 'km'
            ];
        });

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => ['users' => $result]
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
                'code' => 200,
                'message' => '匹配成功！',
                'data' => [
                    'is_match' => true,
                    'match' => $this->formatUser(User::find($userId))
                ]
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => '已喜欢',
            'data' => ['is_match' => false]
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
            'code' => 200,
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
                'nickname' => $item->name,
                'avatar' => $item->avatar_url ?: 'https://neeko-copilot.bytedance.net/api/text_to_image?prompt=avatar%20cute&image_size=square',
                'gender' => $item->gender === 'male' ? 1 : ($item->gender === 'female' ? 2 : 0),
                'age' => $item->age,
                'last_message' => '来聊聊吧',
                'last_time' => '刚刚'
            ];
        });

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => ['matches' => $result]
        ]);
    }

    protected function formatUser($user)
    {
        if (!$user) return null;
        
        return [
            'id' => $user->id,
            'nickname' => $user->name,
            'avatar' => $user->avatar_url ?: 'https://neeko-copilot.bytedance.net/api/text_to_image?prompt=avatar%20cute&image_size=square',
            'gender' => $user->gender === 'male' ? 1 : ($user->gender === 'female' ? 2 : 0),
            'age' => $user->age,
            'height' => $user->height,
            'hobbies' => $user->hobbies,
            'love_declaration' => $user->love_declaration
        ];
    }
}
