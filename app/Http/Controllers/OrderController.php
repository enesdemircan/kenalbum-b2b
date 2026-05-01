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

        // Ana parametreleri al ve kategorilere göre gruplandır.
        // file/files type kategoriler wizard'da render edilmiyor (dosya artık order seviyesinde, checkout'ta).
        $mainCustomizationParams = $product->customizationPivotParams
            ->where('customization_params_ust_id', 0)
            ->filter(function ($pivot) {
                $type = $pivot->param->category->type ?? '';
                return !in_array($type, ['file', 'files'], true);
            })
            ->groupBy('param.customization_category_id');

        // Yeni cascade tespiti: ürün'de pivot'u olan TÜM customization kategorileri
        // step olarak gösterilir. Bir kategorinin tüm pivot'ları cascade ise
        // (ust_id != 0) cascade step olarak işaretlenir; aksi top-level.
        // JS chain filter ANY checked radio'ya göre filtreliyor — tek lineer
        // chain varsayımı (eski kod) yerine multi-branch hierarchy desteği.
        $customizationSteps = collect();
        $authUser = auth()->user();

        $shouldShowHidden = function ($category, $catParams) use ($product, $authUser) {
            if ($category->type !== 'hidden') return true;
            if (!$authUser || !$authUser->customer_id) return false;
            foreach ($catParams as $p) {
                $exists = \App\Models\CustomizationParamsCustomersPivot::where([
                    'customer_id' => $authUser->customer_id,
                    'customization_params_id' => $p->param->id,
                    'product_id' => $product->id,
                ])->exists();
                if ($exists) return true;
            }
            return false;
        };

        // Ürünün TÜM customization kategorileri (pivot'u olan)
        $allCatPivots = $product->customizationPivotParams->groupBy(function ($p) {
            return (int) $p->param->customization_category_id;
        });

        foreach ($allCatPivots as $categoryId => $catPivots) {
            $category = $catPivots->first()->param->category;
            if (!$category) continue;

            if (in_array($category->type, ['file', 'files'])) continue;
            if (!$shouldShowHidden($category, $catPivots)) continue;

            // Cascade mi? Eğer hiç top-level (ust_id=0) pivot'u yoksa → cascade
            $topLevelPivots = $catPivots->where('customization_params_ust_id', 0)->values();
            $isCascade = $topLevelPivots->isEmpty();

            // Cascade step'lerde TÜM pivot'ları (parent_pivot_id'leriyle) ön-render et;
            // top-level step'lerde sadece top-level pivot'ları
            $params = $isCascade ? $catPivots->values() : $topLevelPivots;

            $customizationSteps->push([
                'category' => $category,
                'category_id' => (int) $categoryId,
                'params' => $params,
                'is_cascade' => $isCascade,
                // parent_category_id artık kullanılmıyor (JS chain filter ANY checked radio'ya bakıyor)
                'parent_category_id' => null,
                'step_label' => $category->step_label ?? 'Sipariş Detayı',
                'order' => (int) ($category->order ?? 9999),
                'category_id_for_sort' => (int) $categoryId,
            ]);
        }

        // Sıra: önce category->order, sonra category id (hiyerarşi: Ebat<Kumaş<Renk<Paket<...)
        $customizationSteps = $customizationSteps->sortBy([
            ['order', 'asc'],
            ['category_id_for_sort', 'asc'],
        ])->values();

        // Ekstra ürünler (wizard'ın "Ekstralar" step'i için — eski popup'ın yerine)
        $extraSales = \App\Models\ExtraSale::with('childProduct')
            ->where('main_product_id', $product->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(fn($e) => $e->childProduct !== null)
            ->map(function ($extraSale) {
                return [
                    'id' => $extraSale->childProduct->id,
                    'title' => $extraSale->childProduct->title,
                    'price' => (float) $extraSale->childProduct->price,
                    'images' => $extraSale->childProduct->images,
                    'slug' => $extraSale->childProduct->slug ?? null,
                ];
            })
            ->values();

        $suggestedProducts = $product->getSuggestedProducts();

        $childProducts = collect();
        if ($product->isMainProduct()) {
            $childProducts = $product->childProducts()->where('status', 1)->get();
        }

        return view('frontend.orders.create', compact(
            'product',
            'mainCustomizationParams',
            'customizationSteps',
            'extraSales',
            'suggestedProducts',
            'childProducts'
        ));
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
