<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Message;
use App\Models\Friendship;
use App\Models\Gift;
use App\Models\UserGift;
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
            return response()->json(['code' => 401, 'message' => '未授权'], 401);
        }

        $friendships = Friendship::with(['user', 'friend'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('friend_id', $user->id);
            })
            ->where('status', 'accepted')
            ->get();

        $friends = $friendships->map(function ($friendship) use ($user) {
            return $friendship->user_id == $user->id 
                ? $friendship->friend 
                : $friendship->user;
        })->filter();

        $chatList = [];
        foreach ($friends as $friend) {
            $lastMessage = Message::where(function ($query) use ($user, $friend) {
                $query->where('from_user_id', $user->id)
                      ->where('to_user_id', $friend->id);
            })->orWhere(function ($query) use ($user, $friend) {
                $query->where('from_user_id', $friend->id)
                      ->where('to_user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->first();

            $unreadCount = Message::where('from_user_id', $friend->id)
                ->where('to_user_id', $user->id)
                ->where('is_read', false)
                ->count();

            $chatList[] = [
                'id' => $friend->id,
                'friend' => [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'nickname' => $friend->name,
                    'avatar' => $friend->avatar_url,
                    'is_online' => false,
                ],
                'last_message' => $lastMessage ? [
                    'id' => $lastMessage->id,
                    'content' => $lastMessage->message,
                    'type' => $lastMessage->type,
                    'created_at' => $lastMessage->created_at->toISOString(),
                ] : null,
                'unread_count' => $unreadCount,
            ];
        }

        usort($chatList, function ($a, $b) {
            $aTime = $a['last_message'] ? strtotime($a['last_message']['created_at']) : 0;
            $bTime = $b['last_message'] ? strtotime($b['last_message']['created_at']) : 0;
            return $bTime - $aTime;
        });

        return response()->json([
            'success' => true,
            'data' => $chatList
        ]);
    }

    /**
     * 获取与某用户的消息
     */
    public function messages($userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => '未授权'], 401);
        }

        $targetUser = User::find($userId);
        
        if (!$targetUser) {
            return response()->json(['success' => false, 'message' => '用户不存在'], 404);
        }

        $messages = Message::where(function ($query) use ($user, $targetUser) {
            $query->where('from_user_id', $user->id)
                  ->where('to_user_id', $targetUser->id);
        })->orWhere(function ($query) use ($user, $targetUser) {
            $query->where('from_user_id', $targetUser->id)
                  ->where('to_user_id', $user->id);
        })
        ->orderBy('created_at', 'desc')
        ->limit(20)
        ->get()
        ->reverse();

        Message::where('from_user_id', $targetUser->id)
            ->where('to_user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'messages' => $messages->values()->toArray(),
            ],
        ]);
    }

    /**
     * 发送消息
     */
    public function send(Request $request, $userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => '未授权'], 401);
        }

        $targetUser = User::find($userId);
        
        if (!$targetUser) {
            return response()->json(['success' => false, 'message' => '用户不存在'], 404);
        }

        $content = $request->input('message', $request->input('content'));
        $type = $request->input('type', 'text');

        if (!$content) {
            return response()->json(['success' => false, 'message' => '消息内容不能为空'], 400);
        }

        $message = Message::create([
            'from_user_id' => $user->id,
            'to_user_id' => $userId,
            'message' => $content,
            'type' => $type,
            'attachment_url' => $request->input('attachment_url'),
        ]);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'message' => $message,
            ],
        ]);
    }

    /**
     * 获取新消息（轮询用）
     */
    public function fetchMessages(Request $request, $userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => '未授权'], 401);
        }

        $targetUser = User::find($userId);
        
        if (!$targetUser) {
            return response()->json(['success' => false, 'message' => '用户不存在'], 404);
        }

        $lastMessageId = $request->get('last_message_id', 0);

        $messages = Message::where(function ($query) use ($user, $targetUser) {
                $query->where('from_user_id', $user->id)
                      ->where('to_user_id', $targetUser->id);
            })->orWhere(function ($query) use ($user, $targetUser) {
                $query->where('from_user_id', $targetUser->id)
                      ->where('to_user_id', $user->id);
            })
            ->where('id', '>', $lastMessageId)
            ->orderBy('created_at', 'asc')
            ->get();

        Message::where('from_user_id', $targetUser->id)
            ->where('to_user_id', $user->id)
            ->where('is_read', false)
            ->where('id', '>', $lastMessageId)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'messages' => $messages,
            ],
        ]);
    }

    /**
     * 加载更多历史消息
     */
    public function loadHistory(Request $request, $userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => '未授权'], 401);
        }

        $targetUser = User::find($userId);
        
        if (!$targetUser) {
            return response()->json(['success' => false, 'message' => '用户不存在'], 404);
        }

        $beforeId = $request->get('before_id');
        $limit = $request->get('limit', 50);

        $messages = Message::where(function ($query) use ($user, $targetUser) {
                $query->where('from_user_id', $user->id)
                      ->where('to_user_id', $targetUser->id);
            })->orWhere(function ($query) use ($user, $targetUser) {
                $query->where('from_user_id', $targetUser->id)
                      ->where('to_user_id', $user->id);
            })
            ->where('id', '<', $beforeId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse();

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'messages' => $messages->values()->toArray(),
            ],
            'has_more' => $messages->count() >= $limit,
        ]);
    }

    /**
     * 获取礼物列表
     */
    public function getUserGifts()
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => '未授权'], 401);
        }

        $gifts = Gift::where('is_active', true)
            ->orderBy('type', 'asc')
            ->orderBy('price', 'asc')
            ->get();

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'gifts' => $gifts,
                'user' => [
                    'points' => $user->points,
                    'coins' => $user->coins,
                ],
            ],
        ]);
    }

    /**
     * 购买并发送礼物
     */
    public function sendGift(Request $request, $userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => '未授权'], 401);
        }

        $targetUser = User::find($userId);
        
        if (!$targetUser) {
            return response()->json(['success' => false, 'message' => '用户不存在'], 404);
        }

        $request->validate([
            'gift_id' => 'required|exists:gifts,id',
        ]);

        $gift = Gift::find($request->gift_id);

        if (!$gift->is_active) {
            return response()->json([
                'success' => false,
                'message' => '礼物不可用',
            ]);
        }

        DB::beginTransaction();
        try {
            if ($gift->price_type === 'activity_points') {
                if ($user->points < $gift->price) {
                    return response()->json([
                        'success' => false,
                        'message' => '活跃度不足',
                    ]);
                }
                $user->points -= $gift->price;
            } else {
                if ($user->coins < $gift->price) {
                    return response()->json([
                        'success' => false,
                        'message' => '金币不足',
                    ]);
                }
                $user->coins -= $gift->price;
            }

            $user->save();

            $message = Message::create([
                'from_user_id' => $user->id,
                'to_user_id' => $userId,
                'message' => json_encode([
                    'gift_id' => $gift->id,
                    'gift_name' => $gift->name,
                    'gift_image' => $gift->image,
                ]),
                'type' => 'gift',
                'attachment_url' => $gift->image,
            ]);

            $existingUserGift = UserGift::where('user_id', $userId)
                ->where('gift_id', $gift->id)
                ->where('is_redeemed', false)
                ->first();

            if ($existingUserGift) {
                $existingUserGift->increment('quantity');
            } else {
                UserGift::create([
                    'user_id' => $userId,
                    'gift_id' => $gift->id,
                    'quantity' => 1,
                    'is_redeemed' => false,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '发送失败',
            ], 500);
        }
    }
}
