<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestEasemobDebug extends Command
{
    protected $signature = 'easemob:debug';
    protected $description = '调试环信配置';

    public function handle()
    {
        $this->info('=================================');
        $this->info('环信配置调试');
        $this->info('=================================');
        $this->newLine();
        
        $appKey = config('easemob.app_key');
        $orgName = config('easemob.org_name');
        $appName = config('easemob.app_name', 'demo');
        $clientId = config('easemob.client_id');
        $clientSecret = config('easemob.client_secret');
        $restApi = config('easemob.rest_api');
        
        $this->info('配置信息:');
        $this->info("App Key: {$appKey}");
        $this->info("Org Name: {$orgName}");
        $this->info("App Name: {$appName}");
        $this->info("Client ID: {$clientId}");
        $this->info("Client Secret: " . substr($clientSecret, 0, 10) . '...');
        $this->info("REST API: {$restApi}");
        $this->newLine();
        
        $parts = explode('#', $appKey);
        $this->info("从 AppKey 解析:");
        $this->info("Org Name (from appKey): {$parts[0]}");
        $this->info("App Name (from appKey): " . ($parts[1] ?? 'demo'));
        $this->newLine();
        
        // Token URL 不需要 /1.0 前缀
        $tokenUrl = "https://{$restApi}/{$orgName}/{$appName}/token";
        
        $this->info("Token URL: {$tokenUrl}");
        $this->newLine();
        
        $this->info('尝试获取 Token...');
        
        try {
            $response = Http::withOptions(['verify' => false])
                ->post($tokenUrl, [
                    'grant_type' => 'client_credentials',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                ]);
            
            $this->info("状态码：" . $response->status());
            $this->info("响应内容：" . $response->body());
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['access_token'])) {
                    $this->info('✅ Token 获取成功!');
                    $this->info("Token: " . substr($data['access_token'], 0, 20) . '...');
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
