<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MemberLevel;

class MemberLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            [
                'name' => '青铜会员',
                'icon' => '🥉',
                'min_points' => 0,
                'max_points' => 1000,
                'level_order' => 1,
                'privileges' => ['基础功能'],
                'is_active' => true,
            ],
            [
                'name' => '白银会员',
                'icon' => '🥈',
                'min_points' => 1001,
                'max_points' => 5000,
                'level_order' => 2,
                'privileges' => ['基础功能', '每日额外 +5 积分'],
                'is_active' => true,
            ],
            [
                'name' => '黄金会员',
                'icon' => '🥇',
                'min_points' => 5001,
                'max_points' => 10000,
                'level_order' => 3,
                'privileges' => ['基础功能', '每日额外 +5 积分', '解锁特殊头像框'],
                'is_active' => true,
            ],
            [
                'name' => '钻石会员',
                'icon' => '💎',
                'min_points' => 10001,
                'max_points' => 50000,
                'level_order' => 4,
                'privileges' => ['基础功能', '每日额外 +10 积分', '解锁特殊头像框', '解锁高级搜索功能'],
                'is_active' => true,
            ],
            [
                'name' => '王者会员',
                'icon' => '👑',
                'min_points' => 50001,
                'max_points' => null,
                'level_order' => 5,
                'privileges' => ['基础功能', '每日额外 +20 积分', '解锁特殊头像框', '解锁高级搜索功能', '专属客服', '优先推荐'],
                'is_active' => true,
            ],
        ];
        
        foreach ($levels as $level) {
            MemberLevel::create($level);
        }
    }
}
