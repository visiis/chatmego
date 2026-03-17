<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestEasemobFull extends Command
{
    protected $signature = 'easemob:full-test';
    protected $description = '完整的环信 API 测试';

    public function handle()
    {
        $this->info('=================================');
        $this->info('环信 API 完整测试');
        $this->info('=================================');
        $this->newLine();
        
        // 配置信息
        $appKey = config('easemob.app_key');
        $orgName = config('easemob.org_name');
        $appName = config('easemob.app_name', 'demo');
        $clientId = config('easemob.client_id');
        $clientSecret = config('easemob.client_secret');
        $restApi = config('easemob.rest_api');
        
        $this->info('📋 配置信息:');
        $this->table(
            ['配置项', '值'],
            [
                ['App Key', $appKey],
                ['Org Name', $orgName],
                ['App Name', $appName],
                ['Client ID', $clientId],
                ['Client Secret', substr($clientSecret, 0, 15) . '...'],
                ['REST API', $restApi],
            ]
        );
        $this->newLine();
        
        // 测试不同的 Token URL 格式
        $testUrls = [
            "https://{$restApi}/{$orgName}/{$appName}/token" => '标准格式',
            "https://{$restApi}/1.0/{$orgName}/{$appName}/token" => '带 1.0 前缀',
            "https://{$restApi}/token" => '不带 org/app',
        ];
        
        foreach ($testUrls as $url => $description) {
            $this->info("🔍 测试 URL ({$description}): {$url}");
            
            // 测试 1: 使用 client_id 和 client_secret
            $this->comment('  方式 1: client_id + client_secret');
            try {
                $response = Http::withOptions(['verify' => false])
                    ->timeout(10)
                    ->post($url, [
                        'grant_type' => 'client_credentials',
                        'client_id' => $clientId,
                        'client_secret' => $clientSecret,
                    ]);
                
                $this->info("  状态码：{$response->status()}");
                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['access_token'])) {
                        $this->info("  ✅ 成功！Token: " . substr($data['access_token'], 0, 30) . '...');
                        $this->newLine();
                        $this->info('=================================');
                        $this->info('🎉 找到正确的配置！');
                        $this->info('=================================');
                        return 0;
                    }
                }
                $this->info("  响应：" . $response->body());
            } catch (\Exception $e) {
                $this->error("  异常：" . $e->getMessage());
            }
            
            // 测试 2: 使用 AppKey 作为 client_id
            $this->comment('  方式 2: AppKey + client_secret');
            try {
                $response = Http::withOptions(['verify' => false])
                    ->timeout(10)
                    ->post($url, [
                        'grant_type' => 'client_credentials',
                        'client_id' => $appKey,
                        'client_secret' => $clientSecret,
                    ]);
                
                $this->info("  状态码：{$response->status()}");
                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['access_token'])) {
                        $this->info("  ✅ 成功！Token: " . substr($data['access_token'], 0, 30) . '...');
                        $this->newLine();
                        $this->info('=================================');
                        $this->info('🎉 找到正确的配置！');
                        $this->info('=================================');
                        return 0;
                    }
                }
                $this->info("  响应：" . $response->body());
            } catch (\Exception $e) {
                $this->error("  异常：" . $e->getMessage());
            }
            
            $this->newLine();
        }
        
        $this->error('=================================');
        $this->error('❌ 所有测试都失败了');
        $this->error('=================================');
        $this->newLine();
        
        $this->info('💡 建议检查:');
        $this->info('1. 环信控制台中的应用状态是否为"已激活"');
        $this->info('2. Client ID 和 Client Secret 是否正确复制');
        $this->info('3. 是否混淆了不同应用的凭证');
        $this->info('4. 环信账户是否有足够的权限');
        
        return 1;
    }
}
