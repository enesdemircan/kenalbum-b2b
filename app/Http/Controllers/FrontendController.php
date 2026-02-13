<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CustomizationCategory;
use App\Http\Requests\StoreOrderRequest;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\MainCategory;
use App\Models\SiteSetting;
use App\Models\Page;
use App\Models\CustomizationParam;
use App\Models\Slider;
class FrontendController extends Controller
{
    public function page($slug) 
    {
        $page = Page::where('slug', $slug)->first();
        return view('frontend.pages.show', compact('page'));
    }
    public function index()
    {
        // Ana sayfada gösterilecek kategorileri al (ust_id = 0 olanlar)
        $homepageCategories = MainCategory::where('ust_id', 0)
            ->with(['children', 'products' => function($query) {
                $query->where('status', 1)->orderBy('id', 'desc')->limit(8);
            }])
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();
            
        // Her kategori için alt kategorilerdeki ürünleri de dahil et
        foreach($homepageCategories as $category) {
            // Alt kategorilerin ID'lerini al
            $childCategoryIds = $category->children->pluck('id');
            
            // Alt kategorilerdeki ürünleri al
            $childProducts = \App\Models\Product::whereIn('main_category_id', $childCategoryIds)
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->limit(8)
                ->get();
            
            // Ana kategori ürünleri ile alt kategori ürünlerini birleştir
            $allProducts = $category->products->merge($childProducts)->take(8);
            
            // Ürünleri kategoriye ata
            $category->setRelation('products', $allProducts);
        }
            
        // Debug için
        foreach($homepageCategories as $cat) {
            \Log::info("Kategori: {$cat->title}, Toplam ürün sayısı: " . $cat->products->count());
        }
            
        $sliders = Slider::where('is_active', 1)->get();
        return view('frontend.index', compact('homepageCategories', 'sliders'));
    }

    public function profile()
    {
        return view('frontend.profile.index');
    }

    public function category($slug)
    {
        $category = MainCategory::where('slug', $slug)->firstOrFail();
        
        // Ana kategori ise (ust_id = 0), alt kategorilerdeki ürünleri de getir
        if ($category->ust_id == 0) {
            // Alt kategorilerin ID'lerini al
            $childCategoryIds = MainCategory::where('ust_id', $category->id)->pluck('id');
            
            // Ana kategori ve alt kategorilerdeki ürünleri getir
            $products = Product::with(['mainCategory', 'customizationPivotParams.param.category'])
                ->where(function($query) use ($category, $childCategoryIds) {
                    $query->where('main_category_id', $category->id)
                          ->orWhereIn('main_category_id', $childCategoryIds);
                })
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->get();
        } else {
            // Alt kategori ise, sadece o kategorideki ürünleri getir
            $products = Product::with(['mainCategory', 'customizationPivotParams.param.category'])
                ->where('main_category_id', $category->id)
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->get();
        }
        
        return view('frontend.categories.index', compact('category', 'products'));
    }



        public function show($slug)
    {
      
        $product = Product::with(['details', 'mainCategory'])->where('slug', $slug)->firstOrFail();
            
            return view('frontend.products.show', compact('product'));
    }

   

    /**
     * Customization verilerini işle
     */
    private function processCustomizationData(Request $request, $product)
    {
        $customizationDetails = [];
        
        // Customization seçimlerini işle
        if ($request->customizations) {
            foreach ($request->customizations as $categoryId => $value) {
                if (is_array($value)) {
                    // Checkbox
                    $customizationDetails[] = [
                        'category_id' => $categoryId,
                        'type' => 'checkbox',
                        'values' => $value
                    ];
                } else {
                    // Radio/Select
                    $customizationDetails[] = [
                        'category_id' => $categoryId,
                        'type' => 'radio',
                        'value' => $value
                    ];
                }
            }
        }
        
        // Dosya verilerini işle
        if ($request->customization_files_data) {
            foreach ($request->customization_files_data as $paramId => $fileData) {
                if (!empty($fileData)) {
                    $customizationDetails[] = [
                        'param_id' => $paramId,
                        'type' => 'file',
                        'file_data' => $fileData
                    ];
                }
            }
        }
        
        return $customizationDetails;
    }

    /**
     * Toplam fiyatı hesapla
     */
    private function calculateTotalPrice(Request $request, $product, $customizationDetails)
    {
        $basePrice = $product->price;
        $customizationPrice = 0;
        $pagePrice = 0;
        
        // Customization fiyatlarını hesapla
        foreach ($customizationDetails as $customization) {
            if (isset($customization['value'])) {
                $pivotParam = $product->customizationPivotParams
                    ->where('params_id', $customization['value'])
                    ->first();
                if ($pivotParam && $pivotParam->price) {
                    $customizationPrice += $pivotParam->price;
                }
            }
        }
        
        // Sayfa fiyatını hesapla
        $pageCount = $request->page_count;
        if ($pageCount > $product->min_pages) {
            $pagePrice = ($pageCount - $product->min_pages) * $product->price_difference_per_page;
        }
        
        return $basePrice + $customizationPrice + $pagePrice;
    }

