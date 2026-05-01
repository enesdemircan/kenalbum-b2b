<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiDocsAccessMiddleware
{
    /**
     * /api-docs erişim kontrolü.
     * Yalnızca role_id = 1 (admin) veya role_id = 11 (api kullanıcısı) görüntüleyebilir.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'API dökümantasyonunu görüntülemek için giriş yapmalısınız.');
        }

        $user = Auth::user();
        $allowed = $user->roles()->whereIn('roles.id', [1, 11])->exists();

        if (!$allowed) {
            abort(403, 'API dökümantasyonuna erişim yetkiniz yok. Bu sayfa yalnızca yöneticilere ve API kullanıcılarına açıktır.');
        }

        return $next($request);
    }
}
