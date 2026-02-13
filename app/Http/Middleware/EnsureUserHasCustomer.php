<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasCustomer
{
    /**
     * Firma ataması yapılmamış kullanıcıların sipariş sayfasına erişimini engeller.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && !$user->customer_id) {
            return redirect()->route('customer.assignment.required');
        }

        return $next($request);
    }
}
