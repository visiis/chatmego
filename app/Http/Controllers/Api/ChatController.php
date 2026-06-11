<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * 获取对话列表
     */
    public function conversations()
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        // 获取匹配用户作为对话列表
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
                'last_message' => '来聊聊吧',
                'unread_count' => 0,
                'last_time' => '刚刚'
            ];
        });

        return response()->json([
            'message' => 'success',
            'data' => $result
        ]);
    }

    /**
     * 获取与某用户的消息
     */
    public function messages($userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        // 模拟消息列表
        $targetUser = User::find($userId);
        
        if (!$targetUser) {
            return response()->json(['message' => '用户不存在'], 404);
        }

        $messages = [
            [
                'id' => 1,
                'from_id' => $userId,
                'to_id' => $user->id,
                'content' => '你好呀~',
                'type' => 'text',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 minutes'))
            ],
            [
                'id' => 2,
                'from_id' => $user->id,
                'to_id' => $userId,
                'content' => '你好！很高兴认识你',
                'type' => 'text',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 minutes'))
            ],
            [
                'id' => 3,
                'from_id' => $userId,
                'to_id' => $user->id,
                'content' => '😊',
                'type' => 'text',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 minutes'))
            ]
        ];

        return response()->json([
            'message' => 'success',
            'data' => [
                'user' => [
                    'id' => $targetUser->id,
                    'name' => $targetUser->name,
                    'avatar' => $targetUser->avatar_url
                ],
                'messages' => $messages
            ]
        ]);
    }

    /**
     * 发送消息
     */
    public function send(Request $request, $userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $targetUser = User::find($userId);
        
        if (!$targetUser) {
            return response()->json(['message' => '用户不存在'], 404);
        }

        $content = $request->input('content');
        $type = $request->input('type', 'text');

        if (!$content) {
            return response()->json(['message' => '消息内容不能为空'], 400);
        }

        // 模拟发送消息
        $message = [
            'id' => rand(1000, 9999),
            'from_id' => $user->id,
            'to_id' => $userId,
            'content' => $content,
            'type' => $type,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return response()->json([
            'message' => '发送成功',
            'data' => $message
        ]);
    }
}
