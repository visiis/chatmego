<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\User;
use Illuminate\Http\Request;

class AttractionController extends Controller
{
    public function like($userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权'], 401);
        }

        if ($user->id == $userId) {
            return response()->json(['code' => 400, 'message' => '不能喜欢自己'], 400);
        }

        $targetUser = User::find($userId);
        if (!$targetUser) {
            return response()->json(['code' => 404, 'message' => '用户不存在'], 404);
        }

        $attraction = Attraction::updateOrCreate(
            ['from_user_id' => $user->id, 'to_user_id' => $userId],
            ['type' => 1]
        );

        $mutual = Attraction::where('from_user_id', $userId)
            ->where('to_user_id', $user->id)
            ->where('type', 1)
            ->exists();

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'is_mutual' => $mutual,
                'type' => $attraction->type
            ]
        ]);
    }

    public function dislike($userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权'], 401);
        }

        if ($user->id == $userId) {
            return response()->json(['code' => 400, 'message' => '不能讨厌自己'], 400);
        }

        $targetUser = User::find($userId);
        if (!$targetUser) {
            return response()->json(['code' => 404, 'message' => '用户不存在'], 404);
        }

        $attraction = Attraction::updateOrCreate(
            ['from_user_id' => $user->id, 'to_user_id' => $userId],
            ['type' => 2]
        );

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'type' => $attraction->type
            ]
        ]);
    }

    public function cancel($userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权'], 401);
        }

        Attraction::where('from_user_id', $user->id)
            ->where('to_user_id', $userId)
            ->delete();

        return response()->json([
            'code' => 200,
            'message' => 'success'
        ]);
    }

    public function getStatus($userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权'], 401);
        }

        $attraction = Attraction::where('from_user_id', $user->id)
            ->where('to_user_id', $userId)
            ->first();

        $theirAttraction = Attraction::where('from_user_id', $userId)
            ->where('to_user_id', $user->id)
            ->first();

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'my_type' => $attraction ? $attraction->type : 0,
                'their_type' => $theirAttraction ? $theirAttraction->type : 0,
                'is_mutual' => ($attraction && $attraction->type === 1) && ($theirAttraction && $theirAttraction->type === 1)
            ]
        ]);
    }

    public function getLikes()
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权'], 401);
        }

        $attractions = Attraction::where('from_user_id', $user->id)
            ->where('type', 1)
            ->with('toUser')
            ->get();

        $users = $attractions->map(function ($a) {
            return [
                'id' => $a->toUser->id,
                'name' => $a->toUser->name ?: $a->toUser->nickname,
                'nickname' => $a->toUser->nickname ?: $a->toUser->name,
                'avatar' => $a->toUser->avatar ?: '',
                'gender' => $a->toUser->gender ?: '',
                'age' => $a->toUser->age ?: 0,
                'height' => $a->toUser->height ?: 0,
                'weight' => $a->toUser->weight ?: 0,
                'hobbies' => $a->toUser->hobbies ?: '',
                'love_declaration' => $a->toUser->love_declaration ?: ''
            ];
        });

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $users
        ]);
    }

    public function getMutualLikes()
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权'], 401);
        }

        $myLikes = Attraction::where('from_user_id', $user->id)
            ->where('type', 1)
            ->pluck('to_user_id')
            ->toArray();

        $mutual = Attraction::whereIn('from_user_id', $myLikes)
            ->where('to_user_id', $user->id)
            ->where('type', 1)
            ->with('fromUser')
            ->get();

        $users = $mutual->map(function ($a) {
            return [
                'id' => $a->fromUser->id,
                'name' => $a->fromUser->name ?: $a->fromUser->nickname,
                'nickname' => $a->fromUser->nickname ?: $a->fromUser->name,
                'avatar' => $a->fromUser->avatar ?: '',
                'gender' => $a->fromUser->gender ?: '',
                'age' => $a->fromUser->age ?: 0,
                'height' => $a->fromUser->height ?: 0,
                'weight' => $a->fromUser->weight ?: 0,
                'hobbies' => $a->fromUser->hobbies ?: '',
                'love_declaration' => $a->fromUser->love_declaration ?: ''
            ];
        });

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $users
        ]);
    }
}