<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\MainCategory;
use App\Models\SiteSetting;
use App\Models\Page;

class ShareViewData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Ana kategorileri tüm view'lara paylaş
        view()->share('mainCategories', MainCategory::all());
        
        // Site ayarlarını tüm view'lara paylaş (ilk kaydı al)
        view()->share('siteSettings', SiteSetting::first());
        
        // Sayfaları tüm view'lara paylaş
        view()->share('pages', Page::select('id','title','slug')->get());

        return $next($request);
    }
} 