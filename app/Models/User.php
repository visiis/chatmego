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
        return MemberLevel::getLevelByPoints($this->points);
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

    public function canAccessPanel(Panel $panel): bool
    {
        // 本地测试：允许ID=1和is_admin=true的用户登录
        return $this->id === 1 || $this->is_admin === true;
    }
}
