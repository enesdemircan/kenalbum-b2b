<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\CartFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\CustomizationPivotParam;
use App\Jobs\ProcessCustomizationFiles;

use App\Services\MailService; 
use ZipArchive;
use Intervention\Image\ImageManager;

class CartController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Sepeti görüntülemek için giriş yapmalısınız.');
        }

        $cartItems = Auth::user()->cart()->where('status', 0)->with(['product', 'discountGroup'])->get();
        
        // Ara toplam ve indirim hesapla
        $subtotal = 0;
        $totalDiscount = 0;
        $finalTotal = 0;
        
        foreach ($cartItems as $item) {
            // Ürünün orijinal fiyatını al (original_price alanından)
            $originalPrice = $item->original_price;
            $discountedPrice = $item->price;
            
            $originalTotal = $originalPrice * $item->quantity;
            $discountedTotal = $discountedPrice * $item->quantity;
            
            $subtotal += $originalPrice; // Orijinal fiyatlar toplamı
            $totalDiscount += ($originalTotal - $discountedTotal);
            $finalTotal += $discountedTotal; // İndirimli fiyatlar toplamı
            
            // Debug için detaylı log
            \Log::info("Cart Item {$item->id}: Original: {$originalPrice}, Discounted: {$discountedPrice}, Qty: {$item->quantity}, OriginalTotal: {$originalTotal}, DiscountedTotal: {$discountedTotal}, Discount: " . ($originalTotal - $discountedTotal));
        }
    
        
        \Log::info("Final calculation: Subtotal: {$subtotal}, TotalDiscount: {$totalDiscount}, FinalTotal: {$finalTotal}");
        
        return view('frontend.cart.index', compact('cartItems', 'subtotal', 'totalDiscount', 'finalTotal'));
    }
    
    public function checkout()  
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Sipariş vermek için giriş yapmalısınız.');
        }

        $user = Auth::user();
                    $cartItems = $user->cart()->where('status', 0)->with(['product', 'discountGroup'])->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Sepetiniz boş.');
        }

        $subtotal = 0;
        $totalDiscount = 0;
        $totalUrgentPrice = 0;
        
        foreach ($cartItems as $item) {
            $originalTotal = $item->original_price * $item->quantity;
            $discountedTotal = $item->price * $item->quantity;
            
            $subtotal += $originalTotal;
            $totalDiscount += ($originalTotal - $discountedTotal);
            
            // Acil üretim fiyatını hesapla
            if ($item->notes) {
                $notes = json_decode($item->notes, true);
                if (isset($notes['urgent_price'])) {
                    $totalUrgentPrice += $notes['urgent_price'];
                }
            }
        }
        
        $total = $subtotal - $totalDiscount + $totalUrgentPrice;

        // Kullanıcının adreslerini getir
        $addresses = $user->addresses()->orderBy('created_at', 'desc')->get();

        // Dosya zorunluluğu: s3_zip boş olan ve ürünü zorunlu file/files kategorisi içeren itemları listele
        $missingFileItems = [];
        $hasIsRequired = \Schema::hasColumn('customization_pivot_params', 'is_required');
        if ($hasIsRequired) {
            foreach ($cartItems as $item) {
                if (!empty($item->s3_zip)) {
                    continue;
                }

                $requiresFile = \DB::table('customization_pivot_params as cpp')
                    ->join('customization_categories as cc', 'cc.id', '=', 'cpp.customization_category_id')
                    ->where('cpp.product_id', $item->product_id)
                    ->where('cpp.is_required', 1)
                    ->whereIn('cc.type', ['file', 'files'])
                    ->exists();

                if ($requiresFile) {
                    $missingFileItems[] = $item->product->title ?? 'Ürün';
                }
            }
        }

        return view('frontend.cart.checkout', compact('cartItems', 'subtotal', 'total', 'totalDiscount', 'totalUrgentPrice', 'addresses', 'missingFileItems'));
    }
    
    public function complete(Request $request)
    {
    
        if ($request->isMethod('post')) {

            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Sipariş vermek için giriş yapmalısınız.');
            }

            $request->validate([
                'customer_name' => 'required|string|min:2|max:255|regex:/^[A-Za-zğüşıöçĞÜŞİÖÇ\s]+$/',
                'customer_surname' => 'required|string|min:2|max:255|regex:/^[A-Za-zğüşıöçĞÜŞİÖÇ\s]+$/',
                'customer_phone' => 'required|string|min:10|max:20|regex:/^[0-9\s\-\+\(\)]+$/',
                'city' => 'required|string|max:255',
                'district' => 'required|string|max:255',
                'shipping_address' => 'required|string|min:10|max:1000',
                'payment_method' => 'required|in:bakiye'
            ], [
                'customer_name.required' => 'Ad alanı zorunludur.',
                'customer_name.min' => 'Ad en az 2 karakter olmalıdır.',
                'customer_name.max' => 'Ad 255 karakterden uzun olamaz.',
                'customer_name.regex' => 'Ad sadece harf ve boşluk karakterleri içerebilir.',
                'customer_surname.required' => 'Soyad alanı zorunludur.',
                'customer_surname.min' => 'Soyad en az 2 karakter olmalıdır.',
                'customer_surname.max' => 'Soyad 255 karakterden uzun olamaz.',
                'customer_surname.regex' => 'Soyad sadece harf ve boşluk karakterleri içerebilir.',
                'customer_phone.required' => 'Telefon alanı zorunludur.',
                'customer_phone.min' => 'Telefon numarası en az 10 karakter olmalıdır.',
                'customer_phone.max' => 'Telefon numarası 20 karakterden uzun olamaz.',
                'customer_phone.regex' => 'Geçerli bir telefon numarası giriniz.',
                'city.required' => 'İl seçimi zorunludur.',
                'city.max' => 'İl adı 255 karakterden uzun olamaz.',
                'district.required' => 'İlçe seçimi zorunludur.',
                'district.max' => 'İlçe adı 255 karakterden uzun olamaz.',
                'shipping_address.required' => 'Teslimat adresi zorunludur.',
                'shipping_address.min' => 'Teslimat adresi en az 10 karakter olmalıdır.',
                'shipping_address.max' => 'Teslimat adresi 1000 karakterden uzun olamaz.',
                'payment_method.required' => 'Ödeme yöntemi seçimi zorunludur.',
                'payment_method.in' => 'Geçersiz ödeme yöntemi.'
            ]);

            $user = Auth::user();
            $cartItems = $user->cart()->where('status', 0)->with(['product', 'discountGroup'])->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Sepetiniz boş.');
            }

            // Dosya zorunluluğu kontrolü: Ürün zorunlu file/files kategorisi içeriyorsa s3_zip dolu olmalı
            if (\Schema::hasColumn('customization_pivot_params', 'is_required')) {
                foreach ($cartItems as $cartItem) {
                    if (!empty($cartItem->s3_zip)) {
                        continue;
                    }

                    $requiresFile = \DB::table('customization_pivot_params as cpp')
                        ->join('customization_categories as cc', 'cc.id', '=', 'cpp.customization_category_id')
                        ->where('cpp.product_id', $cartItem->product_id)
                        ->where('cpp.is_required', 1)
                        ->whereIn('cc.type', ['file', 'files'])
                        ->exists();

                    if ($requiresFile) {
                        $productTitle = $cartItem->product->title ?? 'Ürün';
                        return redirect()->route('cart.index')
                            ->with('error', '"' . $productTitle . '" ürünü için dosya yüklemesi zorunludur. Lütfen sepete dönüp dosyayı yükleyin.');
                    }
                }
            }

            // Toplam fiyatı hesapla (indirimli fiyatlar)
            $totalPrice = 0;
            $totalDiscount = 0;
            
            foreach ($cartItems as $item) {
                $originalTotal = $item->original_price * $item->quantity;
                $discountedTotal = $item->price * $item->quantity;
                
                $totalPrice += $discountedTotal;
                $totalDiscount += ($originalTotal - $discountedTotal);
            }

            // Sipariş numarası oluştur (ken-000000001 formatında)
            $orderNumber = Order::generateOrderNumber();

            // Acil üretim bilgilerini topla
            $urgentProductionNotes = [];
            $totalUrgentPrice = 0;
            foreach ($cartItems as $cartItem) {
                if ($cartItem->notes) {
                    $notes = json_decode($cartItem->notes, true);
                    if (isset($notes['urgent_production']) && $notes['urgent_production']) {
                        $urgentProductionNotes[] = $cartItem->product->title;
                        $totalUrgentPrice += $notes['urgent_price'] ?? 0;
                    }
                }
            }
            
            // Sipariş notlarını oluştur
            $orderNotes = [];
            if (!empty($urgentProductionNotes)) {
                $orderNotes[] = '🚨 ACİL ÜRETİM İSTENİYOR - Ürünler: ' . implode(', ', $urgentProductionNotes);
            }
            
            // Siparişi oluştur
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'customer_name' => $request->customer_name,
                'customer_surname' => $request->customer_surname,
                'customer_phone' => $request->customer_phone,
                'city' => $request->city,
                'district' => $request->district,
                'shipping_address' => $request->shipping_address,
                'payment_method' => 'bakiye', // Ödeme türünü bakiye olarak ayarla
                'total_price' => $totalPrice,
                'discount_amount' => $totalDiscount,
                'notes' => !empty($orderNotes) ? implode("\n", $orderNotes) : null,
                'status' => 0 // İşlemde durumu
            ]);

            // Sepet kalemlerini siparişe bağla, durumlarını güncelle ve cart_id'yi sipariş numarasıyla yenile
            foreach ($cartItems as $cartItem) {
                $oldCartId = $cartItem->cart_id;
                $newCartId = $cartItem->generateCartIdentifier($orderNumber);

                $cartItem->update([
                    'order_id' => $order->id,
                    'status' => 1,
                    'cart_id' => $newCartId,
                ]);

                // S3'teki ZIP dosyasını yeni isimle güncelle (dosya adı + içindeki klasör)
                $cartItem->renameS3Zip($oldCartId, $newCartId);
            }
            // Müşterinin firmasının bakiyesinden sipariş tutarını düş
            if ($user->customer && $user->customer->balance !== null) {
                $user->customer->subtractBalance($totalPrice);
            }
            // Sipariş onay e-postası gönder
            $mailService = new MailService();
            $mailService->sendOrderConfirmationEmail($order->load('user'));

            return redirect()->route('orders.show', $order->id)->with('success', 'Siparişiniz başarıyla oluşturuldu. Sipariş numaranız: ' . $orderNumber);
        }

        // GET request için view döndür
        return view('frontend.cart.complete');
    }

        public function add(Request $request)
    {
     
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Sepete ürün eklemek için giriş yapmalısınız.');
        }
        
        // Validation kuralları
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'customizations' => 'nullable|array',
            'customization' => 'nullable|array',
        ]);
        
        // Ürünü al
        $product = Product::with(['mainCategory'])->findOrFail($request->product_id);
        
        // Customization verilerini işle
        $customizationData = [];
        $totalCustomizationPrice = 0;

        // Parent parametreler (customization array)
        if ($request->has('customization')) {
            foreach ($request->customization as $categoryId => $data) {
                // Dosya kontrolü - eğer data array ise ve files key'i varsa dosya işleme
                if (is_array($data) && isset($data['files']) && !empty($data['files'])) {
                    // Dosya işleme - bu kategorinin işlemi dosya işleme bölümünde yapılacak
                    continue;
                }
                
                if (is_array($data)) {
                    // Checkbox array
                    $customizationData[$categoryId] = [
                        'type' => 'checkbox',
                        'values' => $data
                    ];
                    
                    // Fiyat hesapla
                    foreach ($data as $pivotId) {
                        $pivotParam = CustomizationPivotParam::find($pivotId);
                        if ($pivotParam && $pivotParam->price) {
                            $totalCustomizationPrice += $pivotParam->price;
                        }
                    }
                } else {
                    // Radio/Select/Input - pivot ID veya text
                    if (is_numeric($data)) {
                        // Pivot ID - radio/select
                        $pivotParam = CustomizationPivotParam::find($data);
                        if ($pivotParam) {
                            // Select mi radio mu hidden mu kontrol et
                            $category = \App\Models\CustomizationCategory::find($categoryId);
                            $type = 'radio'; // Default radio
                            if ($category) {
                                if ($category->type == 'select') {
                                    $type = 'select';
                                } elseif ($category->type == 'hidden') {
                                    $type = 'hidden';
                                }
                            }
                            
                            $customizationData[$categoryId] = [
                                'type' => $type,
                                'value' => $data
                            ];
                            
                            // Fiyat hesapla
                            if ($pivotParam->price) {
                                $totalCustomizationPrice += $pivotParam->price;
                            }
                        }
                    } else {
                        // Text input
                        $value = is_array($data) ? json_encode($data) : $data;
                        if (!empty($value) && $value !== 'null' && $value !== '{"data":null}') {
                            $customizationData[$categoryId] = [
                                'type' => 'input',
                                'value' => $value
                            ];
                        }
                    }
                }
            }
        }

        
        // Child parametreler (customizations array) - Hidden tipindeki kategoriler burada işlenmez
        if ($request->has('customizations')) {
            foreach ($request->customizations as $parentParamId => $parentData) {
                // Yeni nested array formatını kontrol et
                if (is_array($parentData)) {
                    foreach ($parentData as $categoryId => $value) {
                        // Hidden tipindeki kategorileri atla, bunlar customization array'inde işleniyor
                        $category = \App\Models\CustomizationCategory::find($categoryId);
                        if ($category && $category->type == 'hidden') {
                            continue;
                        }
                        
                        if (is_array($value)) {
                            // Checkbox - seçilen pivot ID'leri array olarak
                            $customizationData[$categoryId] = [
                                'type' => 'checkbox',
                                'values' => $value
                            ];
                            
                            // Fiyat hesapla
                            foreach ($value as $pivotId) {
                                $pivotParam = CustomizationPivotParam::find($pivotId);
                                if ($pivotParam && $pivotParam->price) {
                                    $totalCustomizationPrice += $pivotParam->price;
                                }
                            }
                        } else {
                            // Radio/Select - seçilen pivot ID
                            $customizationData[$categoryId] = [
                                'type' => 'radio',
                                'value' => $value
                            ];
                            
                            // Fiyat hesapla
                            $pivotParam = CustomizationPivotParam::find($value);
                            if ($pivotParam && $pivotParam->price) {
                                $totalCustomizationPrice += $pivotParam->price;
                            }
                        }
                    }
                } else {
                    // Eski format için backward compatibility
                    $categoryId = $parentParamId;
                    $value = $parentData;
                    
                    // Hidden tipindeki kategorileri atla, bunlar customization array'inde işleniyor
                    $category = \App\Models\CustomizationCategory::find($categoryId);
                    if ($category && $category->type == 'hidden') {
                        continue;
                    }
                    
                    if (is_array($value)) {
                        // Checkbox - seçilen pivot ID'leri array olarak
                        $customizationData[$categoryId] = [
                            'type' => 'checkbox',
                            'values' => $value
                        ];
                        
                        // Fiyat hesapla
                        foreach ($value as $pivotId) {
                            $pivotParam = CustomizationPivotParam::find($pivotId);
                            if ($pivotParam && $pivotParam->price) {
                                $totalCustomizationPrice += $pivotParam->price;
                            }
                        }
                    } else {
                        // Radio/Select - seçilen pivot ID
                        $customizationData[$categoryId] = [
                            'type' => 'radio',
                            'value' => $value
                        ];
                        
                        // Fiyat hesapla
                        $pivotParam = CustomizationPivotParam::find($value);
                        if ($pivotParam && $pivotParam->price) {
                            $totalCustomizationPrice += $pivotParam->price;
                        }
                    }
                }
            }
        }
        
      
        
        // Sepete ekle
        $cart = new Cart();
        $cart->user_id = auth()->id();
        $cart->product_id = $request->product_id;
        $cart->quantity = $request->quantity ?? 1;
        $cart->page_count = $request->page_count;
        if($request->has('urgent_status')){
            $cart->urgent_status = 1;
        }
        // Fiyatları güncelle
        $cart->original_price = $request->price; // Orijinal fiyat
        
        // İndirim hesaplaması yap
        $discountedPrice = $request->price;
        $discountGroupId = null;
      
        // Kullanıcının customer_id'si var mı kontrol et
        if (auth()->user()->customer_id) {
            // Ürünün kategorisini al
            $product = Product::find($request->product_id);
           
            if ($product) {
                // Kullanıcı için geçerli indirimleri bul
                $validDiscounts = \App\Models\DiscountGroup::getValidDiscountsForUser(
                    auth()->id(), 
                    $product->main_category_id
                );
               
                if($validDiscounts->isNotEmpty()){
                    $maxDiscount = $validDiscounts->max('discount_percentage');
                    $discountedPrice = $request->price * (1 - $maxDiscount / 100);
                    $discountGroupId = $validDiscounts->where('discount_percentage', $maxDiscount)->first()->id;
                }
            }
        }
        
        $cart->price = $discountedPrice; // İndirimli fiyat
      
        
       
        
        // Notes alanını güncelle
        $notesData = [
            'customizations' => $customizationData,
            'total_customization_price' => $request->price,
        ];
        
        // Sipariş notu varsa ekle
        if ($request->has('order_note') && !empty(trim($request->order_note))) {
            $notesData['order_note'] = trim($request->order_note);
        }
        
        $cart->notes = json_encode($notesData);
        $cart->save();
        
        // Generate cart identifier and unique barcode
        $cart->cart_id = $cart->generateCartIdentifier();
        $cart->barcode = $cart->generateUniqueBarcode();
        $cart->save();
        
   
        
        // OrderStatusHistory tablosuna kayıt ekle
        OrderStatusHistory::create([
            'cart_id' => $cart->id, // Cart ID'sini cart_id alanına kaydet
            'order_status_id' => 1, // İlk durum: İşlemde
            'user_id' => auth()->id()
        ]);
        
        // Ürünün extra_sales verilerini kontrol et
        $extraSales = null;
        $extraSalesProducts = \App\Models\ExtraSale::with('childProduct')
            ->where('main_product_id', $product->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        if ($extraSalesProducts->count() > 0) {
            $extraSales = $extraSalesProducts->map(function($extraSale) {
                return [
                    'id' => $extraSale->childProduct->id,
                    'title' => $extraSale->childProduct->title,
                    'price' => $extraSale->childProduct->price,
                    'images' => $extraSale->childProduct->images
                ];
            })->toArray();
            
            // Debug: Extra sales verilerini logla
            \Log::info('Extra sales data:', [
                'product_id' => $product->id,
                'product_title' => $product->title,
                'extra_sales_count' => $extraSalesProducts->count(),
                'extra_sales' => $extraSales
            ]);
        }
        
        // JSON response döndür
        return response()->json([
            'success' => true,
            'message' => 'Ürün sepete eklendi!',
            'cart_id' => $cart->id,
            'extra_sales' => $extraSales,
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Auth::user()->cart()->with(['product', 'discountGroup'])->findOrFail($id);
        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        // İndirim bilgilerini de döndür
        $discountInfo = '';
        if ($cartItem->discountGroup) {
            $discountInfo = " (%{$cartItem->discountGroup->discount_percentage} indirim uygulandı)";
        }

        return response()->json([
            'success' => true,
            'message' => 'Sepet güncellendi',
            'total_price' => $cartItem->price * $request->quantity,
            'discount_info' => $discountInfo
        ]);
    }

    public function remove($id)
    {
        $cartItem = Auth::user()->cart()->with(['product', 'discountGroup'])->findOrFail($id);
        
        // Tüm ilişkili dosyaları sil (notes ve cart_files)
        $cartItem->deleteAssociatedFiles();
        
        // OrderStatusHistory kayıtlarını da sil
        OrderStatusHistory::where('cart_id', $cartItem->id)->delete();
        
        $cartItem->delete();

        // Güncel sepet özetini hesapla
        $cartItems = Auth::user()->cart()->where('status', 0)->with(['product', 'discountGroup'])->get();
        
        $subtotal = 0;
        $totalDiscount = 0;
        $finalTotal = 0;
        
        foreach ($cartItems as $item) {
            $originalPrice = $item->original_price;
            $discountedPrice = $item->price;
            
            $originalTotal = $originalPrice * $item->quantity;
            $discountedTotal = $discountedPrice * $item->quantity;
            
            $subtotal += $originalPrice;
            $totalDiscount += ($originalTotal - $discountedTotal);
            $finalTotal += $discountedTotal;
        }

        return response()->json([
            'success' => true,
            'message' => 'Ürün sepetten kaldırıldı',
            'cart_summary' => [
                'subtotal' => number_format($subtotal, 2),
                'total_discount' => number_format($totalDiscount, 2),
                'final_total' => number_format($finalTotal, 2),
                'item_count' => $cartItems->count()
            ]
        ]);
    }

    public function clear()
    {
        $cartItems = Auth::user()->cart()->where('status', 0)->with(['product', 'discountGroup'])->get();
        
        // Her cart item için dosyaları ve ilişkili kayıtları sil
        foreach ($cartItems as $cartItem) {
            // Tüm ilişkili dosyaları sil (notes ve cart_files)
            $cartItem->deleteAssociatedFiles();
            
            // OrderStatusHistory kayıtlarını da sil
            OrderStatusHistory::where('cart_id', $cartItem->id)->delete();
        }
        
        Auth::user()->cart()->where('status', 0)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sepet temizlendi'
        ]);
    }

    public function addExtra(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $product = Product::with(['mainCategory'])->findOrFail($request->product_id);

        // Kullanıcının aktif sepetinde bu ürün var mı kontrol et
        $existingCartItem = $user->cart()->where('product_id', $request->product_id)->where('status', 0)->with(['product', 'discountGroup'])->first();

        if (!$existingCartItem) {
            // İndirim hesaplaması yap
            $discountedPrice = $product->price;
            $discountGroupId = null;
            
            // Kullanıcının customer_id'si var mı kontrol et
            if ($user->customer_id) {
                // Ürünün kategorisini al
                if ($product->main_category_id) {
                    // Kullanıcı için geçerli indirimleri bul
                    $validDiscounts = \App\Models\DiscountGroup::getValidDiscountsForUser(
                        $user->id, 
                        $product->main_category_id
                    );
                    
                    if ($validDiscounts->isNotEmpty()) {
                        // En yüksek indirim oranını al
                        $maxDiscount = $validDiscounts->max('discount_percentage');
                        $discountGroup = $validDiscounts->where('discount_percentage', $maxDiscount)->first();
                        
                        if ($discountGroup) {
                            // İndirimli fiyatı hesapla
                            $discountedPrice = $product->price * (1 - $maxDiscount / 100);
                            $discountGroupId = $discountGroup->id;
                        }
                    }
                }
            }
            
            // Yeni ürün ekle
            $cart = $user->cart()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $discountedPrice,
                'original_price' => $product->price,
                'discount_group_id' => $discountGroupId,
                'notes' => null
            ]);
            
            // Generate cart identifier and unique barcode
            $cart->cart_id = $cart->generateCartIdentifier();
            $cart->barcode = $cart->generateUniqueBarcode();
            $cart->save();

            return response()->json([
                'success' => true,
                'message' => 'Ürün sepete eklendi',
                'cart_count' => $user->cart()->where('status', 0)->count()
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Ürün zaten sepette mevcut.',
                'cart_count' => $user->cart()->where('status', 0)->count()
            ]);
        }
    }

    public function getCartCount()
    {
        $count = Auth::user()->cart()->where('status', 0)->count();
        
        return response()->json([
            'count' => $count
        ]);
    }
    
    public function updateQuantity(Request $request, $id)
    {
        $cartItem = Auth::user()->cart()->with(['product', 'discountGroup'])->findOrFail($id);
        $newQuantity = $request->quantity;
        
        // Minimum 1 olmalı
        if ($newQuantity < 1) {
            $newQuantity = 1;
        }
        
        $cartItem->update(['quantity' => $newQuantity]);
        
        // İndirim bilgilerini de döndür
        $discountInfo = '';
        if ($cartItem->discountGroup) {
            $discountInfo = " (%{$cartItem->discountGroup->discount_percentage} indirim uygulandı)";
        }
        
        return response()->json([
            'success' => true,
            'quantity' => $newQuantity,
            'total_price' => number_format($cartItem->price * $newQuantity, 2),
            'discount_info' => $discountInfo
        ]);
    }
    
   

   
    /**
     * Dosya türünün resim olup olmadığını kontrol et
     */
    private function isImageFile($fileType): bool
    {
        $imageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        return in_array(strtolower($fileType), $imageTypes);
    }

    /**
     * Resim dosyasından thumbnail oluştur
     */
    private function createThumbnail($fileContent, $pivotParamId): ?string
    {
        try {
            // Thumbnail oluştur
            $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($fileContent);
            $image->resize(300, 300);
            
            // Thumbnail'i S3'e yükle
            $thumbnailContent = $image->toJpeg(80);
            $thumbnailPath = 'thumbnails/cart_' . $this->cartId . '_param_' . $pivotParamId . '_' . time() . '.jpg';
            
            $disk = Storage::disk('s3');
            $disk->put($thumbnailPath, $thumbnailContent, 'public');
            $thumbnailUrl = $disk->url($thumbnailPath);
            
            return $thumbnailUrl;
            
        } catch (\Exception $e) {
            Log::error('Thumbnail creation failed', [
                'pivot_param_id' => $pivotParamId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Geçici dosyaları temizle
     */
    private function cleanupTempFiles($tempDir): void
    {
        if (is_dir($tempDir)) {
            $files = glob($tempDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            if (count(scandir($tempDir)) <= 2) {
                rmdir($tempDir);
            }
        }
    }

    /**
     * Dosya adını güvenli hale getir
     */
    private function sanitizeFilename($filename)
    {
        // Dosya uzantısını ayır
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        
        // Türkçe karakterleri değiştir
        $turkish = ['ç', 'Ç', 'ğ', 'Ğ', 'ı', 'İ', 'ö', 'Ö', 'ş', 'Ş', 'ü', 'Ü'];
        $english = ['c', 'C', 'g', 'G', 'i', 'I', 'o', 'O', 's', 'S', 'u', 'U'];
        $name = str_replace($turkish, $english, $name);
        
        // Boşlukları tire ile değiştir
        $name = str_replace(' ', '-', $name);
        
        // Özel karakterleri kaldır (nokta hariç)
        $name = preg_replace('/[^a-zA-Z0-9\-_]/', '', $name);
        
        // Birden fazla tire'yi tek tire'ye çevir
        $name = preg_replace('/-+/', '-', $name);
        
        // Başındaki ve sonundaki tire'leri kaldır
        $name = trim($name, '-');
        
        // Uzantıyı geri ekle
        return $name . '.' . $extension;
    }

    /**
     * Cart dosya durumunu kontrol et
     */
    public function getCartFileStatus($cartFileId)
    {
        try {
            $cartFile = CartFile::with(['cart.product', 'cart.discountGroup'])->findOrFail($cartFileId);

            return response()->json([
                'success' => true,
                'status' => $cartFile->status,
                's3_url' => $cartFile->s3_url,
                'error_message' => $cartFile->error_message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dosya durumu alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cart dosyalarını listele
     */
    public function getCartFiles($cartId)
    {
        try {
            $cartFiles = CartFile::where('cart_id', $cartId)->with(['cart.product', 'cart.discountGroup'])->get();

            return response()->json([
                'success' => true,
                'files' => $cartFiles
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dosyalar alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Zip işlemini başlat (artık otomatik çalışıyor)
     */
    public function startZipProcess($cartId)
    {
        try {
            // Cart'ın tüm dosyalarını kontrol et
            $pendingFiles = CartFile::where('cart_id', $cartId)
                ->where('status', 'pending')
                ->count();
            
            $completedFiles = CartFile::where('cart_id', $cartId)
                ->where('status', 'completed')
                ->count();
            
            $failedFiles = CartFile::where('cart_id', $cartId)
                ->where('status', 'failed')
                ->count();
            
            Log::info('Zip process status check', [
                'cart_id' => $cartId,
                'pending_files' => $pendingFiles,
                'completed_files' => $completedFiles,
                'failed_files' => $failedFiles
            ]);
            
            if ($pendingFiles > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Bazı dosyalar hala yükleniyor ({$pendingFiles} adet). Zip işlemi otomatik olarak başlayacak.",
                    'pending_count' => $pendingFiles,
                    'completed_count' => $completedFiles,
                    'failed_count' => $failedFiles,
                    'auto_start' => true
                ]);
            }
            
            if ($completedFiles === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Henüz tamamlanmış dosya bulunamadı.',
                    'pending_count' => $pendingFiles,
                    'completed_count' => $completedFiles,
                    'failed_count' => $failedFiles
                ]);
            }

            // Zip işlemi zaten otomatik olarak tamamlanmış olmalı
            return response()->json([
                'success' => true,
                'message' => "Zip işlemi zaten tamamlandı!",
                'completed_count' => $completedFiles
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to check zip process status', [
                'cart_id' => $cartId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Durum kontrol edilemedi: ' . $e->getMessage()
            ], 500);
        }
    }

}
