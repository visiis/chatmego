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
     * Show the users list with search and filter options.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
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
        
        // 获取筛选条件
        $search = $request->input('search');
        $gender = $request->input('gender');
        $minAge = $request->input('min_age');
        $maxAge = $request->input('max_age');
        $minHeight = $request->input('min_height');
        $maxHeight = $request->input('max_height');
        $memberLevel = $request->input('member_level');
        
        // 获取用户列表（排除自己和已是好友的用户）
        $users = User::where('id', '!=', $authUserId)
                    ->whereNotIn('id', $friendIds);
        
        // 搜索关键词筛选（用户名、个性签名）
        if (!empty($search)) {
            $users->where(function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('love_declaration', 'like', '%' . $search . '%');
            });
        }
        
        // 性别筛选
        if (!empty($gender)) {
            $users->where('gender', $gender);
        }
        
        // 年龄范围筛选
        if (!empty($minAge)) {
            $users->where('age', '>=', $minAge);
        }
        if (!empty($maxAge)) {
            $users->where('age', '<=', $maxAge);
        }
        
        // 身高范围筛选
        if (!empty($minHeight)) {
            $users->where('height', '>=', $minHeight);
        }
        if (!empty($maxHeight)) {
            $users->where('height', '<=', $maxHeight);
        }
        
        // 会员等级筛选
        if (!empty($memberLevel)) {
            $users->where('member_level', $memberLevel);
        }
        
        $users = $users->get();
        
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
            } else {
                // 没有好友关系，用于调试
                $user->friendship_status = null;
            }
        }
        
        // 调试：输出当前用户 ID
        \Log::info('Home page loaded', [
            'auth_user_id' => $authUserId,
            'users_count' => $users->count(),
            'users_with_status' => $users->filter(function($u) {
                return $u->friendship_status !== null;
            })->map(function($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'status' => $u->friendship_status,
                    'requester_id' => $u->requester_id,
                ];
            })->values()->toArray()
        ]);
        
        return view('users', compact('users', 'search', 'gender', 'minAge', 'maxAge', 'minHeight', 'maxHeight', 'memberLevel'));
    }
}
