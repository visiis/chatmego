<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftRedemption;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RedemptionController extends Controller
{
    /**
     * 显示兑换申请列表
     */
    public function index()
    {
        $user = auth()->user();
        
        // 只允许管理员访问
        if (!$user->is_admin) {
            abort(403, '无权访问');
        }
        
        $redemptions = GiftRedemption::with(['user', 'gift'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.redemptions.index', compact('redemptions'));
    }
    
    /**
     * 查看兑换申请详情
     */
    public function show(GiftRedemption $redemption)
    {
        $user = auth()->user();
        
        if (!$user->is_admin) {
            abort(403, '无权访问');
        }
        
        return view('admin.redemptions.show', compact('redemption'));
    }
    
    /**
     * 更新兑换申请状态
     */
    public function updateStatus(Request $request, GiftRedemption $redemption)
    {
        $user = auth()->user();
        
        if (!$user->is_admin) {
            abort(403, '无权访问');
        }
        
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled',
            'admin_notes' => 'nullable|string|max:1000',
        ]);
        
        $redemption->status = $validated['status'];
        
        if (isset($validated['admin_notes'])) {
            $redemption->admin_notes = $validated['admin_notes'];
        }
        
        $redemption->save();
        
        return back()->with('success', '状态已更新');
    }
}
