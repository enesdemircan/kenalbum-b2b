<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApprovalMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if ($user) {
            // Kullanıcının status'u 0 ve admin rolü yoksa onay bekliyor
            if ($user->status == 0 && !$user->roles()->where('roles.id', 1)->exists()) {
                // Onay bekliyor sayfasına yönlendir
                return redirect()->route('approval.pending');
            }
        }
        
        return $next($request);
    }
} 