<?php

namespace App\Observers;

use App\Models\User;
use App\Services\EasemobService;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * 处理用户创建后的事件
     *
     * @param User $user
     * @return void
     */
    public function created(User $user)
    {
        // 异步注册环信用户
        $this->registerEasemobUser($user);
    }

    /**
     * 处理用户更新后的事件
     *
     * @param User $user
     * @return void
     */
    public function updated(User $user)
    {
        // 如果用户名或昵称发生变化，更新环信用户信息
        if ($user->isDirty('name')) {
            $this->updateEasemobUser($user);
        }
    }

    /**
     * 处理用户删除后的事件
     *
     * @param User $user
     * @return void
     */
    public function deleted(User $user)
    {
        // 删除环信用户
        $this->deleteEasemobUser($user);
    }

    /**
     * 注册环信用户
     *
     * @param User $user
     * @return void
     */
    protected function registerEasemobUser(User $user)
    {
        try {
            $easemobService = app(EasemobService::class);
            
            // 使用用户 ID 作为环信用户名，确保唯一性
            $username = 'user_' . $user->id;
            // 生成一个随机密码（用户不知道环信密码，通过 token 登录）
            $password = bin2hex(random_bytes(16));
            $nickname = $user->name;

            $easemobService->registerUser($username, $password, $nickname);

            Log::info('用户注册环信成功', [
                'user_id' => $user->id,
                'username' => $username,
                'nickname' => $nickname,
            ]);
        } catch (\Exception $e) {
            // 环信注册失败不影响本地注册，只记录日志
            Log::error('用户注册环信失败', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 更新环信用户信息
     *
     * @param User $user
     * @return void
     */
    protected function updateEasemobUser(User $user)
    {
        try {
            $easemobService = app(EasemobService::class);
            
            $username = 'user_' . $user->id;
            
            $easemobService->updateUserInfo($username, $user->name);

            Log::info('更新环信用户信息成功', [
                'user_id' => $user->id,
                'username' => $username,
                'nickname' => $user->name,
            ]);
        } catch (\Exception $e) {
            Log::error('更新环信用户信息失败', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 删除环信用户
     *
     * @param User $user
     * @return void
     */
    protected function deleteEasemobUser(User $user)
    {
        try {
            $easemobService = app(EasemobService::class);
            
            $username = 'user_' . $user->id;
            
            $easemobService->deleteUser($username);

            Log::info('删除环信用户成功', [
                'user_id' => $user->id,
                'username' => $username,
            ]);
        } catch (\Exception $e) {
            Log::error('删除环信用户失败', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
