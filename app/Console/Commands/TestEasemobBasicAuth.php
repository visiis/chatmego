<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestEasemobBasicAuth extends Command
{
    protected $signature = 'easemob:test-basicauth';
    protected $description = '使用 Basic Auth 方式测试环信 Token 获取';

    public function handle()
    {
        $this->info('=================================');
        $this->info('环信 Token 测试 (使用 Basic Auth)');
        $this->info('=================================');
        $this->newLine();
        
        $clientId = config('easemob.client_id');
        $clientSecret = config('easemob.client_secret');
        $restApi = config('easemob.rest_api');
        
        $appKey = config('easemob.app_key');
        $parts = explode('#', $appKey);
        $orgName = $parts[0];
        $appName = $parts[1] ?? 'demo';
        
        $tokenUrl = "https://{$restApi}/{$orgName}/{$appName}/token";
        
        $this->info("Token URL: {$tokenUrl}");
        $this->info("Client ID: {$clientId}");
        $this->info("Client Secret: " . substr($clientSecret, 0, 10) . '...');
        $this->newLine();
        
        // 方法 1: 使用 Basic Auth
        $this->info('方法 1: 使用 Basic Auth');
        try {
            $response = Http::withOptions(['verify' => false])
                ->withBasicAuth($clientId, $clientSecret)
                ->post($tokenUrl, [
                    'grant_type' => 'client_credentials',
                ]);
            
            $this->info("状态码：" . $response->status());
            $this->info("响应内容：" . $response->body());
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['access_token'])) {
                    $this->info('✅ Token 获取成功!');
                    $this->info("Token: " . substr($data['access_token'], 0, 30) . '...');
                }
            }
        } catch (\Exception $e) {
            $this->error('❌ 异常：' . $e->getMessage());
        }
        
        $this->newLine();
        
        // 方法 2: 使用 Authorization Header
        $this->info('方法 2: 使用 Authorization Header');
        try {
            $auth = base64_encode("{$clientId}:{$clientSecret}");
            $response = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Basic ' . $auth,
                ])
                ->post($tokenUrl, [
                    'grant_type' => 'client_credentials',
                ]);
            
            $this->info("状态码：" . $response->status());
            $this->info("响应内容：" . $response->body());
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['access_token'])) {
                    $this->info('✅ Token 获取成功!');
                    $this->info("Token: " . substr($data['access_token'], 0, 30) . '...');
                }
            }
        } catch (\Exception $e) {
            $this->error('❌ 异常：' . $e->getMessage());
        }
        
        $this->newLine();
        $this->info('=================================');
    }
}
