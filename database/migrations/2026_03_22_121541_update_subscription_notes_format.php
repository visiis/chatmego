<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 更新现有的 notes 字段，将中文文本转换为 JSON 格式
        $subscriptions = DB::table('user_subscriptions')->get();
        
        foreach ($subscriptions as $subscription) {
            $notes = $subscription->notes;
            
            // 跳过已经是 JSON 格式的数据
            if (empty($notes) || json_decode($notes, true) !== null) {
                continue;
            }
            
            // 解析中文备注，提取信息
            // 格式：购买至尊会员，1 个月，30 天，叠加模式
            if (preg_match('/购买 (.+?) 会员，(\d+) 个月，(\d+) 天/', $notes, $matches)) {
                $planName = $matches[1];
                $months = (int)$matches[2];
                $days = (int)$matches[3];
                
                // 根据中文名称映射到 code
                $planCodeMap = [
                    '基础' => 'basic',
                    '高级' => 'vip',
                    '至尊' => 'svip',
                ];
                
                $planCode = $planCodeMap[$planName] ?? 'basic';
                
                // 更新为 JSON 格式
                DB::table('user_subscriptions')
                    ->where('id', $subscription->id)
                    ->update([
                        'notes' => json_encode([
                            'plan_code' => $planCode,
                            'months' => $months,
                            'days' => $days,
                        ]),
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 不需要回滚
    }
};
