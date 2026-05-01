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



    /**
     * Hızlı sipariş modal'ında "Geçmişim" tab'ı için kullanıcının önceden
     * verdiği siparişlerin (status > 0) ürün+customization özetini döner.
     * Aynı product için en güncel cart'ı tutar; en fazla 30 kayıt.
     * Cart bağımlılıklarına sahip 'duplicate' ve 'order_url(?from_cart=...)'
     * action'larıyla geri.
     */
    public function orderHistory(Request $request)
    {
        if (!auth()->check()) return response()->json(['items' => []]);

        $userId = auth()->id();

        // En güncel cart_id'leri product_id başına grupla
        $cartIds = \DB::table('carts')
            ->select(\DB::raw('MAX(id) as id'))
            ->where('user_id', $userId)
            ->where('status', '>', 0)
            ->groupBy('product_id')
            ->orderByDesc(\DB::raw('MAX(created_at)'))
            ->limit(30)
            ->pluck('id');

        if ($cartIds->isEmpty()) return response()->json(['items' => []]);

        $carts = \App\Models\Cart::with('product')
            ->whereIn('id', $cartIds)
            ->orderByDesc('id')
            ->get();

        $items = $carts->map(function ($cart) {
            if (!$cart->product) return null;

            $notes = $cart->notes ? json_decode($cart->notes, true) : [];
            $customizations = $notes['customizations'] ?? [];

            $summary = [];
            foreach ($customizations as $catId => $c) {
                $cat = \App\Models\CustomizationCategory::find($catId);
                if (!$cat) continue;
                $type = $c['type'] ?? null;
                $value = '';
                if (in_array($type, ['radio', 'select', 'hidden'], true)) {
                    $pivot = \App\Models\CustomizationPivotParam::with('param')->find($c['value'] ?? null);
                    if ($pivot && $pivot->param) $value = $pivot->param->key;
                } elseif ($type === 'checkbox' && !empty($c['values'])) {
                    $names = collect($c['values'])->map(function ($pid) {
                        $p = \App\Models\CustomizationPivotParam::with('param')->find($pid);
                        return $p?->param?->key;
                    })->filter()->values();
                    $value = $names->join(', ');
                } elseif ($type === 'input') {
                    $value = $c['value'] ?? '';
                }
                if ($value !== '') $summary[] = $cat->title . ': ' . $value;
            }

            $img = null;
            if (is_array($cart->product->images)) {
                $img = $cart->product->images[0] ?? null;
            } elseif (is_string($cart->product->images) && $cart->product->images !== '') {
                $img = explode(',', $cart->product->images)[0];
            }

            return [
                'cart_id'        => $cart->id,
                'product_id'     => $cart->product->id,
                'product_title'  => $cart->product->title,
                'product_image'  => $img,
                'page_count'     => (int) $cart->page_count,
                'summary'        => $summary,
                'created_at'     => $cart->created_at?->format('d.m.Y'),
                'order_url'      => route('products.ordercreate.id', $cart->product->id) . '?from_cart=' . $cart->id,
                'duplicate_url'  => route('cart.duplicate', $cart->id),
            ];
        })->filter()->values();

        return response()->json(['items' => $items]);
    }

    /**
     * Header "Sipariş Ver" modal'ı için ana ürün listesi JSON.
     * Kategori (main_category_id) ve search (title/tags LIKE) filtreleri.
     * Çocuk ürünler (ust_id != null) listelenmiyor — ana ürünler hızlı sipariş için.
     */
    public function productPicker(Request $request)
    {
        $query = Product::with('mainCategory')
            ->where('status', 1)
            ->whereNull('ust_id')
            ->orderBy('order')
            ->orderBy('title');

        if ($categoryId = $request->input('category')) {
            // Hierarchy: ürünlerin main_category_id'si genellikle alt-kategoride.
            // Üst kategori seçilince TÜM alt kategorileri de dahil et (1 seviye).
            $catIds = [(int) $categoryId];
            $children = \App\Models\MainCategory::where('ust_id', (int) $categoryId)->pluck('id')->toArray();
            if (!empty($children)) $catIds = array_merge($catIds, $children);
            $query->whereIn('main_category_id', $catIds);
        }

        if ($search = trim((string) $request->input('search'))) {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('title', 'like', $like)
                  ->orWhere('tags', 'like', $like);
            });
        }

        $products = $query->limit(60)->get()->map(function ($p) {
            $img = null;
            if (is_array($p->images)) {
                $img = $p->images[0] ?? null;
            } elseif (is_string($p->images) && $p->images !== '') {
                $img = explode(',', $p->images)[0];
            }
            return [
                'id'        => $p->id,
                'title'     => $p->title,
                'slug'      => $p->slug,
                'image'     => $img,
                'category'  => $p->mainCategory?->title,
                'order_url' => route('products.ordercreate.id', $p->id),
            ];
        });

        return response()->json(['products' => $products]);
    }

    /**
     * Ekstra ürün modal'ı için flat customization formu HTML'i.
     * Ana wizard'a kıyasla file/files/hidden tipleri atlanır,
     * Diğer tab'ı sabit alanları yok — sadece ekstrayı sepete eklemek
     * için minimum gerekli alanlar.
     */
    public function extraForm($id)
    {
        $product = Product::with(['customizationPivotParams.param.category'])->findOrFail($id);

        $allCatPivots = $product->customizationPivotParams->groupBy(fn($p) => (int) $p->param->customization_category_id);

        $categories = [];
        foreach ($allCatPivots as $categoryId => $catPivots) {
            $category = $catPivots->first()->param->category;
            if (!$category) continue;
            if (in_array($category->type, ['file', 'files', 'hidden'], true)) continue;

            $topLevelPivots = $catPivots->where('customization_params_ust_id', 0)->values();
            $isCascade = $topLevelPivots->isEmpty();
            $params = $isCascade ? $catPivots->values() : $topLevelPivots;

            $categories[] = [
                'category' => $category,
                'params' => $params,
                'order' => (int) ($category->order ?? 9999),
            ];
        }
        usort($categories, fn($a, $b) => $a['order'] <=> $b['order']);

        return view('frontend.orders.partials.extra-customize-form', compact('product', 'categories'));
    }

    public function ordercreateById($id, Request $request)
    {
        // GET request ise sipariş form sayfasını göster
        $product = Product::with(['customizationPivotParams.param.category', 'childProducts'])->findOrFail($id);

        // Prefill: ?from_cart={cart_id} ile gelen geçmiş cart'ın customization
        // verileri JS'e expose edilir, DOMContentLoaded'da form doldurulur.
        $prefill = null;
        if ($fromCartId = $request->query('from_cart')) {
            $oldCart = \App\Models\Cart::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->find($fromCartId);
            if ($oldCart && $oldCart->notes) {
                $oldNotes = json_decode($oldCart->notes, true) ?: [];
                $prefill = [
                    'page_count' => (int) ($oldCart->page_count ?? 0),
                    'customizations' => $oldNotes['customizations'] ?? [],
                    'urgent_production' => !empty($oldNotes['urgent_production']),
                    'design_service' => $oldNotes['design_service'] ?? null,
                    'order_note' => $oldNotes['order_note'] ?? null,
                ];
            }
        }

        // Ana parametreleri al ve kategorilere göre gruplandır.
        // file/files type kategoriler wizard'da render edilmiyor (dosya artık order seviyesinde, checkout'ta).
        $mainCustomizationParams = $product->customizationPivotParams
            ->where('customization_params_ust_id', 0)
            ->filter(function ($pivot) {
                $type = $pivot->param->category->type ?? '';
                return !in_array($type, ['file', 'files'], true);
            })
            ->groupBy('param.customization_category_id');

        // Wizard step'leri customization_categories.step_label'a göre GRUPLANIR.
        // Aynı step_label'a sahip kategoriler tek bir wizard tab'ında görünür
        // (admin "Özel" gibi bir grup yapabilir). Farklı label = ayrı tab.
        // JS chain filter (refreshCascadeChain) ANY checked radio'ya göre çalışır,
        // multi-branch hierarchy + grup yapısı destekli.
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

        // Önce her kategori için step bilgisi toplayıp, sonra step_label'a göre grupla.
        $perCategory = [];
        foreach ($allCatPivots as $categoryId => $catPivots) {
            $category = $catPivots->first()->param->category;
            if (!$category) continue;

            if (in_array($category->type, ['file', 'files'])) continue;
            if (!$shouldShowHidden($category, $catPivots)) continue;

            $topLevelPivots = $catPivots->where('customization_params_ust_id', 0)->values();
            $isCascade = $topLevelPivots->isEmpty();
            $params = $isCascade ? $catPivots->values() : $topLevelPivots;

            $perCategory[] = [
                'category' => $category,
                'category_id' => (int) $categoryId,
                'params' => $params,
                'is_cascade' => $isCascade,
                'step_label' => $category->step_label ?: ('Kategori ' . $categoryId),
                'order' => (int) ($category->order ?? 9999),
            ];
        }

        // step_label'a göre grupla — aynı label = aynı wizard step (tek tab, birden fazla section)
        $byLabel = [];
        foreach ($perCategory as $catInfo) {
            $label = $catInfo['step_label'];
            if (!isset($byLabel[$label])) {
                $byLabel[$label] = [
                    'step_label' => $label,
                    'categories' => [],
                    'is_cascade' => false,
                    'min_order' => 99999,
                    'min_cat_id' => 99999,
                ];
            }
            $byLabel[$label]['categories'][] = $catInfo;
            if ($catInfo['is_cascade']) $byLabel[$label]['is_cascade'] = true;
            $byLabel[$label]['min_order'] = min($byLabel[$label]['min_order'], $catInfo['order']);
            $byLabel[$label]['min_cat_id'] = min($byLabel[$label]['min_cat_id'], $catInfo['category_id']);
        }

        // "Diğer" tab her sipariş formunda görünmek zorunda — hardcoded
        // alanlar (Tasarım Hizmeti, Acil Üretim, Sipariş Notu) burada render
        // edilir. Eğer ürünün step_label='Diğer' ile eşleşen customization'ı
        // yoksa synthetic bir step ekle.
        if (!isset($byLabel['Diğer'])) {
            $byLabel['Diğer'] = [
                'step_label' => 'Diğer',
                'categories' => [],
                'is_cascade' => false,
                'min_order' => 9998,
                'min_cat_id' => 99998,
            ];
        }

        $customizationSteps = collect(array_values($byLabel))->sortBy([
            ['min_order', 'asc'],
            ['min_cat_id', 'asc'],
        ])->values();

        // Ekstra ürünler (wizard'ın "Ekstralar" step'i için).
        // has_customization flag: child product'ın file/files olmayan customization'ı
        // var mı? Varsa kart üzerinde "Özelleştir ve Ekle" modalı tetiklenir,
        // yoksa direkt +/- ile sepete eklenir.
        $extraSales = \App\Models\ExtraSale::with('childProduct.customizationPivotParams.param.category')
            ->where('main_product_id', $product->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(fn($e) => $e->childProduct !== null)
            ->map(function ($extraSale) {
                $child = $extraSale->childProduct;
                $hasCustomization = $child->customizationPivotParams
                    ->filter(fn($p) => !in_array($p->param->category->type ?? '', ['file', 'files', 'hidden'], true))
                    ->isNotEmpty();
                return [
                    'id' => $child->id,
                    'title' => $child->title,
                    'price' => (float) $child->price,
                    'images' => $child->images,
                    'slug' => $child->slug ?? null,
                    'has_customization' => $hasCustomization,
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
            'childProducts',
            'prefill'
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
