<?php

/*
 * 测试新增用户并验证 Observer 自动注册环信
 * 
 * 使用方法：
 * php tests/test_new_user_registration.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Services\EasemobService;
use Illuminate\Support\Facades\Hash;

echo "=== 测试新增用户并验证 Observer 自动注册 ===\n\n";

try {
    // 创建一个测试用户
    $testUsername = 'newuser_' . time();
    $testEmail = 'newuser_' . time() . '@example.com';
    $testPassword = 'password123';

    echo "步骤 1: 创建新的本地用户\n";
    echo "-------------------\n";
    echo "用户名：$testUsername\n";
    echo "邮箱：$testEmail\n\n";
    
    $user = User::create([
        'name' => $testUsername,
        'email' => $testEmail,
        'password' => Hash::make($testPassword),
    ]);

    echo "✓ 本地用户创建成功\n";
    echo "  用户 ID: {$user->id}\n\n";

    // 验证环信用户是否已自动注册
    echo "步骤 2: 验证 Observer 是否自动注册了环信用户\n";
    echo "-------------------\n";
    
    $easemobService = app(EasemobService::class);
    $huanxinUsername = 'user_' . $user->id;
    
    try {
        $userInfo = $easemobService->getUserInfo($huanxinUsername);
        
        echo "✓ Observer 工作正常！环信用户已自动注册\n";
        
        if (isset($userInfo['entities'][0])) {
            $entity = $userInfo['entities'][0];
            echo "  环信用户名：{$entity['username']}\n";
            echo "  昵称：{$entity['nickname']}\n";
            echo "  UUID: {$entity['uuid']}\n";
            echo "  创建时间：" . date('Y-m-d H:i:s', $entity['created'] / 1000) . "\n";
        }
        echo "\n";
        
    } catch (\Exception $e) {
        echo "✗ 获取环信用户信息失败：" . $e->getMessage() . "\n";
        echo "  这可能是因为 Observer 没有正确注册\n\n";
    }

    // 使用同步命令验证
    echo "步骤 3: 使用同步命令验证（应该显示已存在）\n";
    echo "-------------------\n";
    echo "运行：php artisan easemob:sync-users\n\n";
    
    echo "提示：\n";
    echo "  - 新用户会自动通过 Observer 注册到环信\n";
    echo "  - 使用同步命令可以批量处理已存在的用户\n";
    echo "  - 测试用户 ID: {$user->id}\n";
    echo "  - 删除测试用户：User::find({$user->id})->delete();\n\n";

    echo "=== 测试完成 ===\n";

} catch (\Exception $e) {
    echo "✗ 测试失败：" . $e->getMessage() . "\n";
    echo "错误堆栈:\n" . $e->getTraceAsString() . "\n";
}
