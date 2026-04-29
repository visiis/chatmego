<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EasemobService
{
    protected $baseUrl;
    protected $appKey;
    protected $orgName;
    protected $appName;
    protected $clientId;
    protected $clientSecret;
    protected $token;

    public function __construct()
    {
        $this->appKey = config('easemob.app_key');
        $this->orgName = config('easemob.org_name');
        $this->appName = config('easemob.app_name', 'demo');
        $this->clientId = config('easemob.client_id');
        $this->clientSecret = config('easemob.client_secret');
        $this->baseUrl = 'https://' . config('easemob.rest_api') . '/1.0';
    }
    
    protected function getTokenOrNull()
    {
        try {
            return $this->getToken();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * 获取环信 Token
     * 
     * @param bool $useAppKey 是否使用 AppKey 方式认证
     */
    public function getToken(bool $useAppKey = false)
    {
        // 从 appKey 中提取 orgName 和 appName
        $parts = explode('#', $this->appKey);
        $orgName = $parts[0];
        $appName = $parts[1] ?? 'demo';
        
        // Token URL 不需要 /1.0 前缀
        $tokenUrl = 'https://' . config('easemob.rest_api') . "/{$orgName}/{$appName}/token";
        
        // 使用 AppKey 方式认证
        if ($useAppKey) {
            $response = Http::withOptions([
                'verify' => false,
            ])->post($tokenUrl, [
                'grant_type' => 'client_credentials',
                'client_id' => $this->appKey,
                'client_secret' => $this->clientSecret,
            ]);
        } else {
            // 使用 Client ID 方式认证
            $response = Http::withOptions([
                'verify' => false,
            ])->post($tokenUrl, [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);
        }

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['access_token'])) {
                return $data['access_token'];
            }
            throw new \Exception('获取环信 Token 失败：' . json_encode($data, JSON_UNESCAPED_UNICODE));
        }

        throw new \Exception('获取环信 Token 失败：' . $response->body());
    }

    /**
     * 注册环信用户
     * 
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $nickname 昵称
     * @return array
     */
    public function registerUser(string $username, string $password, string $nickname = '')
    {
        $token = $this->getToken();
        
        // 从 appKey 中提取 orgName 和 appName
        $parts = explode('#', $this->appKey);
        $orgName = $parts[0];
        $appName = $parts[1] ?? 'demo';
        
        // 用户管理 API 不需要 /1.0 前缀
        $url = 'https://' . config('easemob.rest_api') . "/{$orgName}/{$appName}/users";
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->withOptions([
            'verify' => false,
        ])->post($url, [
            'username' => $username,
            'password' => $password,
            'nickname' => $nickname,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('注册环信用户失败：' . $response->body());
    }

    /**
     * 发送单聊消息
     * 
     * @param string $from 发送者用户名
     * @param string $to 接收者用户名
     * @param string $message 消息内容
     * @param string $type 消息类型：txt, img, voice, video, location, file
     * @return array
     */
    public function sendPrivateMessage(string $from, string $to, string $message, string $type = 'txt')
    {
        $token = $this->getToken();
        
        // 从 appKey 中提取 orgName 和 appName
        $parts = explode('#', $this->appKey);
        $orgName = $parts[0];
        $appName = $parts[1] ?? 'demo';
        
        // 消息 API 不需要 /1.0 前缀
        $url = 'https://' . config('easemob.rest_api') . "/{$orgName}/{$appName}/messages";
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->withOptions([
            'verify' => false,
        ])->post($url, [
            'from' => $from,
            'to' => $to,
            'type' => $type,
            'msg' => [
                'type' => $type,
                'msg' => $message,
            ],
            'bodies' => [
                [
                    'type' => $type,
                    'msg' => $message,
                ],
            ],
            'target_type' => 'users',
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('发送消息失败：' . $response->body());
    }

    /**
     * 获取用户信息
     * 
     * @param string $username 用户名
     * @return array
     */
    public function getUserInfo(string $username)
    {
        $token = $this->getToken();
        
        // 从 appKey 中提取 orgName 和 appName
        $parts = explode('#', $this->appKey);
        $orgName = $parts[0];
        $appName = $parts[1] ?? 'demo';
        
        // 用户管理 API 不需要 /1.0 前缀
        $url = 'https://' . config('easemob.rest_api') . "/{$orgName}/{$appName}/users/{$username}";
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withOptions([
            'verify' => false,
        ])->get($url);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('获取用户信息失败：' . $response->body());
    }

    /**
     * 更新用户信息
     * 
     * @param string $username 用户名
     * @param string $nickname 新昵称
     * @return array
     */
    public function updateUserInfo(string $username, string $nickname)
    {
        $token = $this->getToken();
        
        // 从 appKey 中提取 orgName 和 appName
        $parts = explode('#', $this->appKey);
        $orgName = $parts[0];
        $appName = $parts[1] ?? 'demo';
        
        // 用户管理 API 不需要 /1.0 前缀
        $url = 'https://' . config('easemob.rest_api') . "/{$orgName}/{$appName}/users/{$username}";
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->withOptions([
            'verify' => false,
        ])->put($url, [
            'nickname' => $nickname,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('更新用户信息失败：' . $response->body());
    }

    /**
     * 删除用户
     * 
     * @param string $username 用户名
     * @return array
     */
    public function deleteUser(string $username)
    {
        $token = $this->getToken();
        
        // 从 appKey 中提取 orgName 和 appName
        $parts = explode('#', $this->appKey);
        $orgName = $parts[0];
        $appName = $parts[1] ?? 'demo';
        
        // 用户管理 API 不需要 /1.0 前缀
        $url = 'https://' . config('easemob.rest_api') . "/{$orgName}/{$appName}/users/{$username}";
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withOptions([
            'verify' => false,
        ])->delete($url);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('删除用户失败：' . $response->body());
    }
}
