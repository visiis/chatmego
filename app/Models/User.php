<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'gender',
        'age',
        'height',
        'weight',
        'hobbies',
        'specialty',
        'love_declaration',
        'points',
        'total_points_earned',
        'coins',
        'total_coins_spent',
        'total_coins_recharged',
        'is_active',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'points' => 'integer',
            'total_points_earned' => 'integer',
            'coins' => 'integer',
            'total_coins_spent' => 'integer',
            'total_coins_recharged' => 'integer',
        ];
    }
    
    /**
     * 获取用户的会员等级
     */
    public function memberLevel()
    {
        return $this->hasOne(MemberLevel::class);
    }
    
    /**
     * 获取当前积分对应的等级
     */
    public function getCurrentLevelAttribute()
    {
        return MemberLevel::getLevelByPoints($this->total_points_earned);
    }
    
    /**
     * 检查是否达到某个等级
     */
    public function hasLevel($levelName)
    {
        $currentLevel = $this->current_level;
        return $currentLevel && $currentLevel->name === $levelName;
    }
    
    /**
     * 检查是否达到或超过某个等级
     */
    public function hasLevelOrHigher($levelName)
    {
        $currentLevel = $this->current_level;
        if (!$currentLevel) {
            return false;
        }
        
        $targetLevel = MemberLevel::where('name', $levelName)->first();
        if (!$targetLevel) {
            return false;
        }
        
        return $currentLevel->level_order >= $targetLevel->level_order;
    }
    
    /**
     * 获取用户的付费会员订阅
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }
    
    /**
     * 获取当前有效的付费会员计划
     */
    public function getCurrentMembershipAttribute()
    {
        return UserSubscription::getUserCurrentPlan($this->id);
    }
    
    /**
     * 检查是否有有效的付费会员
     */
    public function hasActiveMembership()
    {
        return $this->current_membership !== null;
    }
    
    /**
     * 获取头像 URL（中等大小，用于详情页）
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->getAvatarForContext('detail');
    }
    
    /**
     * 获取列表用头像（小图）
     */
    public function getAvatarForListAttribute(): ?string
    {
        return $this->getAvatarForContext('list');
    }
    
    /**
     * 获取详情用头像（中图）
     */
    public function getAvatarForDetailAttribute(): ?string
    {
        return $this->getAvatarForContext('detail');
    }
    
    /**
     * 获取预览用头像（大图）
     */
    public function getAvatarForPreviewAttribute(): ?string
    {
        return $this->getAvatarForContext('preview');
    }
    
    protected function getAvatarForContext($context): ?string
    {
        if (!$this->avatar) {
            return asset('images/default-avatar.png');
        }
        
        return avatar_url($this->avatar);
    }
    
    /**
     * 检查是否是 VIP
     */
    public function isVip()
    {
        $membership = $this->current_membership;
        return $membership && in_array($membership->code, ['vip', 'svip']);
    }
    
    /**
     * 检查是否是 SVIP
     */
    public function isSvip()
    {
        $membership = $this->current_membership;
        return $membership && $membership->code === 'svip';
    }
    
    /**
     * 增加金币
     */
    public function addCoins(int $amount, string $reason = ''): void
    {
        $this->increment('coins', $amount);
        $this->increment('total_coins_recharged', $amount);
        
        // TODO: 记录金币日志
    }
    
    /**
     * 消费金币
     */
    public function spendCoins(int $amount, string $reason = ''): bool
    {
        if ($this->coins < $amount) {
            return false;
        }
        
        $this->decrement('coins', $amount);
        $this->increment('total_coins_spent', $amount);
        
        // TODO: 记录金币日志
        return true;
    }
    
    /**
     * 活跃度兑换金币（100 活跃度 = 1 金币）
     */
    public function convertPointsToCoins(int $points): bool
    {
        if ($this->points < $points) {
            return false;
        }
        
        $coins = floor($points / 100);
        
        if ($coins < 1) {
            return false;
        }
        
        $this->decrement('points', $points);
        $this->increment('coins', $coins);
        // 注意：total_points_earned 不减少，因为这是历史累计获得的活跃度
        // 兑换金币不会导致会员等级降级
        
        // TODO: 记录兑换日志
        
        return true;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // 本地测试：允许ID=1和is_admin=true的用户登录
        return $this->id === 1 || $this->is_admin === true;
    }
}
