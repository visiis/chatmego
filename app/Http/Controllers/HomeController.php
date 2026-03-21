<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the users list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $authUserId = auth()->id();
        
        // 获取已是好友的用户 ID（只包括 accepted 状态）
        $friendIds = \DB::table('friendships')
            ->where('user_id', $authUserId)
            ->where('status', 'accepted')
            ->pluck('friend_id')
            ->merge(
                \DB::table('friendships')
                    ->where('friend_id', $authUserId)
                    ->where('status', 'accepted')
                    ->pluck('user_id')
            )
            ->unique()
            ->toArray();
        
        // 获取用户列表（排除自己和已是好友的用户）
        $users = User::where('id', '!=', $authUserId)
                    ->whereNotIn('id', $friendIds)
                    ->get();
        
        // 为每个用户添加好友状态信息
        foreach ($users as $user) {
            // 查询当前用户与该用户之间的好友关系
            $friendship = \DB::table('friendships')
                ->where(function($query) use ($authUserId, $user) {
                    // 情况 1：当前用户是发起者
                    $query->where('user_id', $authUserId)
                          ->where('friend_id', $user->id);
                })
                ->orWhere(function($query) use ($authUserId, $user) {
                    // 情况 2：对方是发起者
                    $query->where('user_id', $user->id)
                          ->where('friend_id', $authUserId);
                })
                ->whereIn('status', ['pending', 'accepted'])
                ->first();
            
            if ($friendship) {
                $user->friendship_status = $friendship->status;
                // requester_id 是发起好友请求的人的 ID
                $user->requester_id = $friendship->user_id;
            }
        }
        
        return view('users', compact('users'));
    }
}
