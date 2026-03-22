<?php

namespace App\Services;

use App\Models\User;
use App\Models\MembershipPlan;
use App\Models\UserSubscription;
use Carbon\Carbon;

class MembershipService
{
    /**
     * 为用户购买会员（叠加模式）
     */
    public static function purchaseMembership(User $user, MembershipPlan $plan): array
    {
        // 检查金币是否足够
        if ($user->coins < $plan->price) {
            return [
                'success' => false,
                'message' => '金币不足',
                'required' => $plan->price,
                'current' => $user->coins,
            ];
        }
        
        // 扣除金币
        $user->spendCoins($plan->price, "购买{$plan->name}");
        
        // 计算有效期
        $now = Carbon::now();
        
        // 获取当前有效的订阅
        $currentSubscription = UserSubscription::getUserActiveSubscription($user->id);
        
        // 叠加模式：总是在当前最晚到期时间基础上延长
        if ($currentSubscription && $currentSubscription->ends_at > $now) {
            // 有有效会员，在当前到期时间基础上延长
            $startsAt = $currentSubscription->ends_at;
            $endsAt = (clone $startsAt)->addDays($plan->duration_days);
        } else {
            // 没有有效会员，立即开始
            $startsAt = $now;
            $endsAt = (clone $startsAt)->addDays($plan->duration_days);
        }
        
        // 创建订阅记录
        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => 'active',
            'price_paid' => $plan->price,
            'notes' => "plan:{$plan->code},months:1,days:{$plan->duration_days},mode:stacking",
        ]);
        
        return [
            'success' => true,
            'message' => "成功购买{$plan->name}，有效期至 {$endsAt->format('Y-m-d')}",
            'subscription' => $subscription,
            'ends_at' => $endsAt,
        ];
    }
    
    /**
     * 取消会员订阅
     */
    public static function cancelMembership(UserSubscription $subscription): bool
    {
        if ($subscription->status !== 'active') {
            return false;
        }
        
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => Carbon::now(),
        ]);
        
        return true;
    }
    
    /**
     * 检查过期订阅
     */
    public static function checkExpiredSubscriptions(): int
    {
        $now = Carbon::now();
        
        $expiredCount = UserSubscription::where('status', 'active')
            ->where('ends_at', '<', $now)
            ->update(['status' => 'expired']);
        
        return $expiredCount;
    }
    
    /**
     * 获取用户当前会员信息
     */
    public static function getUserMembershipInfo(User $user): array
    {
        $membership = $user->current_membership;
        
        if (!$membership) {
            return [
                'has_membership' => false,
                'plan' => null,
                'ends_at' => null,
                'days_remaining' => null,
            ];
        }
        
        $subscription = $user->subscriptions()
            ->where('plan_id', $membership->id)
            ->where('status', 'active')
            ->where('ends_at', '>', Carbon::now())
            ->first();
        
        $daysRemaining = $subscription ? Carbon::parse($subscription->ends_at)->diffInDays(Carbon::now()) : null;
        
        return [
            'has_membership' => true,
            'plan' => $membership,
            'ends_at' => $subscription->ends_at ?? null,
            'days_remaining' => $daysRemaining,
        ];
    }
    
    /**
     * 活跃度兑换金币（100 活跃度 = 1 金币）
     */
    public static function convertPointsToCoins(User $user, int $points): array
    {
        if ($points < 100) {
            return [
                'success' => false,
                'message' => '最少需要 100 活跃度才能兑换',
            ];
        }
        
        if ($user->points < $points) {
            return [
                'success' => false,
                'message' => '活跃度不足',
            ];
        }
        
        $success = $user->convertPointsToCoins($points);
        
        if ($success) {
            $coins = floor($points / 100);
            return [
                'success' => true,
                'message' => "成功将 {$points} 活跃度兑换为 {$coins} 金币",
                'points_used' => $points,
                'coins_received' => $coins,
            ];
        }
        
        return [
            'success' => false,
            'message' => '兑换失败',
        ];
    }
    
    /**
     * 获取所有可用的会员计划
     */
    public static function getAvailablePlans(): array
    {
        $plans = MembershipPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        return $plans->map(function ($plan) {
            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'code' => $plan->code,
                'price' => $plan->price,
                'price_yuan' => $plan->price / 10, // 转换为人民币
                'duration_days' => $plan->duration_days,
                'privileges' => $plan->privileges,
                'icon' => $plan->icon,
                'badge_color' => $plan->badge_color,
            ];
        })->toArray();
    }
}
