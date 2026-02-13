<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MainCategory;
use App\Models\Product;
use App\Models\CustomizationParam;

class DashboardController extends Controller
{
    public function index()
    {
        $mainCategoriesCount = MainCategory::count();
        $productsCount = Product::count();
        $customizationParamsCount = CustomizationParam::count();
        $recentProducts = Product::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'mainCategoriesCount',
            'productsCount',
            'customizationParamsCount',
            'recentProducts'
        ));
    }
}