    /**
     * Pivot parametrelerini hiyerarşik koleksiyon olarak organize eder
     */
    private function buildHierarchicalCollection($pivotParams)
    {
        $hierarchical = collect();
        
        // Ana kategorileri (ust_id = 0) bul
        $mainCategories = $pivotParams->where('customization_params_ust_id', 0);
        
        foreach ($mainCategories as $mainCategory) {
            $categoryData = [
                'pivot' => $mainCategory,
                'param' => $mainCategory->param,
                'children' => $this->getChildren($pivotParams, $mainCategory->id)
            ];
            
            $hierarchical->push($categoryData);
        }
       
        return $hierarchical;
    }



    /**
     * Belirli bir parent ID'sine sahip child'ları recursive olarak bulur
     */
    private function getChildren($pivotParams, $parentId)
    {
        $children = collect();
        
        // Admin panelindeki gibi customization_params_ust_id kullan
        $directChildren = $pivotParams->where('customization_params_ust_id', $parentId);
        
        foreach ($directChildren as $child) {
            $childData = [
                'pivot' => $child,
                'param' => $child->param,
                'children' => $this->getChildren($pivotParams, $child->id)
            ];
            
            $children->push($childData);
        }
        
        return $children;
    }


    public function extraSalesModal(Request $request)
    {
        $extraSalesData = $request->input('extra_sales', []);
        
        // JSON verilerini Product objelerine çevir
        $extraSales = collect($extraSalesData)->map(function($productData) {
            $product = new \App\Models\Product();
            $product->id = $productData['id'];
            $product->title = $productData['title'];
            $product->price = $productData['price'];
            $product->images = $productData['images'] ?? null;
            return $product;
        });
        
        return view('frontend.modal.extra-sales', compact('extraSales'));
    }

    public function getCustomizationParams($productId)
    {
        $product = Product::findOrFail($productId);
        
        // Ürünün top-level özelleştirme parametrelerini al
        $topLevelParams = $product->customizationPivotParams()
            ->with(['param.category'])
            ->where('customization_params_ust_id', 0)
            ->orderBy('order')
            ->orderBy('id')
            ->get();
        
        $html = view('frontend.products.customization-params', [
            'topLevelParams' => $topLevelParams,
            'product' => $product
        ])->render();

        return response()->json(['html' => $html]);
    }

    public function getCustomizationChildren($product, $paramId)
    {
        // Debug log
        \Log::info('getCustomizationChildren called', [
            'product' => $product,
            'paramId' => $paramId
        ]);
        
        // Ürün ID'sini route parametresinden al
        $productId = $product;
        
        if (!$productId) {
            \Log::warning('No product ID provided');
            return response()->json(['html' => '']);
        }
        
        // paramId aslında pivot ID olmalı, params_id değil
        // Bu pivot'u bul
        $parentPivot = \App\Models\CustomizationPivotParam::where('id', $paramId)
            ->where('product_id', $productId)
            ->first();
        
        if (!$parentPivot) {
            \Log::warning('Parent pivot not found', ['paramId' => $paramId, 'productId' => $productId]);
            return response()->json(['html' => '']);
        }
        
        // Child'ları customization_params_ust_id'ye göre bul
        // customization_params_ust_id değeri parent pivot ID'si olarak kullanılıyor
        $childPivots = \App\Models\CustomizationPivotParam::with('param.category')
            ->where('product_id', $productId)
            ->where('customization_params_ust_id', $parentPivot->id)
            ->get();
        
        \Log::info('Child pivots found', [
            'count' => $childPivots->count(),
            'parentPivotId' => $parentPivot->id,
            'childPivots' => $childPivots->pluck('id')->toArray()
        ]);
        
        $childCategory = null;
        if ($childPivots->count() > 0 && $childPivots->first()->param && $childPivots->first()->param->category) {
            $childCategory = $childPivots->first()->param->category;
        }
        
        try {
            $html = view('frontend.products.child-parameters', [
                'childParams' => $childPivots,
                'childCategory' => $childCategory,
                'parentParamId' => $paramId,
                'product' => Product::find($productId)
            ])->render();
            
            \Log::info('HTML generated successfully', ['html_length' => strlen($html)]);
            
            return response()->json(['html' => $html]);
        } catch (\Exception $e) {
            \Log::error('Error generating child parameters HTML', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['html' => '<div class="alert alert-danger">Yükleme hatası: ' . $e->getMessage() . '</div>']);
        }
    }



