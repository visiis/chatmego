<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * 手机号密码登录
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => '参数错误', 'errors' => $validator->errors()], 400);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => '手机号或密码错误'], 401);
        }

        if ($user->status === 'pending') {
            return response()->json(['message' => '账号待审核'], 403);
        }

        if (!$user->is_active) {
            return response()->json(['message' => '账号已被禁用'], 403);
        }

        // 生成或更新 token
        $token = $this->generateToken($user);

        return response()->json([
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
            'phone' => 'required|string|unique:users',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:6',
            'nickname' => 'required|string|max:20',
            'gender' => 'required|integer|in:0,1,2',
            'birthday' => 'required|date',
            'invite_code' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => '参数错误', 'errors' => $validator->errors()], 400);
        }

        // 验证验证码
        $cachedCode = cache()->get('sms_code_' . $request->phone);
        if (!$cachedCode || $cachedCode !== $request->code) {
            return response()->json(['message' => '验证码错误或已过期'], 401);
        }

        // 创建用户
        $user = User::create([
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'name' => $request->nickname,
            'gender' => $request->gender,
            'birthday' => $request->birthday,
            'points' => 100,
            'total_points_earned' => 100,
            'coins' => 0,
            'is_active' => true,
            'status' => 'active',
        ]);

        // 生成 token
        $token = $this->generateToken($user);

        return response()->json([
            'message' => '注册成功',
            'data' => [
                'user' => $this->formatUser($user),
                'token' => $token,
                'im' => [
                    'account' => 'user_' . $user->id,
                    'token' => $this->generateImToken($user->id)
                ]
            ]
        ], 201);
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
        return [
            'id' => $user->id,
            'phone' => $user->phone,
            'name' => $user->name,
            'avatar' => $user->avatar_url,
            'gender' => $user->gender,
            'age' => $user->age,
            'height' => $user->height,
            'weight' => $user->weight,
            'hobbies' => $user->hobbies,
            'specialty' => $user->specialty,
            'love_declaration' => $user->love_declaration,
            'points' => $user->points,
            'total_points_earned' => $user->total_points_earned,
            'coins' => $user->coins,
            'current_level' => $user->current_level ? [
                'name' => $user->current_level->name,
                'icon' => $user->current_level->icon,
                'level_order' => $user->current_level->level_order
            ] : null,
            'has_membership' => $user->hasActiveMembership(),
            'membership' => $user->current_membership ? [
                'name' => $user->current_membership->name,
                'code' => $user->current_membership->code,
                'expired_at' => $user->current_membership->expired_at
            ] : null,
            'token' => $user->api_token,
            'created_at' => $user->created_at->toISOString()
        ];
    }
}
