<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

$orgName = config('easemob.org_name');
$appName = config('easemob.app_name', 'demo');
$clientId = config('easemob.client_id');
$clientSecret = config('easemob.client_secret');

echo "=== 调试环信发送消息 - 使用 target 字段 ===\n\n";

// 1. 获取 Token
$tokenUrl = 'https://' . config('easemob.rest_api') . "/{$orgName}/{$appName}/token";
$tokenResponse = Http::withOptions(['verify' => false])->post($tokenUrl, [
    'grant_type' => 'client_credentials',
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
]);

if (!$tokenResponse->successful()) {
    echo "获取 Token 失败：" . $tokenResponse->body() . "\n";
    exit;
}

$token = $tokenResponse->json()['access_token'];
echo "Token 获取成功\n\n";

// 2. 发送消息 - 使用 target 字段
$messagesUrl = 'https://' . config('easemob.rest_api') . "/{$orgName}/{$appName}/messages";

$testCases = [
    '使用 targets 字段' => [
        'from' => 'user_a',
        'targets' => ['user_b'],
        'type' => 'txt',
        'msg' => [
            'type' => 'txt',
            'msg' => 'Hello!',
        ],
        'ext' => [
            'custom_field' => 'custom_value',
        ],
    ],
    '使用 target 字段（单数）' => [
        'from' => 'user_a',
        'target' => 'user_b',
        'type' => 'txt',
        'msg' => [
            'type' => 'txt',
            'msg' => 'Hello!',
        ],
        'ext' => [
            'custom_field' => 'custom_value',
        ],
    ],
    '完整格式 - 带 msg_type' => [
        'from' => 'user_a',
        'to' => 'user_b',
        'type' => 'txt',
        'msg' => [
            'type' => 'txt',
            'msg' => 'Hello!',
        ],
        'msg_type' => 'txt',
        'ext' => [
            'custom_field' => 'custom_value',
        ],
    ],
];

foreach ($testCases as $name => $payload) {
    echo "测试：$name\n";
    echo "Payload: " . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n";
    
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Content-Type' => 'application/json',
    ])->withOptions(['verify' => false])->post($messagesUrl, $payload);
    
    if ($response->successful()) {
        echo "✓ 成功！\n";
        echo "响应：" . json_encode($response->json(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
        exit;
    } else {
        echo "✗ 失败：" . $response->body() . "\n\n";
    }
}
