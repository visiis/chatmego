<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EasemobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * 邮箱密码登录
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => '参数错误', 'errors' => $validator->errors()], 400);
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => '邮箱或密码错误'], 401);
        }

        if ($user->status === 'pending') {
            return response()->json(['message' => '账号待审核'], 403);
        }

        if (!$user->is_active) {
            return response()->json(['message' => '账号已被禁用'], 403);
        }

        // 生成或更新 token
        $token = $this->generateToken($user);

        // 检查并确保环信用户存在
        $this->ensureEasemobUser($user);

        return response()->json([
            'code' => 200,
            'message' => '登录成功',
            'data' => [
                'user' => $this->formatUser($user),
                'token' => $token,
                'im' => [
                    'account' => 'user_' . $user->id,
                    'token' => $this->generateImToken($user->id)
                ]
            ]
        ])->cookie('api_token', $token, 60 * 24 * 7);
    }

    protected function ensureEasemobUser(User $user)
    {
        try {
            $easemobService = app(EasemobService::class);
            $username = 'user_' . $user->id;
            
            try {
                $easemobService->getUserInfo($username);
            } catch (\Exception $e) {
                $password = bin2hex(random_bytes(16));
                $easemobService->registerUser($username, $password, $user->name);
                
                Log::info('登录时自动注册环信用户', [
                    'user_id' => $user->id,
                    'username' => $username,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('环信用户检查/注册失败', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 验证码登录
     */
    public function loginByCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => '参数错误', 'errors' => $validator->errors()], 400);
        }

        // 验证验证码（简化处理，实际应从缓存获取）
        $cachedCode = cache()->get('sms_code_' . $request->phone);
        if (!$cachedCode || $cachedCode !== $request->code) {
            return response()->json(['message' => '验证码错误或已过期'], 401);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json(['message' => '用户不存在'], 404);
        }

        if ($user->status === 'pending') {
            return response()->json(['message' => '账号待审核'], 403);
        }

        // 生成或更新 token
        $token = $this->generateToken($user);

        return response()->json([
            'code' => 200,
            'message' => '登录成功',
            'data' => [
                'user' => $this->formatUser($user),
                'token' => $token,
                'im' => [
                    'account' => 'user_' . $user->id,
                    'token' => $this->generateImToken($user->id)
                ]
            ]
        ]);
    }

    /**
     * 用户注册
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'gender' => 'nullable|string|in:male,female',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => '参数错误', 'errors' => $validator->errors()], 400);
        }

        $isActive = $request->gender === 'male' ? 1 : 0;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gender' => $request->gender === 'male' ? 'male' : ($request->gender === 'female' ? 'female' : ''),
            'is_active' => $isActive,
            'status' => $isActive ? 'active' : 'pending',
        ]);

        $token = $this->generateToken($user);

        return response()->json([
            'code' => 200,
            'message' => '注册成功',
            'data' => [
                'user' => $this->formatUser($user),
                'token' => $token,
                'im' => [
                    'account' => 'user_' . $user->id,
                    'token' => $this->generateImToken($user->id)
                ]
            ]
        ]);
    }

    /**
     * 发送验证码
     */
    public function sendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'type' => 'required|string|in:register,login',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => '参数错误', 'errors' => $validator->errors()], 400);
        }

        // 检查用户是否存在（注册时不应存在，登录时应存在）
        $userExists = User::where('phone', $request->phone)->exists();
        
        if ($request->type === 'register' && $userExists) {
            return response()->json(['message' => '该手机号已注册'], 400);
        }

        if ($request->type === 'login' && !$userExists) {
            return response()->json(['message' => '用户不存在'], 404);
        }

        // 生成验证码
        $code = rand(100000, 999999);
        
        // 缓存验证码（5分钟有效期）
        cache()->put('sms_code_' . $request->phone, $code, 300);

        // 模拟发送验证码
        \Log::info('发送验证码: ' . $code . ' 到: ' . $request->phone);

        return response()->json([
            'message' => '验证码已发送',
            'data' => [
                'phone' => $request->phone,
                'code' => $code // 测试环境直接返回验证码，生产环境应删除此行
            ]
        ]);
    }

    /**
     * 刷新 token
     */
    public function refresh(Request $request)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $token = $this->generateToken($user);

        return response()->json([
            'message' => '刷新成功',
            'data' => [
                'user' => $this->formatUser($user),
                'token' => $token
            ]
        ]);
    }

    /**
     * 退出登录
     */
    public function logout(Request $request)
    {
        $user = auth()->guard('api')->user();
        
        if ($user) {
            $user->api_token = null;
            $user->save();
        }

        return response()->json(['message' => '退出成功']);
    }

    /**
     * 生成 API token
     */
    protected function generateToken(User $user)
    {
        $token = Str::random(60);
        
        $user->api_token = $token;
        $user->save();
        
        return $token;
    }

    protected function decryptPassword($encryptedPassword)
    {
        try {
            $decoded = base64_decode($encryptedPassword);
            $decrypted = '';
            for ($i = 0; $i < strlen($decoded); $i++) {
                $decrypted .= chr(ord($decoded[$i]) - 1);
            }
            return $decrypted;
        } catch (\Exception $e) {
            return $encryptedPassword;
        }
    }

    /**
     * 生成 IM token
     */
    protected function generateImToken($userId)
    {
        // 生成简单的 IM token
        return md5('user_' . $userId . '_' . time());
    }

    /**
     * 格式化用户数据
     */
    protected function formatUser(User $user)
    {
        $isVip = $user->isVip();
        
        return [
            'id' => $user->id,
            'phone' => $user->phone,
            'nickname' => $user->name,
            'avatar' => $user->avatar_url,
            'gender' => $user->gender === 'male' ? 'male' : ($user->gender === 'female' ? 'female' : ''),
            'birthday' => $user->birthday ?: '',
            'bio' => '',
            'love_declaration' => $user->love_declaration ?: '',
            'location' => '',
            'height' => (int)$user->height ?: 0,
            'weight' => (int)$user->weight ?: 0,
            'zodiac' => '',
            'marital_status' => '',
            'personality' => '',
            'hobbies' => $user->hobbies ?: '',
            'looking_for' => '',
            'ideal_partner' => '',
            'is_vip' => $isVip ? 1 : 0,
            'vip_level' => $isVip ? ($user->current_membership?->code === 'svip' ? 2 : 1) : 0,
            'vip_expire_at' => $user->current_membership?->expired_at ?: '',
            'points' => [
                'balance' => $user->points ?: 0,
                'total_earned' => $user->total_points_earned ?: 0,
                'total_spent' => 0
            ],
            'photos' => []
        ];
    }
}
