<?php

namespace App\Http\Controllers;

use App\Models\User;

class AccountPendingController extends Controller
{
    /**
     * 构造函数，需要认证
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 显示账户待激活页面
     */
    public function index()
    {
        // 获取所有管理员
        $admins = User::where('is_admin', 1)
                     ->where('is_active', 1)
                     ->get();

        return view('account-pending', compact('admins'));
    }
}
