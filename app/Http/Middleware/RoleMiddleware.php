<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Bu alana erişim yetkiniz yok.');
        }

        // Administrator rolü kontrolü - Administrator tüm izinlere sahip
        foreach ($user->roles as $role) {
            if ($role->name === 'administrator' || $role->name === 'Administrator') {
                return $next($request); // Administrator için tüm izinler
            }
        }
        
        // Diğer roller için normal kontrol
        if (!$user->roles()->whereIn('name', $roles)->exists()) {
            abort(403, 'Bu alana erişim yetkiniz yok.');
        }
        
        return $next($request);
    }
}
