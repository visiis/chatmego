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
     * 获取翻译后的备注
     */
    public function getTranslatedNotesAttribute()
    {
        // 尝试解析 JSON 格式的 notes
        $notesData = json_decode($this->notes, true);
        
        if (is_array($notesData) && isset($notesData['plan_code'])) {
            // 获取当前语言环境
            $locale = app()->getLocale();
            
            // 获取会员计划的翻译名称（指定语言）
            $planName = trans("messages.membership.plans.{$notesData['plan_code']}", [], $locale);
            
            return trans('messages.membership.purchase_note', [
                'plan' => $planName,
                'months' => $notesData['months'],
                'days' => $notesData['days'],
            ], $locale);
        }
        
        // 如果不是 JSON 格式，返回原始 notes（兼容旧数据）
        return $this->notes;
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
     * 获取用户的当前有效订阅（按到期时间升序，返回最早到期的）
     */
    public static function getUserActiveSubscription($userId)
    {
        return static::where('user_id', $userId)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->orderBy('ends_at', 'asc')  // 升序：最早到期的在前
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
