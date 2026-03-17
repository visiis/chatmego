<?php

/*
 * 环信 SDK 测试脚本
 * 
 * 使用方法：
 * php tests/easemob_test.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\EasemobService;

echo "=== 环信 SDK 测试 ===\n\n";

try {
    // 创建服务实例
    $easemobService = new EasemobService();
    
    echo "✓ EasemobService 初始化成功\n\n";
    
    // 测试 1: 获取 Token
    echo "测试 1: 获取 Token\n";
    echo "-------------------\n";
    $token = $easemobService->getToken();
    echo "Token: " . substr($token, 0, 30) . "...\n";
    echo "Token 长度：" . strlen($token) . "\n";
    echo "✓ 获取 Token 成功\n\n";
    
    // 测试 2: 注册用户
    echo "测试 2: 注册用户\n";
    echo "-------------------\n";
    $testUsername = 'test_user_' . time();
    $testPassword = 'password123';
    $testNickname = '测试用户';
    
    try {
        $result = $easemobService->registerUser($testUsername, $testPassword, $testNickname);
        echo "用户名：$testUsername\n";
        echo "昵称：$testNickname\n";
        echo "注册结果：" . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        echo "✓ 注册用户成功\n\n";
    } catch (\Exception $e) {
        echo "✗ 注册用户失败：" . $e->getMessage() . "\n\n";
    }
    
    // 测试 3: 获取用户信息
    echo "测试 3: 获取用户信息\n";
    echo "-------------------\n";
    try {
        // 尝试获取刚注册的用户信息
        $userInfo = $easemobService->getUserInfo($testUsername);
        echo "用户信息：" . json_encode($userInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        echo "✓ 获取用户信息成功\n\n";
    } catch (\Exception $e) {
        echo "✗ 获取用户信息失败：" . $e->getMessage() . "\n\n";
    }
    
    // 测试 4: 发送消息
    echo "测试 4: 发送消息\n";
    echo "-------------------\n";
    try {
        $messageResult = $easemobService->sendPrivateMessage(
            'admin',
            $testUsername,
            '欢迎加入 ChatMeGo！',
            'txt'
        );
        echo "发送消息结果：" . json_encode($messageResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        echo "✓ 发送消息成功\n\n";
    } catch (\Exception $e) {
        echo "✗ 发送消息失败：" . $e->getMessage() . "\n\n";
    }
    
    echo "=== 测试完成 ===\n";
    
} catch (\Exception $e) {
    echo "✗ 测试失败：" . $e->getMessage() . "\n";
    echo "错误堆栈:\n" . $e->getTraceAsString() . "\n";
}
