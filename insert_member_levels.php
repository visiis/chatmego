<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// 插入会员等级数据
DB::table('member_levels')->insert([
    [
        'name' => '普通會員',
        'icon' => '',
        'min_points' => 0,
        'max_points' => 999,
        'level_order' => 1,
        'privileges' => '{}',
        'is_active' => 1
    ],
    [
        'name' => '銀卡會員',
        'icon' => '',
        'min_points' => 1000,
        'max_points' => 4999,
        'level_order' => 2,
        'privileges' => '{"daily_likes":50,"can_see_who_liked":true}',
        'is_active' => 1
    ],
    [
        'name' => '金卡會員',
        'icon' => '',
        'min_points' => 5000,
        'max_points' => 19999,
        'level_order' => 3,
        'privileges' => '{"daily_likes":9999,"daily_super_likes":10,"can_see_who_liked":true,"can_recall_message":true,"priority_in_search":true}',
        'is_active' => 1
    ],
    [
        'name' => '鑽石會員',
        'icon' => '',
        'min_points' => 20000,
        'max_points' => NULL,
        'level_order' => 4,
        'privileges' => '{"daily_likes":9999,"daily_super_likes":50,"daily_points":800,"can_see_who_liked":true,"can_hide_profile":true,"can_recall_message":true,"priority_in_search":true}',
        'is_active' => 1
    ],
]);

echo '数据插入成功!' . PHP_EOL;
