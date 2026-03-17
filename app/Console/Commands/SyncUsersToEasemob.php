<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\EasemobService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncUsersToEasemob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'easemob:sync-users {--force : 强制重新注册已存在的用户}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将所有现有用户同步到环信';

    /**
     * Execute the console command.
     */
    public function handle(EasemobService $easemobService)
    {
        $this->info('=== 开始同步用户到环信 ===');
        $this->newLine();

        $users = User::all();
        $total = $users->count();
        
        $this->info("找到 {$total} 个用户");
        $this->newLine();

        $success = 0;
        $failed = 0;
        $exists = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($users as $user) {
            $username = 'user_' . $user->id;
            
            try {
                // 检查用户是否已存在
                try {
                    $easemobService->getUserInfo($username);
                    
                    // 用户已存在
                    if ($this->option('force')) {
                        // 强制模式：更新用户信息
                        $easemobService->updateUserInfo($username, $user->name);
                        $this->recordSuccess($user, $username, 'updated');
                        $success++;
                    } else {
                        $this->recordExists($user, $username);
                        $exists++;
                    }
                } catch (\Exception $e) {
                    // 用户不存在，注册
                    if (strpos($e->getMessage(), '404') !== false || 
                        strpos($e->getMessage(), 'user not found') !== false) {
                        // 注册用户
                        $password = bin2hex(random_bytes(16)); // 生成随机密码
                        $easemobService->registerUser($username, $password, $user->name);
                        
                        $this->recordSuccess($user, $username, 'registered');
                        $success++;
                        
                        Log::info('批量同步用户到环信成功', [
                            'user_id' => $user->id,
                            'username' => $username,
                            'nickname' => $user->name,
                        ]);
                    } else {
                        // 其他错误
                        throw $e;
                    }
                }
            } catch (\Exception $e) {
                $this->recordFailed($user, $username, $e->getMessage());
                $failed++;
                
                Log::error('批量同步用户到环信失败', [
                    'user_id' => $user->id,
                    'username' => $username,
                    'error' => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['状态', '数量'],
            [
                ['成功', $success],
                ['已存在', $exists],
                ['失败', $failed],
            ]
        );

        $this->newLine();
        
        if ($failed > 0) {
            $this->error("同步完成，但有 {$failed} 个用户失败，请查看日志获取详细信息");
        } else {
            $this->info("✓ 同步完成！成功：{$success}, 已存在：{$exists}");
        }

        $this->newLine();
        $this->info('=== 同步结束 ===');

        return Command::SUCCESS;
    }

    /**
     * 记录成功的用户
     */
    protected function recordSuccess(User $user, string $username, string $action)
    {
        $actionText = $action === 'registered' ? '注册' : '更新';
        $this->line("<fg=green>✓</> 用户 {$user->name} (ID: {$user->id}) - {$actionText}环信成功 (用户名：{$username})");
    }

    /**
     * 记录已存在的用户
     */
    protected function recordExists(User $user, string $username)
    {
        $this->line("<fg=yellow>○</> 用户 {$user->name} (ID: {$user->id}) - 环信用户已存在 (用户名：{$username})");
    }

    /**
     * 记录失败的用户
     */
    protected function recordFailed(User $user, string $username, string $error)
    {
        $this->line("<fg=red>✗</> 用户 {$user->name} (ID: {$user->id}) - 失败：{$error}");
    }
}
