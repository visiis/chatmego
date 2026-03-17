<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Friendship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FriendshipController extends Controller
{
    /**
     * 构造函数，需要认证
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 发送好友请求
     */
    public function sendRequest(User $user)
    {
        $authUser = auth()->user();

        // 不能添加自己为好友
        if ($authUser->id === $user->id) {
            return redirect()->back()->with('error', '不能添加自己为好友');
        }

        // 检查是否已经是好友
        $existingFriendship = Friendship::where(function ($query) use ($authUser, $user) {
            $query->where('user_id', $authUser->id)
                  ->where('friend_id', $user->id)
                  ->orWhere('user_id', $user->id)
                  ->where('friend_id', $authUser->id);
        })->first();

        if ($existingFriendship) {
            if ($existingFriendship->status === 'accepted') {
                return redirect()->back()->with('error', '已经是好友了');
            } elseif ($existingFriendship->status === 'pending') {
                return redirect()->back()->with('error', '好友请求已发送，等待对方接受');
            } elseif ($existingFriendship->status === 'blocked') {
                return redirect()->back()->with('error', '无法添加该用户为好友');
            }
        }

        // 创建好友请求
        Friendship::create([
            'user_id' => $authUser->id,
            'friend_id' => $user->id,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', '好友请求已发送');
    }

    /**
     * 接受好友请求
     */
    public function acceptRequest(Friendship $friendship)
    {
        $authUser = auth()->user();

        // 只有接收者才能接受请求
        if ($friendship->friend_id !== $authUser->id) {
            return redirect()->back()->with('error', '无权操作');
        }

        if ($friendship->status !== 'pending') {
            return redirect()->back()->with('error', '好友请求已处理');
        }

        $friendship->accept();

        return redirect()->back()->with('success', '已接受好友请求');
    }

    /**
     * 拒绝好友请求
     */
    public function rejectRequest(Friendship $friendship)
    {
        $authUser = auth()->user();

        // 只有接收者才能拒绝请求
        if ($friendship->friend_id !== $authUser->id) {
            return redirect()->back()->with('error', '无权操作');
        }

        if ($friendship->status !== 'pending') {
            return redirect()->back()->with('error', '好友请求已处理');
        }

        $friendship->reject();

        return redirect()->back()->with('success', '已拒绝好友请求');
    }

    /**
     * 删除好友
     */
    public function removeFriend(User $user)
    {
        $authUser = auth()->user();

        $friendship = Friendship::where(function ($query) use ($authUser, $user) {
            $query->where('user_id', $authUser->id)
                  ->where('friend_id', $user->id)
                  ->orWhere('user_id', $user->id)
                  ->where('friend_id', $authUser->id);
        })->where('status', 'accepted')->first();

        if (!$friendship) {
            return redirect()->back()->with('error', '不是好友关系');
        }

        $friendship->delete();

        return redirect()->back()->with('success', '已删除好友');
    }

    /**
     * 我的好友列表
     */
    public function myFriends()
    {
        $authUser = auth()->user();

        // 获取所有已接受的好友关系
        $friendships = Friendship::with(['user', 'friend'])
            ->where(function ($query) use ($authUser) {
                $query->where('user_id', $authUser->id)
                      ->orWhere('friend_id', $authUser->id);
            })
            ->where('status', 'accepted')
            ->get();

        // 获取好友用户
        $friends = $friendships->map(function ($friendship) use ($authUser) {
            return $friendship->user_id == $authUser->id 
                ? $friendship->friend 
                : $friendship->user;
        })->filter();

        return view('friends', compact('friends'));
    }

    /**
     * 待处理的好友请求
     */
    public function pendingRequests()
    {
        $authUser = auth()->user();

        $requests = Friendship::where('friend_id', $authUser->id)
            ->where('status', 'pending')
            ->with('user')
            ->get();

        return view('friend-requests', compact('requests'));
    }
}
