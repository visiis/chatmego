<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MembershipPlan;
use App\Models\UserSubscription;
use App\Services\MembershipService;
use Carbon\Carbon;
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
        
        // 获取用户的订阅历史（按开始时间倒序）
        $subscriptionHistory = $user->subscriptions()
            ->with('plan')
            ->orderBy('starts_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('membership.index', compact('user', 'membershipInfo', 'availablePlans', 'subscriptionHistory'));
    }
    
    /**
     * 购买会员
     */
    public function purchase(Request $request)
    {
        $user = Auth::user();
        $planId = $request->input('plan_id');
        $months = (int) $request->input('months', 1);
        
        // 验证月数
        if (!in_array($months, [1, 3, 6, 12])) {
            return redirect()->back()
                ->with('error', '无效的购买时长');
        }
        
        $plan = MembershipPlan::find($planId);
        
        if (!$plan || !$plan->is_active) {
            return redirect()->back()
                ->with('error', '会员计划不存在或已停用');
        }
        
        // 计算总价格和总天数
        $totalPrice = $plan->price * $months;
        $totalDays = $plan->duration_days * $months;
        
        // 检查金币是否足够
        if ($user->coins < $totalPrice) {
            return redirect()->back()
                ->with('error', '金币不足')
                ->with('required_coins', $totalPrice)
                ->with('current_coins', $user->coins);
        }
        
        // 扣除金币
        $user->spendCoins($totalPrice, "购买{$plan->name}，{$months}个月");
        
        // 计算有效期（叠加模式）
        $now = Carbon::now();
        $currentSubscription = UserSubscription::getUserActiveSubscription($user->id);
        
        // 叠加模式：总是在当前最晚到期时间基础上延长
        if ($currentSubscription && $currentSubscription->ends_at > $now) {
            // 有有效会员，在当前到期时间基础上延长
            $startsAt = $currentSubscription->ends_at;
            $endsAt = (clone $startsAt)->addDays($totalDays);
        } else {
            // 没有有效会员，立即开始
            $startsAt = $now;
            $endsAt = (clone $startsAt)->addDays($totalDays);
        }
        
        // 创建订阅记录
        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => 'active',
            'price_paid' => $totalPrice,
            'notes' => "购买{$plan->name}，{$months}个月，{$totalDays}天，叠加模式",
        ]);
        
        return redirect()->back()
            ->with('success', "成功购买{$plan->name}，{$months}个月，有效期至 {$endsAt->format('Y-m-d')}")
            ->with('ends_at', $endsAt);
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
