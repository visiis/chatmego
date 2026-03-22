<?php

namespace Database\Seeders;

use App\Models\MembershipPlan;
use Illuminate\Database\Seeder;

class MembershipPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => '基础会员',
                'code' => 'basic',
                'price' => 300, // 300 金币/月 = 30 元
                'duration_days' => 30,
                'privileges' => [
                    '每日可发送 3 次好友申请',
                    '查看最近 7 天访客记录',
                    '基础装扮主题',
                    '广告减少 50%',
                ],
                'icon' => '💳',
                'badge_color' => '#4A90E2',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => '高级会员',
                'code' => 'vip',
                'price' => 600, // 600 金币/月 = 60 元
                'duration_days' => 30,
                'privileges' => [
                    '每日可发送 10 次好友申请',
                    '查看最近 30 天访客记录',
                    '高级装扮主题',
                    '无广告体验',
                    '优先客服支持',
                    '查看谁喜欢了我',
                ],
                'icon' => '⭐',
                'badge_color' => '#FFD700',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => '至尊会员',
                'code' => 'svip',
                'price' => 1200, // 1200 金币/月 = 120 元
                'duration_days' => 30,
                'privileges' => [
                    '无限好友申请',
                    '查看永久访客记录',
                    '至尊装扮主题',
                    '无广告体验',
                    '专属客服支持',
                    '查看谁喜欢了我',
                    '消息置顶',
                    '隐身访问',
                    '高级筛选功能',
                ],
                'icon' => '👑',
                'badge_color' => '#FF6B6B',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];
        
        foreach ($plans as $plan) {
            MembershipPlan::updateOrCreate(
                ['code' => $plan['code']],
                $plan
            );
        }
        
        $this->command->info('会员计划数据填充完成！');
    }
}
