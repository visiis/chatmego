<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Friendship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * 构造函数，需要认证
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 聊天列表（最近聊天）
     */
    public function index()
    {
        $authUser = auth()->user();

        // 获取好友列表（用于显示聊天对象）
        $friendships = Friendship::with(['user', 'friend'])
            ->where(function ($query) use ($authUser) {
                $query->where('user_id', $authUser->id)
                      ->orWhere('friend_id', $authUser->id);
            })
            ->where('status', 'accepted')
            ->get();

        $friends = $friendships->map(function ($friendship) use ($authUser) {
            return $friendship->user_id == $authUser->id 
                ? $friendship->friend 
                : $friendship->user;
        })->filter();

        // 获取每个好友的最近一条消息
        $chatList = [];
        foreach ($friends as $friend) {
            $lastMessage = Message::where(function ($query) use ($authUser, $friend) {
                $query->where('from_user_id', $authUser->id)
                      ->where('to_user_id', $friend->id);
            })->orWhere(function ($query) use ($authUser, $friend) {
                $query->where('from_user_id', $friend->id)
                      ->where('to_user_id', $authUser->id);
            })
            ->orderBy('created_at', 'desc')
            ->first();

            // 计算未读消息数
            $unreadCount = Message::where('from_user_id', $friend->id)
                ->where('to_user_id', $authUser->id)
                ->where('is_read', false)
                ->count();

            $chatList[] = [
                'friend' => $friend,
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount,
            ];
        }

        // 按最后消息时间排序
        usort($chatList, function ($a, $b) {
            $aTime = $a['last_message'] ? $a['last_message']->created_at->timestamp : 0;
            $bTime = $b['last_message'] ? $b['last_message']->created_at->timestamp : 0;
            return $bTime - $aTime;
        });

        return view('chats', compact('chatList'));
    }

    /**
     * 聊天详情
     */
    public function show(User $user)
    {
        $authUser = auth()->user();

        // 获取聊天记录（只获取最近 50 条）
        $messages = Message::where(function ($query) use ($authUser, $user) {
            $query->where('from_user_id', $authUser->id)
                  ->where('to_user_id', $user->id);
        })->orWhere(function ($query) use ($authUser, $user) {
            $query->where('from_user_id', $user->id)
                  ->where('to_user_id', $authUser->id);
        })
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get()
        ->reverse(); // 反转回正序显示

        // 标记所有来自对方的消息为已读
        Message::where('from_user_id', $user->id)
            ->where('to_user_id', $authUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('chat-detail', compact('user', 'messages'));
    }

    /**
     * 发送消息
     */
    public function sendMessage(Request $request, User $user)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'type' => 'required|in:text,image,voice,video,gift,emoji',
            'attachment_url' => 'nullable|url|max:500',
        ]);

        $authUser = auth()->user();

        $message = Message::create([
            'from_user_id' => $authUser->id,
            'to_user_id' => $user->id,
            'message' => $request->message,
            'type' => $request->type,
            'attachment_url' => $request->attachment_url,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * 标记消息为已读
     */
    public function markAsRead(User $user)
    {
        $authUser = auth()->user();

        Message::where('from_user_id', $user->id)
            ->where('to_user_id', $authUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * 获取新消息（轮询用）
     */
    public function fetchMessages(Request $request, User $user)
    {
        $authUser = auth()->user();
        $lastMessageId = $request->get('last_message_id', 0);

        // 获取最后一条消息之后的新消息
        $messages = Message::where(function ($query) use ($authUser, $user) {
                $query->where('from_user_id', $authUser->id)
                      ->where('to_user_id', $user->id);
            })->orWhere(function ($query) use ($authUser, $user) {
                $query->where('from_user_id', $user->id)
                      ->where('to_user_id', $authUser->id);
            })
            ->where('id', '>', $lastMessageId)
            ->orderBy('created_at', 'asc')
            ->get();

        // 标记来自对方的消息为已读
        Message::where('from_user_id', $user->id)
            ->where('to_user_id', $authUser->id)
            ->where('is_read', false)
            ->where('id', '>', $lastMessageId)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * 加载更多历史消息
     */
    public function loadHistory(Request $request, User $user)
    {
        $authUser = auth()->user();
        $beforeId = $request->get('before_id'); // 获取此 ID 之前的消息
        $limit = $request->get('limit', 50); // 默认每次加载 50 条

        // 获取历史消息
        $messages = Message::where(function ($query) use ($authUser, $user) {
                $query->where('from_user_id', $authUser->id)
                      ->where('to_user_id', $user->id);
            })->orWhere(function ($query) use ($authUser, $user) {
                $query->where('from_user_id', $user->id)
                      ->where('to_user_id', $authUser->id);
            })
            ->where('id', '<', $beforeId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse();

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'has_more' => $messages->count() >= $limit,
        ]);
    }
}
