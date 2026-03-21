<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 检查用户是否已登录且账户未激活
        if (auth()->check() && !auth()->user()->is_active) {
            // 允许访问待激活页面和退出登录
            if ($request->routeIs('account.pending') || $request->routeIs('logout')) {
                return $next($request);
            }
            
            // 其他所有请求都重定向到待激活页面
            return redirect()->route('account.pending');
        }
        
        return $next($request);
    }
}
