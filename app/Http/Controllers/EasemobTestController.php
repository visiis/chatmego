<?php

namespace App\Http\Controllers;

use App\Services\EasemobService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EasemobTestController extends Controller
{
    protected $easemobService;

    public function __construct(EasemobService $easemobService)
    {
        $this->easemobService = $easemobService;
    }

    /**
     * 显示测试页面
     */
    public function index()
    {
        return view('easemob-test');
    }

    /**
     * 测试获取 Token
     */
    public function testToken()
    {
        try {
            $token = $this->easemobService->getToken();
            return response()->json([
                'success' => true,
                'message' => '获取 Token 成功',
                'token' => substr($token, 0, 20) . '...',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 测试注册环信用户
     */
    public function testRegister(Request $request)
    {
        try {
            $username = 'test_' . time();
            $password = 'password123';
            $nickname = '测试用户';

            $result = $this->easemobService->registerUser($username, $password, $nickname);

            return response()->json([
                'success' => true,
                'message' => '注册环信用户成功',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 测试发送消息
     */
    public function testSendMessage(Request $request)
    {
        try {
            $from = $request->input('from', 'admin');
            $to = $request->input('to', 'test_user');
            $message = $request->input('message', '这是一条测试消息');

            $result = $this->easemobService->sendPrivateMessage($from, $to, $message);

            return response()->json([
                'success' => true,
                'message' => '发送消息成功',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 测试获取用户信息
     */
    public function testGetUserInfo(Request $request)
    {
        try {
            $username = $request->input('username', 'admin');

            $result = $this->easemobService->getUserInfo($username);

            return response()->json([
                'success' => true,
                'message' => '获取用户信息成功',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 同步用户到环信（将数据库用户注册到环信）
     */
    public function syncUsers()
    {
        try {
            $users = User::all();
            $success = [];
            $failed = [];

            foreach ($users as $user) {
                try {
                    // 使用用户 ID 作为环信用户名
                    $username = 'user_' . $user->id;
                    
                    // 检查用户是否已存在
                    try {
                        $this->easemobService->getUserInfo($username);
                        $success[] = [
                            'user_id' => $user->id,
                            'username' => $username,
                            'status' => 'exists',
                        ];
                    } catch (\Exception $e) {
                        // 用户不存在，注册
                        $this->easemobService->registerUser(
                            $username,
                            'password123', // 默认密码
                            $user->name
                        );
                        
                        $success[] = [
                            'user_id' => $user->id,
                            'username' => $username,
                            'status' => 'registered',
                        ];
                    }
                } catch (\Exception $e) {
                    $failed[] = [
                        'user_id' => $user->id,
                        'username' => $username,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => '用户同步完成',
                'data' => [
                    'total' => count($users),
                    'success_count' => count($success),
                    'failed_count' => count($failed),
                    'success_list' => $success,
                    'failed_list' => $failed,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
