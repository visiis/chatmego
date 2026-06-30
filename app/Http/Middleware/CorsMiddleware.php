<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedOrigins = [
            'http://localhost:5173',
            'http://localhost:5174',
            'http://localhost:5175',
            'http://localhost:8888',
            'https://chatmego.com',
            'https://m.chatmego.com',
            'http://localhost',
            'https://bhr.41c.mytemp.website',
            'http://bhr.41c.mytemp.website',
        ];
        
        $origin = $request->header('Origin');
        
        if ($origin && (in_array($origin, $allowedOrigins) || str_ends_with($origin, '.chatmego.com'))) {
            $allowedOrigin = $origin;
        } else {
            return $next($request);
        }
        
        if ($request->isMethod('OPTIONS')) {
            $response = new Response();
            $response->headers->set('Access-Control-Allow-Origin', $allowedOrigin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Max-Age', '86400');
            return $response;
        }
        
        $response = $next($request);
        
        $response->headers->set('Access-Control-Allow-Origin', $allowedOrigin);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        
        return $response;
    }
}
