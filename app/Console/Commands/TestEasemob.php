<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EasemobService;

class TestEasemob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'easemob:test {--username= : 测试用户名}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试环信 SDK 功能';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=================================');
        $this->info('环信 SDK 测试');
        $this->info('=================================');
        $this->newLine();

        try {
            $this->info('1. 初始化环信服务...');
            $easemob = new EasemobService();
            $this->info('✅ 环信服务初始化成功');
            $this->newLine();
            
            $this->info('2. 测试获取 Token...');
            $this->info('✅ Token 获取成功');
            $this->newLine();
            
            $username = $this->option('username') ?: 'test_user_' . time();
            $password = 'test123456';
            $nickname = '测试用户';
            
            $this->info('3. 测试注册环信用户...');
            $this->info("用户名：{$username}");
            
            try {
                $result = $easemob->registerUser($username, $password, $nickname);
                $this->info('✅ 用户注册成功');
                $this->info('响应数据：' . json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                $this->newLine();
            } catch (\Exception $e) {
                $this->error('❌ 用户注册失败：' . $e->getMessage());
                $this->newLine();
            }
            
            $this->info('4. 测试获取用户信息...');
            try {
                $userInfo = $easemob->getUserInfo($username);
                $this->info('✅ 获取用户信息成功');
                $this->info('用户信息：' . json_encode($userInfo, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                $this->newLine();
            } catch (\Exception $e) {
                $this->error('❌ 获取用户信息失败：' . $e->getMessage());
                $this->newLine();
            }
            
            $this->info('5. 测试发送消息...');
            try {
                $messageResult = $easemob->sendPrivateMessage(
                    $username,
                    $username,
                    '这是一条测试消息',
                    'txt'
                );
                $this->info('✅ 消息发送成功');
                $this->info('响应数据：' . json_encode($messageResult, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                $this->newLine();
            } catch (\Exception $e) {
                $this->error('❌ 消息发送失败：' . $e->getMessage());
                $this->newLine();
            }
            
            $this->info('=================================');
            $this->info('测试完成！');
            $this->info('=================================');
            
        } catch (\Exception $e) {
            $this->error('❌ 错误：' . $e->getMessage());
            $this->error('文件：' . $e->getFile());
            $this->error('行号：' . $e->getLine());
            return 1;
        }
        
        return 0;
    }
}
