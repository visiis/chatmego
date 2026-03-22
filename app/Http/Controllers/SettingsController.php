<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('settings');
    }

    public function update(Request $request)
    {
        $request->validate([
            'language' => 'required|in:zh_TW,zh_CN,en,ja,ko',
        ]);

        session()->put('locale', $request->language);
        
        // 立即设置当前请求的语言环境
        app()->setLocale($request->language);

        return redirect()->back()->with('success', __('messages.settings.success'));
    }
}