<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $localOrigins = [
            'http://localhost:5173',
            'http://localhost:5174',
            'http://localhost:5175',
            'http://localhost:8888',
            'http://localhost',
            'https://bhr.41c.mytemp.website',
            'http://bhr.41c.mytemp.website',
        ];
        
        $origin = $request->header('Origin');
        
        $isAllowed = false;
        if ($origin) {
            if (in_array($origin, $localOrigins)) {
                $isAllowed = true;
            } elseif (preg_match('/^https?:\/\/([a-zA-Z0-9-]+\.)?chatmego\.com$/', $origin)) {
                $isAllowed = true;
            }
        }
        
        if (!$isAllowed) {
            return $next($request);
        }
        
        $allowedOrigin = $origin;
        
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
