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
        $friendIds = \DB::table('friendships')
            ->where(function($query) {
                $query->where('user_id', auth()->id())
                      ->orWhere('friend_id', auth()->id());
            })
            ->whereIn('status', ['pending', 'accepted'])
            ->pluck('user_id')
            ->merge(
                \DB::table('friendships')
                    ->where(function($query) {
                        $query->where('user_id', auth()->id())
                              ->orWhere('friend_id', auth()->id());
                    })
                    ->whereIn('status', ['pending', 'accepted'])
                    ->pluck('friend_id')
            )
            ->unique()
            ->toArray();
        
        // 获取非好友用户（排除自己和已是好友/已发送请求的用户）
        $users = User::where('id', '!=', auth()->id())
                    ->whereNotIn('id', $friendIds)
                    ->get();
        
        return view('users', compact('users'));
    }
}
