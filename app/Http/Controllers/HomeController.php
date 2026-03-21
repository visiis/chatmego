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
        
        return view('users', compact('users'));
    }
}
