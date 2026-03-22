<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipPlan extends Model
{
    protected $fillable = [
        'name',
        'code',
        'price',
        'duration_days',
        'privileges',
        'icon',
        'badge_color',
        'is_active',
        'sort_order',
    ];
    
    protected $casts = [
        'price' => 'integer',
        'duration_days' => 'integer',
        'privileges' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
    
    /**
     * 获取所有启用的会员计划
     */
    public static function getActivePlans()
    {
        return static::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();
    }
    
    /**
     * 获取指定代码的会员计划
     */
    public static function getPlanByCode($code)
    {
        return static::where('code', $code)
            ->where('is_active', true)
            ->first();
    }
    
    /**
     * 获取用户订阅关系
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }
    
    /**
     * 获取当前订阅该计划的用户数
     */
    public function getActiveSubscribersCountAttribute()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->count();
    }
}
