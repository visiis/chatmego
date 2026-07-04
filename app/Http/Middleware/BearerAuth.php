<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BearerAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['code' => 401, 'message' => '未授权'], 401);
        }

        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(['code' => 401, 'message' => '无效的token'], 401);
        }

        auth()->guard('api')->setUser($user);

        return $next($request);
    }
}