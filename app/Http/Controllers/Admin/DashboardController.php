<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MainCategory;
use App\Models\Product;
use App\Models\CustomizationParam;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderStatusHistory;
use App\Models\Customer;
use App\Models\User;
use App\Models\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Temel sayılar
        $mainCategoriesCount = MainCategory::count();
        $productsCount = Product::count();
        $customizationParamsCount = CustomizationParam::count();
        $recentProducts = Product::latest()->take(5)->get();
        
        // Sipariş istatistikleri
        $totalOrders = Order::count();
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
        $monthOrders = Order::whereMonth('created_at', Carbon::now()->month)
                            ->whereYear('created_at', Carbon::now()->year)
                            ->count();
        
        // Sipariş tutarları (iptal edilmemiş siparişler)
        $totalRevenue = Order::where('status', '!=', 3)->sum('total_price');
        $todayRevenue = Order::whereDate('created_at', Carbon::today())
                             ->where('status', '!=', 3)
                             ->sum('total_price');
        $monthRevenue = Order::whereMonth('created_at', Carbon::now()->month)
                             ->whereYear('created_at', Carbon::now()->year)
                             ->where('status', '!=', 3)
                             ->sum('total_price');
        
        // Firma istatistikleri
        $totalCustomers = Customer::count();
        $totalCustomerBalance = Customer::sum('balance');
        $negativeBalanceCustomers = Customer::where('balance', '<', 0)->count();
        $totalCollections = Collection::sum('amount');
        
        // En yüksek bakiyeli firmalar (pozitif bakiye)
        $topCustomersByBalance = Customer::where('balance', '>', 0)
                                        ->orderBy('balance', 'desc')
                                        ->take(5)
                                        ->get();
        
        // En borçlu firmalar (negatif bakiye)
        $mostInDebtCustomers = Customer::where('balance', '<', 0)
                                      ->orderBy('balance', 'asc')
                                      ->take(5)
                                      ->get();
        
        // Son siparişler
        $recentOrders = Order::with(['user.customer', 'orderStatus'])
                             ->latest()
                             ->take(10)
                             ->get();
        
        // Aylık sipariş grafik verisi (son 12 ay)
        $monthlyOrders = Order::select(
                                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                                DB::raw('COUNT(*) as count'),
                                DB::raw('SUM(CASE WHEN status != 3 THEN total_price ELSE 0 END) as revenue')
                            )
                            ->where('created_at', '>=', Carbon::now()->subMonths(12))
                            ->groupBy('month')
                            ->orderBy('month')
                            ->get();
        
        // Ana sipariş durumları (orders tablosu - 4 durum)
        // 0: Onay Bekliyor, 1: İşlemde, 2: Teslim Edildi, 3: İptal
        $ordersByMainStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Alt sipariş durumları (cart bazlı - OrderStatusHistory'den)
        $latestHistoryIds = DB::table('order_status_histories')
            ->select(DB::raw('MAX(id) as id'))
            ->whereNotNull('cart_id')
            ->groupBy('cart_id')
            ->pluck('id');

        $cartsByStatus = OrderStatusHistory::whereIn('id', $latestHistoryIds)
            ->select('order_status_id as status', DB::raw('COUNT(*) as count'))
            ->groupBy('order_status_id')
            ->get();

        $orderStatusTitles = OrderStatus::pluck('title', 'id')->toArray();
        
        // Kullanıcı sayısı
        $totalUsers = User::count();
        $activeUsers = User::where('status', 1)->count();
        
        return view('admin.dashboard', compact(
            'mainCategoriesCount',
            'productsCount',
            'customizationParamsCount',
            'recentProducts',
            'totalOrders',
            'todayOrders',
            'monthOrders',
            'totalRevenue',
            'todayRevenue',
            'monthRevenue',
            'totalCustomers',
            'totalCustomerBalance',
            'negativeBalanceCustomers',
            'totalCollections',
            'topCustomersByBalance',
            'mostInDebtCustomers',
            'recentOrders',
            'monthlyOrders',
            'ordersByMainStatus',
            'cartsByStatus',
            'orderStatusTitles',
            'totalUsers',
            'activeUsers'
        ));
    }
}
