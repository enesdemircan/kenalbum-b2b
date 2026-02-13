<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;

class OrderController extends Controller
{
    public function show($id)
    {

        $order = Order::with(['cartItems.product', 'cartItems.statusHistories.orderStatus'])->findOrFail($id);
        
        // Sadece siparişin sahibi veya admin/editor görebilsin
        
        if (Auth::user()->customer_id !== User::find($order->user_id)->customer_id) {
            abort(403, 'Bu siparişi görüntüleme yetkiniz yok.');
        }

        // Her cart item için notes verilerini parse et ve acil üretim fiyatlarını hesapla
        $totalUrgentPrice = 0;
        foreach ($order->cartItems as $item) {
            if ($item->notes) {
                $item->parsed_notes = json_decode($item->notes, true);
                
                // Acil üretim fiyatını hesapla
                if (isset($item->parsed_notes['urgent_production']) && $item->parsed_notes['urgent_production']) {
                    $totalUrgentPrice += $item->parsed_notes['urgent_price'] ?? 0;
                }
                
                // Debug: Notes verilerini logla
                \Log::info('Cart item notes for order show:', [
                    'cart_id' => $item->id,
                    'notes' => $item->notes,
                    'parsed_notes' => $item->parsed_notes
                ]);
            }
        }
        
        return view('frontend.orders.show', compact('order', 'totalUrgentPrice'));
    }



    public function ordercreateById($id)
    {
        
    
        // GET request ise sipariş form sayfasını göster
        $product = Product::with(['customizationPivotParams.param.category', 'childProducts'])->findOrFail($id);
        
        // Ana parametreleri al (customization_params_ust_id = 0 olanlar) ve kategorilere göre gruplandır
        $mainCustomizationParams = $product->customizationPivotParams
            ->where('customization_params_ust_id', 0)
            ->groupBy('param.customization_category_id');
        
            $suggestedProducts = $product->getSuggestedProducts();
        
            // Alt ürünleri al (eğer bu ürün ana ürünse)
            $childProducts = collect();
            if ($product->isMainProduct()) {
                $childProducts = $product->childProducts()->where('status', 1)->get();
            }
            
            return view('frontend.orders.create', compact('product', 'mainCustomizationParams', 'suggestedProducts', 'childProducts'));
    }

 



    /**
     * Sipariş oluştur
     */
    public function create(Request $request)
    {
       
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'page_count' => 'required|integer|min:1',
                'customizations' => 'required|array',
                'customization_files_data' => 'nullable|array'
            ]);

            $product = Product::findOrFail($request->product_id);
            
            // Customization verilerini işle
            $customizationDetails = $this->processCustomizationData($request, $product);
            
            // Toplam fiyatı hesapla
            $totalPrice = $this->calculateTotalPrice($request, $product, $customizationDetails);
            
            // Siparişi oluştur
            $order = \App\Models\Order::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'customizations' => $customizationDetails,
                'page_count' => $request->page_count,
                'total_price' => $totalPrice,
                'status' => 0 // İşlemde
            ]);
            
            // Sipariş durumu geçmişi ekle
            // Order oluşturulduktan sonra cart'lar için OrderStatusHistory eklenmeli
            // Bu kısım cart'lar oluşturulduğunda yapılacak
            
            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Siparişiniz başarıyla oluşturuldu!');
                
        } catch (\Exception $e) {
            \Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Sipariş oluşturulurken bir hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }
    

}
