<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiRoleMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * Sadece ID 11'li role sahip kullanıcılar API'ye erişebilir
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Kullanıcı authenticate edilmiş mi kontrol et
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Kimlik doğrulama gerekli',
                'error' => 'Unauthenticated'
            ], 401);
        }

        // Kullanıcı ID 11 role sahip mi kontrol et
        $hasApiRole = $request->user()->roles()->where('roles.id', 11)->exists();

        if (!$hasApiRole) {
            return response()->json([
                'success' => false,
                'message' => 'API erişim yetkiniz yok. Bu hizmeti kullanabilmek için API kullanıcısı grubuna dahil olmanız gerekmektedir.',
                'error' => 'Forbidden'
            ], 403);
        }

        return $next($request);
    }
}

