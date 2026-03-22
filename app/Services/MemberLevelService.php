<?php

namespace App\Services;

use App\Models\User;
use App\Models\MemberLevel;

class MemberLevelService
{
    /**
     * 更新用户的会员等级
     */
    public static function updateUserLevel(User $user): ?MemberLevel
    {
        $currentLevel = MemberLevel::getLevelByPoints($user->points);
        
        if ($currentLevel) {
            // 可以在这里添加等级变更时的逻辑，比如发送通知
            $previousLevel = $user->current_level;
            
            if (!$previousLevel || $previousLevel->id !== $currentLevel->id) {
                // 等级发生了变化
                // TODO: 可以添加升级通知、奖励等逻辑
                \Log::info("用户 {$user->name} 等级从 {$previousLevel->name} 变更为 {$currentLevel->name}");
            }
        }
        
        return $currentLevel;
    }
    
    /**
     * 给用户增加积分并更新等级
     */
    public static function addPoints(User $user, int $points, string $reason = ''): void
    {
        $user->increment('points', $points);
        
        // 记录积分日志
        self::logPointsChange($user, $points, 'earn', $reason);
        
        // 更新等级
        self::updateUserLevel($user);
    }
    
    /**
     * 给用户扣除积分并更新等级
     */
    public static function deductPoints(User $user, int $points, string $reason = ''): void
    {
        $user->decrement('points', $points);
        
        // 确保积分不为负
        if ($user->points < 0) {
            $user->points = 0;
            $user->save();
        }
        
        // 记录积分日志
        self::logPointsChange($user, $points, 'spent', $reason);
        
        // 更新等级
        self::updateUserLevel($user);
    }
    
    /**
     * 记录积分变更日志
     */
    private static function logPointsChange(User $user, int $amount, string $type, string $reason): void
    {
        // TODO: 创建 point_logs 表后实现
        \Log::info("用户 {$user->name} 积分{$type}: {$amount}, 原因：{$reason}, 当前积分：{$user->points}");
    }
    
    /**
     * 获取所有等级列表
     */
    public static function getAllLevels()
    {
        return MemberLevel::getAllLevels();
    }
    
    /**
     * 获取用户当前等级
     */
    public static function getUserLevel(User $user): ?MemberLevel
    {
        return $user->current_level;
    }
}