    public function storeOrder(StoreOrderRequest $request, $id)
    {
        // Eğer customizations bir string ise, array'e çevir
        if ($request->has('customizations') && is_string($request->customizations)) {
            $decoded = json_decode($request->customizations, true);
            if (is_array($decoded)) {
                $request->merge(['customizations' => $decoded]);
            }
        }
        // Debug: Gelen verileri logla
        \Log::info('Order creation request', [
            'product_id' => $id,
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
            'customizations' => $request->customizations,
            'customization_inputs' => $request->customization_inputs,
            'page_count' => $request->page_count,
            'total_price' => $request->total_price
        ]);
        
        $product = Product::findOrFail($id);
        
        // Customizations JSON string'ini array'e çevir
        $customizations = [];
        $customizationPrice = 0;
        
        if ($request->customizations) {
            if (is_string($request->customizations)) {
                $customizations = json_decode($request->customizations, true) ?? [];
            } else {
                $customizations = $request->customizations;
            }
        }
         
        // Özelleştirme fiyatlarını hesapla ve parametre bilgilerini al
        $customizationDetails = [];
        
        // Debug: Gelen customizations verisini logla
        \Log::info('Customizations data:', [
            'raw_customizations' => $customizations,
            'type' => gettype($customizations),
            'count' => is_array($customizations) ? count($customizations) : 'not array'
        ]);
        
        // JavaScript'ten gelen veriyi doğru şekilde işle
        foreach ($customizations as $parentParamId => $parentData) {
            // Ana parametreler (parentParamId = 0) ve child parametreler için
            if (is_array($parentData)) {
                foreach ($parentData as $categoryId => $categoryData) {
                    if (is_array($categoryData)) {
                        // Checkbox array veya file array
                        if (isset($categoryData['file']) || isset($categoryData['files'])) {
                            // File handling - bu kısım dosya işleme bölümünde yapılacak
                            continue;
                        }
                        
                        // Checkbox array
                        foreach ($categoryData as $pivotId) {
                            $pivot = $product->customizationPivotParams->find($pivotId);
                            if ($pivot) {
                                $customizationPrice = $pivot->price ?? 0;
                                $customizationDetails[] = [
                                    'param_id' => $pivot->params_id,
                                    'param_name' => $pivot->param->key,
                                    'category_id' => $pivot->customization_category_id,
                                    'category_name' => $pivot->param->category->title,
                                    'price' => $customizationPrice,
                                    'type' => 'checkbox'
                                ];
                                $customizationPrice += $customizationPrice;
                            }
                        }
                    } else {
                        // Radio/Select single value
                        $pivot = $product->customizationPivotParams->find($categoryData);
                        if ($pivot) {
                            $customizationPrice = $pivot->price ?? 0;
                            $customizationDetails[] = [
                                'param_id' => $pivot->params_id,
                                'param_name' => $pivot->param->key,
                                'category_id' => $pivot->customization_category_id,
                                'category_name' => $pivot->param->category->title,
                                'price' => $customizationPrice,
                                'type' => 'radio'
                            ];
                            $customizationPrice += $customizationPrice;
                        }
                    }
                }
            } else {
                // Eski format için backward compatibility
                if (isset($parentData['value'])) {
                    $paramId = $parentData['value'];
                    $type = $parentData['type'] ?? 'radio';
                    
                    // Parametre bilgilerini al
                    $pivot = $product->customizationPivotParams->firstWhere('params_id', $paramId);
                    if ($pivot) {
                        $customizationPrice = $pivot->price ?? 0;
                        $customizationDetails[] = [
                            'param_id' => $paramId,
                            'param_name' => $pivot->param->key, // key alanını kullan
                            'category_id' => $pivot->customization_category_id,
                            'category_name' => $pivot->param->category->title,
                            'price' => $customizationPrice,
                            'type' => $type
                        ];
                        
                        $customizationPrice += $customizationPrice;
                    }
                }
            }
        }
        
        // Input tipindeki özelleştirmeleri ekle
        if ($request->customization_inputs) {
            $customizationInputs = [];
            if (is_string($request->customization_inputs)) {
                $customizationInputs = json_decode($request->customization_inputs, true) ?? [];
            } else {
                $customizationInputs = $request->customization_inputs;
            }
            
            foreach ($customizationInputs as $paramId => $inputValue) {
                if (!empty($inputValue)) {
                    $param = \App\Models\CustomizationParam::find($paramId);
                    if ($param) {
                        $customizationDetails[] = [
                            'param_id' => $paramId,
                            'param_name' => $param->value,
                            'category_id' => $param->customization_category_id,
                            'category_name' => $param->category->title,
                            'price' => 0,
                            'type' => 'input',
                            'input_value' => $inputValue
                        ];
                    }
                }
            }
        }
        
        // Yüklenen dosyaları ekle (önce gizli input'lardan, sonra customizations'dan)
        $processedFileParams = [];
        
        // Gizli dosya input'larını ekle (öncelikli)
        if ($request->customization_files_data) {
            foreach ($request->customization_files_data as $paramId => $fileDataJson) {
                if (!empty($fileDataJson)) {
                    try {
                        $fileData = json_decode($fileDataJson, true);
                        $param = \App\Models\CustomizationParam::find($paramId);
                        if ($param && isset($fileData['data']) && !empty($fileData['data'])) {
                            $customizationDetails[] = [
                                'param_id' => $paramId,
                                'param_name' => $param->value,
                                'category_id' => $param->customization_category_id,
                                'category_name' => $param->category->title,
                                'price' => 0,
                                'type' => 'file',
                                's3_path' => $fileData['data']['s3_path'] ?? null,
                                's3_url' => $fileData['data']['s3_url'] ?? null,
                                'file_count' => $fileData['data']['file_count'] ?? 0,
                                'status' => $fileData['data']['status'] ?? 'completed'
                            ];
                            $processedFileParams[] = $paramId;
                        }
                    } catch (\Exception $e) {
                        \Log::error('Error parsing file data JSON', [
                            'param_id' => $paramId,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }
        
        // Customizations içindeki dosyaları ekle (eğer gizli input'ta yoksa)
        if (isset($customizations['uploaded_files'])) {
            foreach ($customizations['uploaded_files'] as $paramId => $fileData) {
                // Eğer bu parametre zaten gizli input'tan işlendiyse atla
                if (in_array($paramId, $processedFileParams)) {
                    continue;
                }
                
                $param = \App\Models\CustomizationParam::find($paramId);
                if ($param) {
                    $customizationDetails[] = [
                        'param_id' => $paramId,
                        'param_name' => $param->value,
                        'category_id' => $param->customization_category_id,
                        'category_name' => $param->category->title,
                        'price' => 0,
                        'type' => 'file',
                        's3_path' => $fileData['s3_path'] ?? null,
                        's3_url' => $fileData['s3_url'] ?? null,
                        'file_count' => $fileData['file_count'] ?? 0,
                        'status' => $fileData['status'] ?? 'completed'
                    ];
                }
            }
        }
        
        // Yaprak fiyatı hesaplama (sadece price_difference_per_page > 0 ise)
        $pageCount = $request->page_count ?? $product->min_pages;
        $pagePrice = 0;
        if ($pageCount > $product->min_pages && $product->price_difference_per_page > 0) {
            $pagePrice = ($pageCount - $product->min_pages) * $product->price_difference_per_page;
        }
        
        // Toplam fiyat hesaplama
        $totalPrice = $product->price + $customizationPrice + $pagePrice;
        
        try {
            // İndirim hesaplama
            $originalTotal = $totalPrice;
            $discountAmount = 0;
            $finalTotal = $originalTotal;
            $discountGroupId = null;
            
            // Kullanıcının bu ürün kategorisi için geçerli indirimleri kontrol et
            $validDiscounts = \App\Models\DiscountGroup::getValidDiscountsForUser(
                auth()->id(), 
                $product->main_category_id
            );
            
            if ($validDiscounts->count() > 0) {
                // En yüksek indirim oranını al
                $bestDiscount = $validDiscounts->sortByDesc('discount_percentage')->first();
                $discountAmount = ($originalTotal * $bestDiscount->discount_percentage) / 100;
                $finalTotal = $originalTotal - $discountAmount;
                $discountGroupId = $bestDiscount->id;
                
                \Log::info('Discount applied', [
                    'discount_group' => $bestDiscount->name,
                    'discount_percentage' => $bestDiscount->discount_percentage,
                    'original_total' => $originalTotal,
                    'discount_amount' => $discountAmount,
                    'final_total' => $finalTotal
                ]);
            }
            
            $order = \App\Models\Order::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'customizations' => $customizationDetails,
                'page_count' => $request->page_count,
                'total_price' => $finalTotal, // İndirimli fiyat
                'discount_group_id' => $discountGroupId,
                'discount_amount' => $discountAmount,
                'original_total' => $originalTotal,
                'final_total' => $finalTotal,
            ]);
            
            // Sipariş oluşturulduğunda otomatik olarak "Onay Bekliyor" durumunu ata
            // OrderStatusHistory tablosunda sadece cart_id var, order_id yok
            // Bu yüzden Order için OrderStatusHistory kaydı oluşturulamıyor
            // Order durumu Order tablosundaki status alanında tutuluyor
            
            \Log::info('Order created successfully', [
                'order_id' => $order->id,
                'discount_applied' => $discountGroupId ? true : false,
                'discount_amount' => $discountAmount
            ]);
            
            return redirect()->route('orders.show', $order->id)->with('success', 'Siparişiniz oluşturuldu!');
        } catch (\Exception $e) {
            \Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Sipariş oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }


}
