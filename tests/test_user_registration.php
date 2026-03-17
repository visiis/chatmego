<?php

/*
 * 测试用户注册时自动注册环信
 * 
 * 使用方法：
 * php tests/test_user_registration.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Services\EasemobService;
use Illuminate\Support\Facades\Hash;

echo "=== 测试用户注册时自动注册环信 ===\n\n";

try {
    // 创建一个测试用户
    $testUsername = 'testuser_' . time();
    $testEmail = 'test_' . time() . '@example.com';
    $testPassword = 'password123';

    echo "步骤 1: 创建本地用户\n";
    echo "-------------------\n";
    
    $user = User::create([
        'name' => $testUsername,
        'email' => $testEmail,
        'password' => Hash::make($testPassword),
    ]);

    echo "✓ 本地用户创建成功\n";
    echo "  用户 ID: {$user->id}\n";
    echo "  用户名：{$user->name}\n";
    echo "  邮箱：{$user->email}\n\n";

    // 注册环信用户
    echo "步骤 2: 注册环信用户\n";
    echo "-------------------\n";
    
    $easemobService = app(EasemobService::class);
    
    // 使用用户 ID 作为环信用户名
    $huanxinUsername = 'user_' . $user->id;
    
    try {
        $result = $easemobService->registerUser($huanxinUsername, $testPassword, $user->name);
        
        echo "✓ 环信用户注册成功\n";
        echo "  环信用户名：$huanxinUsername\n";
        echo "  昵称：{$user->name}\n";
        
        if (isset($result['entities'][0]['uuid'])) {
            echo "  环信用户 UUID: {$result['entities'][0]['uuid']}\n";
        }
        echo "\n";
        
        // 验证用户信息
        echo "步骤 3: 验证环信用户信息\n";
        echo "-------------------\n";
        
        $userInfo = $easemobService->getUserInfo($huanxinUsername);
        
        echo "✓ 获取环信用户信息成功\n";
        echo "  用户信息：" . json_encode($userInfo['entities'][0] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        
    } catch (\Exception $e) {
        echo "✗ 环信用户注册失败：" . $e->getMessage() . "\n\n";
    }

    // 清理测试用户（可选）
    echo "提示：测试用户已创建\n";
    echo "  本地用户 ID: {$user->id}\n";
    echo "  如需删除测试用户，请运行：\n";
    echo "  php artisan tinker\n";
    echo "  >>> User::find({$user->id})->delete();\n\n";

    echo "=== 测试完成 ===\n";

} catch (\Exception $e) {
    echo "✗ 测试失败：" . $e->getMessage() . "\n";
    echo "错误堆栈:\n" . $e->getTraceAsString() . "\n";
}
