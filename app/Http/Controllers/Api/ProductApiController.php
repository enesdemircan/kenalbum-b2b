<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\CustomizationPivotParam;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductApiController extends Controller
{
    /**
     * Display a listing of products.
     *
     * @param Request $request
     * @return JsonResponse
     */ 
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $query = Product::with(['mainCategory', 'parentProduct', 'childProducts']);

            // Filtreleme
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('main_category_id')) {
                $query->where('main_category_id', $request->main_category_id);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('tags', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            $products = $query->orderBy('order', 'asc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Ürünler başarıyla getirildi',
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ürünler getirilirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:products,slug',
                'price' => 'required|numeric|min:0',
                'urgent_price' => 'nullable|numeric|min:0',
                'main_category_id' => 'required|exists:main_categories,id',
                'images' => 'nullable|string',
                'thumbnails' => 'nullable|string',
                'template_url' => 'nullable|string',
                'status' => 'required|integer|in:0,1',
                'order' => 'nullable|integer',
                'price_difference_per_page' => 'nullable|numeric',
                'decreasing_per_page' => 'nullable|numeric',
                'max_pages' => 'nullable|integer',
                'min_pages' => 'nullable|integer',
                'option1' => 'nullable|string',
                'option2' => 'nullable|string',
                'suggested_products' => 'nullable|array',
                'ust_id' => 'nullable|exists:products,id',
                'tags' => 'nullable|string',
                'stock_status' => 'nullable|string|max:255',
                'description' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Slug oluştur
            if (!$request->slug) {
                $slug = Str::slug($request->title);
                $count = 1;
                while (Product::where('slug', $slug)->exists()) {
                    $slug = Str::slug($request->title) . '-' . $count;
                    $count++;
                }
                $request->merge(['slug' => $slug]);
            }

            $product = Product::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla oluşturuldu',
                'data' => $product->load(['mainCategory', 'parentProduct'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün oluşturulurken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = Product::with([
                'mainCategory',
                'parentProduct',
                'childProducts',
                'customizationPivotParams.param',
                'customizationPivotParams.category',
                'details',
                'extraSales.childProduct'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Ürün detayları getirildi',
                'data' => $product
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified product.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|string|max:255',
                'slug' => 'sometimes|string|max:255|unique:products,slug,' . $id,
                'price' => 'sometimes|numeric|min:0',
                'urgent_price' => 'sometimes|numeric|min:0',
                'main_category_id' => 'sometimes|exists:main_categories,id',
                'images' => 'sometimes|string',
                'thumbnails' => 'sometimes|string',
                'template_url' => 'sometimes|string',
                'status' => 'sometimes|integer|in:0,1',
                'order' => 'sometimes|integer',
                'price_difference_per_page' => 'sometimes|numeric',
                'decreasing_per_page' => 'sometimes|numeric',
                'max_pages' => 'sometimes|integer',
                'min_pages' => 'sometimes|integer',
                'option1' => 'sometimes|string',
                'option2' => 'sometimes|string',
                'suggested_products' => 'sometimes|array',
                'ust_id' => 'sometimes|exists:products,id',
                'tags' => 'sometimes|string',
                'stock_status' => 'sometimes|string|max:255',
                'description' => 'sometimes|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $product->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla güncellendi',
                'data' => $product->load(['mainCategory', 'parentProduct', 'childProducts'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün güncellenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            
            // Alt ürünleri kontrol et
            if ($product->childProducts()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu ürünün alt ürünleri var. Önce alt ürünleri silin.'
                ], 400);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla silindi'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün silinirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product customization parameters (hierarchical structure)
     *
     * @param int $productId
     * @return JsonResponse
     */
    public function getCustomizationParams(int $productId): JsonResponse
    {
        try {
            $product = Product::findOrFail($productId);
            
            // Tüm parametreleri al
            $allParams = CustomizationPivotParam::where('product_id', $productId)
                ->with(['param.category'])
                ->orderBy('order', 'asc')
                ->get();
            
            // Ana parametreleri al (customization_params_ust_id = 0)
            $mainParams = $allParams->where('customization_params_ust_id', 0);
            
            // Recursive fonksiyon: Bir pivot parametrenin tüm children'larını getirir
            $buildHierarchy = function($parentId, $level = 0) use ($allParams, &$buildHierarchy) {
                $children = $allParams->where('customization_params_ust_id', $parentId);
                
                return $children->map(function ($pivotParam) use ($buildHierarchy, $level, $allParams) {
                    // Children var mı kontrol et (collection içinde arama yaparak)
                    $hasChildren = $allParams->where('customization_params_ust_id', $pivotParam->id)->count() > 0;
                    
                    $data = [
                        'pivot_id' => $pivotParam->id,
                        'param_id' => $pivotParam->params_id,
                        'category_id' => $pivotParam->customization_category_id,
                        'category_title' => $pivotParam->category->title ?? null,
                        'category_type' => $pivotParam->category->type ?? null,
                        'category_required' => $pivotParam->category->required ?? false,
                        'param_key' => $pivotParam->param->key ?? null,
                        'param_value' => $pivotParam->param->value ?? null,
                        'price' => $pivotParam->price ?? 0,
                        'order' => $pivotParam->order ?? 0,
                        'has_children' => $hasChildren,
                    ];
                    
                    // Eğer children varsa, recursive olarak ekle
                    if ($hasChildren) {
                        $data['children'] = $buildHierarchy($pivotParam->id, $level + 1);
                    } else {
                        $data['children'] = [];
                    }
                    
                    return $data;
                })->values();
            };
            
            // Hiyerarşik yapıyı oluştur
            $hierarchicalParams = $mainParams->map(function ($pivotParam) use ($buildHierarchy, $allParams) {
                // Children var mı kontrol et (collection içinde arama yaparak)
                $hasChildren = $allParams->where('customization_params_ust_id', $pivotParam->id)->count() > 0;
                
                $data = [
                    'pivot_id' => $pivotParam->id,
                    'param_id' => $pivotParam->params_id,
                    'category_id' => $pivotParam->customization_category_id,
                    'category_title' => $pivotParam->category->title ?? null,
                    'category_type' => $pivotParam->category->type ?? null,
                    'category_required' => $pivotParam->category->required ?? false,
                    'param_key' => $pivotParam->param->key ?? null,
                    'param_value' => $pivotParam->param->value ?? null,
                    'price' => $pivotParam->price ?? 0,
                    'order' => $pivotParam->order ?? 0,
                    'has_children' => $hasChildren,
                ];
                
                // Eğer children varsa, recursive olarak ekle
                if ($hasChildren) {
                    $data['children'] = $buildHierarchy($pivotParam->id, 0);
                } else {
                    $data['children'] = [];
                }
                
                return $data;
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Ürün özelleştirme parametreleri getirildi',
                'data' => [
                    'product' => [
                        'id' => $product->id,
                        'title' => $product->title,
                        'price' => $product->price
                    ],
                    'customization_params' => $hierarchicalParams,
                    'usage' => [
                        'description' => 'Bu endpoint ürünün özelleştirme parametrelerini hiyerarşik yapıda döndürür.',
                        'how_to_use' => [
                            '1. Ana parametreleri (customization_params_ust_id = 0) seçin',
                            '2. Seçilen ana parametrenin children array\'inden alt parametreleri seçin',
                            '3. Her seviyede category_type\'a göre (select, radio, checkbox, input) uygun format kullanın',
                            '4. Seçilen pivot_id\'leri notes alanındaki customizations JSON\'unda kullanın'
                        ],
                        'notes_format' => [
                            'customizations' => [
                                'pivot_id' => [
                                    'type' => 'select|radio|checkbox|input',
                                    'value' => 'param_id (numeric) veya text (string)'
                                ]
                            ],
                            'total_customization_price' => 'number',
                            'order_note' => 'string (optional)'
                        ]
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Özelleştirme parametreleri getirilirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

