<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MembershipPlan;
use App\Models\UserSubscription;
use App\Services\MembershipService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MembershipApiController extends Controller
{
    protected function getUserFromToken(Request $request): ?User
    {
        $token = $request->header('Authorization');
        if ($token && str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }
        
        if (!$token) {
            return null;
        }
        
        return User::where('api_token', $token)->first();
    }

    public function index(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权']);
        }

        $membershipInfo = MembershipService::getUserMembershipInfo($user);
        $availablePlans = MembershipService::getAvailablePlans();
        
        $subscriptionHistory = $user->subscriptions()
            ->with('plan')
            ->orderBy('starts_at', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($subscription) {
                return [
                    'id' => $subscription->id,
                    'plan_id' => $subscription->plan_id,
                    'plan_name' => $subscription->plan->name,
                    'plan_code' => $subscription->plan->code,
                    'plan_icon' => $subscription->plan->icon,
                    'plan_badge_color' => $subscription->plan->badge_color,
                    'starts_at' => $subscription->starts_at->toISOString(),
                    'ends_at' => $subscription->ends_at->toISOString(),
                    'status' => $subscription->status,
                    'price_paid' => $subscription->price_paid,
                    'notes' => $subscription->notes,
                ];
            })
            ->toArray();

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'user' => [
                    'coins' => $user->coins,
                    'points' => $user->points,
                ],
                'membership_info' => $membershipInfo,
                'available_plans' => $availablePlans,
                'subscription_history' => $subscriptionHistory,
            ]
        ]);
    }

    public function purchase(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权']);
        }

        $planId = $request->input('plan_id');
        $months = (int) $request->input('months', 1);
        
        if (!in_array($months, [1, 3, 6, 12])) {
            return response()->json(['code' => 400, 'message' => '无效的购买时长']);
        }
        
        $plan = MembershipPlan::find($planId);
        
        if (!$plan || !$plan->is_active) {
            return response()->json(['code' => 400, 'message' => '会员计划不存在或已停用']);
        }
        
        $totalPrice = $plan->price * $months;
        $totalDays = $plan->duration_days * $months;
        
        if ($user->coins < $totalPrice) {
            return response()->json([
                'code' => 400, 
                'message' => '金币不足',
                'required_coins' => $totalPrice,
                'current_coins' => $user->coins,
            ]);
        }
        
        $user->spendCoins($totalPrice, "购买{$plan->name}，{$months}个月");
        
        $now = Carbon::now();
        
        $allActiveSubscriptions = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('ends_at', '>', $now)
            ->orderBy('ends_at', 'desc')
            ->get();
        
        if ($allActiveSubscriptions->count() > 0) {
            $latestEndsAt = $allActiveSubscriptions->max('ends_at');
            $startsAt = $latestEndsAt;
            $endsAt = (clone $startsAt)->addDays($totalDays);
        } else {
            $startsAt = $now;
            $endsAt = (clone $startsAt)->addDays($totalDays);
        }
        
        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => 'active',
            'price_paid' => $totalPrice,
            'notes' => json_encode([
                'plan_code' => $plan->code,
                'months' => $months,
                'days' => $totalDays,
            ]),
        ]);
        
        return response()->json([
            'code' => 200,
            'message' => "成功购买{$plan->name}，{$months}个月，有效期至 {$endsAt->format('Y-m-d')}",
            'data' => [
                'subscription' => $subscription,
                'ends_at' => $endsAt->format('Y-m-d'),
            ]
        ]);
    }

    public function convertPoints(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权']);
        }

        $points = $request->input('points', 100);
        
        if ($points % 100 !== 0) {
            return response()->json(['code' => 400, 'message' => '兑换的活跃度必须是 100 的倍数']);
        }
        
        $result = MembershipService::convertPointsToCoins($user, $points);
        
        if ($result['success']) {
            return response()->json([
                'code' => 200,
                'message' => $result['message'],
                'data' => $result,
            ]);
        }
        
        return response()->json([
            'code' => 400,
            'message' => $result['message'],
        ]);
    }

    public function cancel(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权']);
        }

        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->first();
        
        if (!$subscription) {
            return response()->json(['code' => 400, 'message' => '没有有效的订阅']);
        }
        
        $success = MembershipService::cancelMembership($subscription);
        
        if ($success) {
            return response()->json([
                'code' => 200,
                'message' => '已取消会员订阅，会员权益将持续到 ' . $subscription->ends_at->format('Y-m-d'),
            ]);
        }
        
        return response()->json(['code' => 400, 'message' => '取消失败']);
    }
}
