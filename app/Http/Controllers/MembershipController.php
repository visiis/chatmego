<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MembershipPlan;
use App\Services\MembershipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{
    /**
     * 显示会员中心页面
     */
    public function index()
    {
        $user = Auth::user();
        $membershipInfo = MembershipService::getUserMembershipInfo($user);
        $availablePlans = MembershipService::getAvailablePlans();
        
        return view('membership.index', compact('user', 'membershipInfo', 'availablePlans'));
    }
    
    /**
     * 购买会员
     */
    public function purchase(Request $request)
    {
        $user = Auth::user();
        $planId = $request->input('plan_id');
        
        $plan = MembershipPlan::find($planId);
        
        if (!$plan || !$plan->is_active) {
            return redirect()->back()
                ->with('error', '会员计划不存在或已停用');
        }
        
        $result = MembershipService::purchaseMembership($user, $plan);
        
        if ($result['success']) {
            return redirect()->back()
                ->with('success', $result['message'])
                ->with('ends_at', $result['ends_at']);
        }
        
        return redirect()->back()
            ->with('error', $result['message'])
            ->with('required_coins', $result['required'] ?? null)
            ->with('current_coins', $result['current'] ?? null);
    }
    
    /**
     * 活跃度兑换金币
     */
    public function convertPoints(Request $request)
    {
        $user = Auth::user();
        $points = $request->input('points', 100);
        
        // 必须是 100 的倍数
        if ($points % 100 !== 0) {
            return redirect()->back()
                ->with('error', '兑换的活跃度必须是 100 的倍数');
        }
        
        $result = MembershipService::convertPointsToCoins($user, $points);
        
        if ($result['success']) {
            return redirect()->back()
                ->with('success', $result['message']);
        }
        
        return redirect()->back()
            ->with('error', $result['message']);
    }
    
    /**
     * 取消会员订阅
     */
    public function cancel(Request $request)
    {
        $user = Auth::user();
        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->first();
        
        if (!$subscription) {
            return redirect()->back()
                ->with('error', '没有有效的订阅');
        }
        
        $success = MembershipService::cancelMembership($subscription);
        
        if ($success) {
            return redirect()->back()
                ->with('success', '已取消会员订阅，会员权益将持续到 ' . $subscription->ends_at->format('Y-m-d'));
        }
        
        return redirect()->back()
            ->with('error', '取消失败');
    }
}
