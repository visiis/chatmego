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
        // 获取已是好友的用户 ID 列表（包括 pending 和 accepted 状态）
        $authUserId = auth()->id();
        
        // 获取所有相关的好友关系 ID（包括自己和对方）
        $friendIds = \DB::table('friendships')
            ->where('user_id', $authUserId)
            ->whereIn('status', ['pending', 'accepted'])
            ->pluck('friend_id')
            ->merge(
                \DB::table('friendships')
                    ->where('friend_id', $authUserId)
                    ->whereIn('status', ['pending', 'accepted'])
                    ->pluck('user_id')
            )
            ->unique()
            ->toArray();
        
        // 获取非好友用户（排除自己和已是好友/已发送请求的用户）
        $users = User::where('id', '!=', $authUserId)
                    ->whereNotIn('id', $friendIds)
                    ->get();
        
        return view('users', compact('users'));
    }
}
