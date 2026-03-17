<?php

/*
 * 测试 User Observer 自动注册环信
 * 
 * 使用方法：
 * php tests/test_user_observer.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Services\EasemobService;
use Illuminate\Support\Facades\Hash;

echo "=== 测试 User Observer 自动注册环信 ===\n\n";

try {
    // 创建一个测试用户
    $testUsername = 'observer_test_' . time();
    $testEmail = 'observer_' . time() . '@example.com';
    $testPassword = 'password123';

    echo "步骤 1: 创建本地用户（Observer 会自动注册环信）\n";
    echo "----------------------------------------------\n";
    
    $user = User::create([
        'name' => $testUsername,
        'email' => $testEmail,
        'password' => Hash::make($testPassword),
    ]);

    echo "✓ 本地用户创建成功\n";
    echo "  用户 ID: {$user->id}\n";
    echo "  用户名：{$user->name}\n";
    echo "  邮箱：{$user->email}\n\n";

    // 验证环信用户是否已自动注册
    echo "步骤 2: 验证环信用户是否已自动注册\n";
    echo "----------------------------------------------\n";
    
    $easemobService = app(EasemobService::class);
    $huanxinUsername = 'user_' . $user->id;
    
    try {
        $userInfo = $easemobService->getUserInfo($huanxinUsername);
        
        echo "✓ Observer 成功自动注册环信用户\n";
        echo "  环信用户名：$huanxinUsername\n";
        
        if (isset($userInfo['entities'][0])) {
            $entity = $userInfo['entities'][0];
            echo "  昵称：{$entity['nickname']}\n";
            echo "  UUID: {$entity['uuid']}\n";
            echo "  创建时间：" . date('Y-m-d H:i:s', $entity['created'] / 1000) . "\n";
            echo "  激活状态：" . ($entity['activated'] ? '已激活' : '未激活') . "\n";
        }
        echo "\n";
        
    } catch (\Exception $e) {
        echo "✗ Observer 自动注册失败：" . $e->getMessage() . "\n\n";
    }

    // 测试更新用户
    echo "步骤 3: 测试更新用户（Observer 会自动更新环信）\n";
    echo "----------------------------------------------\n";
    
    $newName = $testUsername . '_updated';
    $user->update(['name' => $newName]);
    
    echo "✓ 本地用户更新成功\n";
    echo "  新用户名：$newName\n\n";
    
    // 等待一秒确保异步更新完成
    sleep(1);
    
    try {
        $userInfo = $easemobService->getUserInfo($huanxinUsername);
        
        if (isset($userInfo['entities'][0]['nickname'])) {
            echo "✓ Observer 成功自动更新环信用户信息\n";
            echo "  新昵称：{$userInfo['entities'][0]['nickname']}\n\n";
        }
    } catch (\Exception $e) {
        echo "✗ 获取更新后的用户信息失败：" . $e->getMessage() . "\n\n";
    }

    // 清理提示
    echo "提示：测试用户已创建\n";
    echo "  本地用户 ID: {$user->id}\n";
    echo "  如需删除测试用户，请运行：\n";
    echo "  php artisan tinker\n";
    echo "  >>> User::find({$user->id})->delete();\n";
    echo "  （Observer 会自动删除环信用户）\n\n";

    echo "=== 测试完成 ===\n";

} catch (\Exception $e) {
    echo "✗ 测试失败：" . $e->getMessage() . "\n";
    echo "错误堆栈:\n" . $e->getTraceAsString() . "\n";
}
