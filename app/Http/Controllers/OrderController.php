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

        // Yeni: HER kategori AYRI bir wizard step'i. Cascade child kategorileri de
        // (Ebat → Kumaş → Renk → Paket) kendi step'lerinde gözükecek.
        // step_label sadece görsel grouping (h6) için kullanılıyor.
        $customizationSteps = collect();
        $authUser = auth()->user();
        $processedCategories = [];

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

        foreach ($mainCustomizationParams as $categoryId => $catParams) {
            $category = $catParams->first()->param->category;

            if (in_array($category->type, ['file', 'files'])) continue;
            if (!$shouldShowHidden($category, $catParams)) continue;
            if (in_array((int)$categoryId, $processedCategories)) continue;

            $customizationSteps->push([
                'category' => $category,
                'category_id' => (int)$categoryId,
                'params' => $catParams,
                'is_cascade' => false,
                'parent_category_id' => null,
                'step_label' => $category->step_label ?? 'Sipariş Detayı',
                'order' => (int)($category->order ?? 0),
            ]);
            $processedCategories[] = (int)$categoryId;

            // Cascade chain'i takip et: Ebat → Kumaş → Renk → Paket
            // Bir top-level pivot'tan başlayıp child pivot zincirini izleyerek
            // kategorileri sırayla ekle.
            $currentPivot = $catParams->first();
            $parentCatId = (int)$categoryId;
            while ($currentPivot) {
                $childPivot = $product->customizationPivotParams
                    ->where('customization_params_ust_id', $currentPivot->id)
                    ->first();
                if (!$childPivot) break;
                $childCategory = $childPivot->param->category;
                $childCatId = (int)$childCategory->id;
                if (in_array($childCatId, $processedCategories)) break;
                if (in_array($childCategory->type, ['file', 'files'])) break;
                if (!$shouldShowHidden($childCategory, collect([$childPivot]))) break;

                $customizationSteps->push([
                    'category' => $childCategory,
                    'category_id' => $childCatId,
                    'params' => collect(), // boş — AJAX ile dinamik dolacak
                    'is_cascade' => true,
                    'parent_category_id' => $parentCatId,
                    'step_label' => $childCategory->step_label ?? 'Sipariş Detayı',
                    'order' => (int)($childCategory->order ?? 0),
                ]);
                $processedCategories[] = $childCatId;
                $parentCatId = $childCatId;
                $currentPivot = $childPivot;
            }
        }

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
