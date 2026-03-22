<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberLevel extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'min_points',
        'max_points',
        'level_order',
        'privileges',
        'is_active',
    ];
    
    protected $casts = [
        'min_points' => 'integer',
        'max_points' => 'integer',
        'level_order' => 'integer',
        'privileges' => 'array',
        'is_active' => 'boolean',
    ];
    
    /**
     * 获取当前积分对应的等级
     */
    public static function getLevelByPoints($points)
    {
        return static::where('min_points', '<=', $points)
            ->where(function($query) use ($points) {
                $query->whereNull('max_points')
                      ->orWhere('max_points', '>=', $points);
            })
            ->where('is_active', true)
            ->orderBy('level_order', 'desc')
            ->first();
    }
    
    /**
     * 获取所有等级（按顺序）
     */
    public static function getAllLevels()
    {
        return static::where('is_active', true)
            ->orderBy('level_order', 'asc')
            ->get();
    }
}
