<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class CheckRoutePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       
        // Kullanıcı giriş yapmamışsa login'e yönlendir
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $currentRouteName = Route::currentRouteName();
        $currentMethod = $request->method();
      

       

        


        // Route name yoksa izin ver (API route'ları için)
        if (!$currentRouteName) {
            return $next($request);
        }

      
      
        // Kullanıcının rolleri var mı?
        if (!$user->roles || $user->roles->isEmpty()) {
           
            abort(403, 'Bu sayfaya erişim izniniz yok.');
        }
     
        // Administrator rolü kontrolü - Administrator tüm izinlere sahip
        foreach ($user->roles as $role) {
            if ($role->id === 1 || $role->name === 'administrator' || $role->name === 'Administrator') {
                return $next($request); // Administrator için tüm izinler
            }
        }
    
        // Her role için kontrol et
        foreach ($user->roles as $role) {
            // Route'a erişim izni var mı?
            if ($role->hasRouteAccess($currentRouteName)) {
                return $next($request);
            }
        }
      
        
        // Hiçbir role'de izin yoksa - Administrator bypass
        if ($user->roles()->where('roles.id', 1)->exists()) {
            return $next($request);
        }
        
        // Hiçbir role'de izin yoksa
        abort(403, 'Bu sayfaya erişim izniniz yok.');
    }

    /**
     * HTTP method'dan action türünü belirle
     */
    private function getActionFromMethod($method)
    {
        switch (strtoupper($method)) {
            case 'GET':
                return 'read';
            case 'POST':
                return 'create';
            case 'PUT':
            case 'PATCH':
                return 'update';
            case 'DELETE':
                return 'delete';
            default:
                return null;
        }
    }
}
