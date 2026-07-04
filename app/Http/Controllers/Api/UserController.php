<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAlbum;
use App\Models\AlbumPhoto;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * 从请求中获取当前用户
     */
    protected function getUserFromToken(Request $request): ?User
    {
        $token = $request->header('Authorization');
        if ($token && str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }
        
        if (!$token) {
            return null;
        }
        
        return User::where('api_token', $token)->first();
    }

    /**
     * 获取当前登录用户资料
     */
    public function profile(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $this->formatUser($user)
        ]);
    }

    /**
     * 获取指定用户资料
     */
    public function getUserProfile($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return response()->json(['code' => 404, 'message' => '用户不存在'], 404);
        }

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $this->formatUser($user)
        ]);
    }

    /**
     * 更新用户资料
     */
    public function updateProfile(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $user->fill($request->only([
            'name', 'gender', 'age', 'height', 'weight', 
            'hobbies', 'specialty', 'love_declaration'
        ]));
        $user->save();

        return response()->json([
            'message' => '更新成功',
            'data' => $this->formatUser($user)
        ]);
    }

    /**
     * 获取积分信息
     */
    public function points(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        return response()->json([
            'message' => 'success',
            'data' => [
                'points' => $user->points,
                'total_points_earned' => $user->total_points_earned,
                'current_level' => $user->current_level ? [
                    'name' => $user->current_level->name,
                    'icon' => $user->current_level->icon,
                    'level_order' => $user->current_level->level_order
                ] : null
            ]
        ]);
    }

    /**
     * 每日签到
     */
    public function signIn(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        // 检查今天是否已签到
        $lastSignIn = $user->last_sign_in_at ?? null;
        if ($lastSignIn && $lastSignIn->isToday()) {
            return response()->json(['message' => '今日已签到'], 400);
        }

        // 增加积分
        $user->increment('points', 10);
        $user->increment('total_points_earned', 10);
        $user->last_sign_in_at = now();
        $user->save();

        return response()->json([
            'message' => '签到成功',
            'data' => [
                'points' => $user->points,
                'total_points_earned' => $user->total_points_earned
            ]
        ]);
    }

    protected function formatUser($user)
    {
        return [
            'id' => $user->id,
            'phone' => $user->phone,
            'name' => $user->name,
            'avatar' => $user->avatar_url,
            'gender' => (int)$user->gender === 1 ? 'male' : ((int)$user->gender === 2 ? 'female' : ''),
            'age' => $user->age,
            'height' => $user->height,
            'weight' => $user->weight,
            'hobbies' => $user->hobbies,
            'specialty' => $user->specialty,
            'love_declaration' => $user->love_declaration,
            'points' => $user->points,
            'total_points_earned' => $user->total_points_earned,
            'coins' => $user->coins,
            'current_level' => $user->current_level ? [
                'name' => $user->current_level->name,
                'icon' => $user->current_level->icon,
                'level_order' => $user->current_level->level_order
            ] : null,
            'has_membership' => $user->hasActiveMembership(),
            'membership' => $user->current_membership ? [
                'name' => $user->current_membership->name,
                'code' => $user->current_membership->code,
                'expired_at' => $user->current_membership->expired_at
            ] : null,
            'created_at' => $user->created_at ? $user->created_at->toISOString() : null
        ];
    }

    public function getAlbum(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $photos = AlbumPhoto::whereHas('album', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('status', 1);
            })
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'photos' => $photos->map(function ($photo) {
                    return [
                        'id' => $photo->id,
                        'url' => $photo->image_url,
                        'thumbnail_url' => $photo->thumbnail_url,
                        'title' => $photo->title,
                        'is_main' => $photo->is_main ?? 0,
                        'is_premium' => $photo->is_premium ?? 0,
                        'points_price' => $photo->points_price ?? 0,
                        'created_at' => $photo->created_at ? $photo->created_at->toISOString() : null
                    ];
                })
            ]
        ]);
    }

    public function uploadAlbumPhoto(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $defaultAlbum = UserAlbum::where('user_id', $user->id)
            ->where('status', 1)
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$defaultAlbum) {
            $defaultAlbum = UserAlbum::create([
                'user_id' => $user->id,
                'name' => '我的照片',
                'description' => '',
                'privacy' => 1,
                'price' => 0,
            ]);
        }

        $request->validate([
            'photo' => 'required|image|max:10240',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $picBedService = app(\App\Services\PicBedService::class);
            $result = $picBedService->upload($file->getRealPath());

            if ($result['success']) {
                $imageUrl = $result['url'];
                $thumbnailUrl = str_contains($imageUrl, '.th.') ? $imageUrl : preg_replace('/\.(jpg|jpeg|png|webp)$/i', '.th.$1', $imageUrl);

                $photo = AlbumPhoto::create([
                    'album_id' => $defaultAlbum->id,
                    'image_url' => $imageUrl,
                    'thumbnail_url' => $thumbnailUrl,
                    'title' => '',
                    'description' => '',
                    'is_main' => 0,
                    'is_premium' => 0,
                    'points_price' => 0,
                    'sort_order' => AlbumPhoto::where('album_id', $defaultAlbum->id)->count() + 1,
                ]);

                return response()->json([
                    'code' => 200,
                    'message' => '图片上传成功',
                    'data' => [
                        'id' => $photo->id,
                        'url' => $photo->image_url,
                        'thumbnail_url' => $photo->thumbnail_url,
                        'is_main' => $photo->is_main,
                        'is_premium' => $photo->is_premium,
                        'points_price' => $photo->points_price
                    ]
                ]);
            }

            return response()->json(['code' => 400, 'message' => $result['message']]);
        }

        return response()->json(['code' => 400, 'message' => '请选择图片']);
    }
}
