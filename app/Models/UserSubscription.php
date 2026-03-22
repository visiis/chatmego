<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'starts_at',
        'ends_at',
        'status',
        'price_paid',
        'cancelled_at',
        'notes',
    ];
    
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'price_paid' => 'integer',
    ];
    
    /**
     * 获取会员计划
     */
    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class);
    }
    
    /**
     * 获取用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * 检查订阅是否有效
     */
    public function isValid()
    {
        return $this->status === 'active' 
            && $this->ends_at > now();
    }
    
    /**
     * 获取用户的当前有效订阅
     */
    public static function getUserActiveSubscription($userId)
    {
        return static::where('user_id', $userId)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->orderBy('ends_at', 'desc')
            ->first();
    }
    
    /**
     * 获取用户当前订阅的会员计划
     */
    public static function getUserCurrentPlan($userId)
    {
        $subscription = static::getUserActiveSubscription($userId);
        return $subscription ? $subscription->plan : null;
    }
}
