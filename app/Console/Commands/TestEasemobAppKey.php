<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestEasemobAppKey extends Command
{
    protected $signature = 'easemob:test-appkey';
    protected $description = '使用 AppKey 方式测试环信 Token 获取';

    public function handle()
    {
        $this->info('=================================');
        $this->info('环信 Token 测试 (使用 AppKey)');
        $this->info('=================================');
        $this->newLine();
        
        $appKey = config('easemob.app_key');
        $clientSecret = config('easemob.client_secret');
        $restApi = config('easemob.rest_api');
        
        $parts = explode('#', $appKey);
        $orgName = $parts[0];
        $appName = $parts[1] ?? 'demo';
        
        $tokenUrl = "https://{$restApi}/{$orgName}/{$appName}/token";
        
        $this->info("Token URL: {$tokenUrl}");
        $this->info("Client ID (使用 AppKey): {$appKey}");
        $this->info("Client Secret: " . substr($clientSecret, 0, 10) . '...');
        $this->newLine();
        
        try {
            $response = Http::withOptions(['verify' => false])
                ->post($tokenUrl, [
                    'grant_type' => 'client_credentials',
                    'client_id' => $appKey,
                    'client_secret' => $clientSecret,
                ]);
            
            $this->info("状态码：" . $response->status());
            $this->info("响应内容：" . $response->body());
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['access_token'])) {
                    $this->info('✅ Token 获取成功!');
                    $this->info("Token: " . substr($data['access_token'], 0, 30) . '...');
                    $this->info("Token 完整：" . $data['access_token']);
                } else {
                    $this->error('❌ 响应中没有 access_token');
                }
            } else {
                $this->error('❌ 请求失败');
            }
        } catch (\Exception $e) {
            $this->error('❌ 异常：' . $e->getMessage());
        }
        
        $this->newLine();
        $this->info('=================================');
    }
}
