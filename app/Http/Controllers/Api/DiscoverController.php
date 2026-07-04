<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAlbum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscoverController extends Controller
{
    /**
     * 获取用户列表（发现页面）
     * 完全复制 chatmego.com/home 的逻辑
     */
    public function cards(Request $request)
    {
        $token = $request->header('Authorization');
        if ($token && str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }
        
        $user = User::where('api_token', $token)->first();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权']);
        }
        
        $authUserId = $user->id;

        // 获取已是好友的用户 ID（只包括 accepted 状态）
        $friendIds = DB::table('friendships')
            ->where('user_id', $authUserId)
            ->where('status', 'accepted')
            ->pluck('friend_id')
            ->merge(
                DB::table('friendships')
                    ->where('friend_id', $authUserId)
                    ->where('status', 'accepted')
                    ->pluck('user_id')
            )
            ->unique()
            ->toArray();
        
        // 排除自己和已是好友的用户
        $excludedIds = array_merge([$authUserId], $friendIds);

        $search = $request->input('search', '');
        
        // 获取用户列表（和 home 页面完全一样的逻辑）
        $query = User::where('id', '!=', $authUserId)
                    ->whereNotIn('id', $friendIds);
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('love_declaration', 'like', '%' . $search . '%')
                  ->orWhere('hobbies', 'like', '%' . $search . '%');
            });
        }
        
        $users = $query->get();

        $result = $users->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'avatar' => avatar_url($item->avatar),
                'gender' => (int)$item->gender === 1 ? 'male' : ((int)$item->gender === 2 ? 'female' : ''),
                'age' => $item->age,
                'height' => $item->height,
                'weight' => $item->weight,
                'hobbies' => $item->hobbies,
                'love_declaration' => $item->love_declaration,
                'is_vip' => $item->hasActiveMembership() ? 1 : 0,
            ];
        });

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $result
        ]);
    }

    /**
     * 获取推荐用户
     */
    public function recommend(Request $request)
    {
        $token = $request->header('Authorization');
        if ($token && str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }
        
        $user = User::where('api_token', $token)->first();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权']);
        }

        // 获取未喜欢/未跳过的用户
        $excludedIds = [$user->id];
        try {
            $interactionIds = DB::table('user_interactions')
                ->where('user_id', $user->id)
                ->pluck('target_user_id')
                ->toArray();
            $excludedIds = array_merge($excludedIds, $interactionIds);
        } catch (\Exception $e) {
            // 表不存在时忽略
        }

        $users = User::whereNotIn('id', $excludedIds)
            ->where('is_active', true)
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
                foreach ($album->photos()->orderBy('created_at', 'desc')->get() as $photo) {
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
                'gender' => (int)$item->gender === 1 ? 'male' : ((int)$item->gender === 2 ? 'female' : ''),
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
    public function like(Request $request, $userId)
    {
        $token = $request->header('Authorization');
        if ($token && str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }
        
        $user = User::where('api_token', $token)->first();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权']);
        }

        if ($user->id == $userId) {
            return response()->json(['code' => 400, 'message' => '不能喜欢自己']);
        }

        $matched = false;
        try {
            DB::table('user_interactions')->updateOrInsert(
                ['user_id' => $user->id, 'target_user_id' => $userId],
                ['type' => 'like', 'created_at' => now()]
            );

            $matched = DB::table('user_interactions')
                ->where('user_id', $userId)
                ->where('target_user_id', $user->id)
                ->where('type', 'like')
                ->exists();

            if ($matched) {
                DB::table('matches')->updateOrInsert(
                    ['user1_id' => min($user->id, $userId), 'user2_id' => max($user->id, $userId)],
                    ['created_at' => now()]
                );
            }
        } catch (\Exception $e) {
            // 表不存在时忽略
        }

        if ($matched) {
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
    public function dislike(Request $request, $userId)
    {
        $token = $request->header('Authorization');
        if ($token && str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }
        
        $user = User::where('api_token', $token)->first();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权']);
        }

        if ($user->id == $userId) {
            return response()->json(['code' => 400, 'message' => '不能跳过自己']);
        }

        try {
            DB::table('user_interactions')->updateOrInsert(
                ['user_id' => $user->id, 'target_user_id' => $userId],
                ['type' => 'dislike', 'created_at' => now()]
            );
        } catch (\Exception $e) {
            // 表不存在时忽略
        }

        return response()->json([
            'code' => 200,
            'message' => '已跳过',
            'data' => []
        ]);
    }

    /**
     * 跳过用户（pass接口，与dislike逻辑相同）
     */
    public function pass(Request $request, $userId)
    {
        return $this->dislike($request, $userId);
    }

    /**
     * 获取匹配列表
     */
    public function matches(Request $request)
    {
        $token = $request->header('Authorization');
        if ($token && str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }
        
        $user = User::where('api_token', $token)->first();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权']);
        }

        // 获取匹配的用户
        $matchIds = [];
        try {
            $matchIds = DB::table('matches')
                ->where('user1_id', $user->id)
                ->orWhere('user2_id', $user->id)
                ->get()
                ->map(function ($match) use ($user) {
                    return $match->user1_id == $user->id ? $match->user2_id : $match->user1_id;
                })
                ->toArray();
        } catch (\Exception $e) {
            // 表不存在时忽略
        }

        $users = User::whereIn('id', $matchIds)->get();

        $result = $users->map(function ($item) {
            return [
                'id' => $item->id,
                'nickname' => $item->name,
                'avatar' => $item->avatar_url ?: 'https://neeko-copilot.bytedance.net/api/text_to_image?prompt=avatar%20cute&image_size=square',
                'gender' => (int)$item->gender === 1 ? 'male' : ((int)$item->gender === 2 ? 'female' : ''),
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
            'gender' => (int)$user->gender === 1 ? 'male' : ((int)$user->gender === 2 ? 'female' : ''),
            'age' => $user->age,
            'height' => $user->height,
            'hobbies' => $user->hobbies,
            'love_declaration' => $user->love_declaration
        ];
    }
}
