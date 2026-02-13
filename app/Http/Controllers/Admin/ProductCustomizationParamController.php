<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CustomizationCategory;
use App\Models\CustomizationParam;
use App\Models\CustomizationPivotParam;

class ProductCustomizationParamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($productId)
    {
       
        $product = Product::findOrFail($productId);
        $customizationCategories = CustomizationCategory::with(['params' => function($query) {
            $query->with('children', 'parent');
        }])->get();
        
        // Ürünün mevcut pivot parametrelerini al ve hiyerarşik olarak organize et
        $pivotParams = $product->customizationPivotParams()
            ->with(['param.category', 'param.children', 'param.parent'])
            ->orderBy('customization_params_ust_id')
            ->orderBy('id')
            ->get();
        
        return view('admin.product_customization_params.index', compact('product', 'customizationCategories', 'pivotParams'));
    }

    public function hierarchical($productId)
    {
        $product = Product::findOrFail($productId);
        
        // Ürünün mevcut pivot parametrelerini al ve hiyerarşik olarak organize et
        $pivotParams = $product->customizationPivotParams()
            ->with(['param.category', 'param.children', 'param.parent'])
            ->orderBy('customization_params_ust_id')
            ->orderBy('order')
            ->orderBy('id')
            ->get();
        
        return view('admin.product_customization_params.hierarchical', compact('product', 'pivotParams'));
    }
    
    /**
     * Analyze hierarchy structure
     */
    private function analyzeHierarchy($pivotParams)
    {
        $analysis = [
            'top_level' => [],
            'children' => [],
            'orphans' => [],
            'issues' => []
        ];
        
        foreach ($pivotParams as $param) {
            if ($param->customization_params_ust_id == 0) {
                $analysis['top_level'][] = [
                    'id' => $param->id,
                    'key' => $param->param->key,
                    'children_count' => $pivotParams->where('customization_params_ust_id', $param->id)->count()
                ];
            } else {
                $parent = $pivotParams->where('id', $param->customization_params_ust_id)->first();
                if ($parent) {
                    $analysis['children'][] = [
                        'id' => $param->id,
                        'key' => $param->param->key,
                        'parent_id' => $param->customization_params_ust_id,
                        'parent_key' => $parent->param->key
                    ];
                } else {
                    $analysis['orphans'][] = [
                        'id' => $param->id,
                        'key' => $param->param->key,
                        'parent_id' => $param->customization_params_ust_id
                    ];
                    $analysis['issues'][] = "ID {$param->id} ({$param->param->key}) parent ID {$param->customization_params_ust_id} bulunamadı!";
                }
            }
        }
        
        return $analysis;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($productId, Request $request)
    {
        $product = Product::findOrFail($productId);
        $parent = null;
        $customizationCategories = CustomizationCategory::get();
        $subCategoryParams = null;
        $selectedCategoryId = null;
        
        if ($request->has('parent')) {
            $parent = CustomizationPivotParam::findOrFail($request->parent);
            
            // Parent parametrenin kategorisini al
            $parentCategoryId = $parent->param->customization_category_id;
            $selectedCategoryId = $parentCategoryId;
            
            // Parent kategorisinin alt kategorilerindeki parametreleri al
            $subCategories = CustomizationCategory::where('ust_id', $parentCategoryId)->get();
            
            // Alt kategorilerdeki parametreleri al
            $subCategoryIds = $subCategories->pluck('id')->toArray();
            $subCategoryParams = CustomizationParam::whereIn('customization_category_id', $subCategoryIds)->get();
        }
        
        return view('admin.product_customization_params.create', compact(
            'product', 
            'customizationCategories', 
            'parent', 
            'subCategoryParams',
            'selectedCategoryId'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $productId)
    {
        $request->validate([
            'params_id' => 'required|exists:customization_params,id',
            'price' => 'nullable|numeric|min:0',
            'customization_params_ust_id' => 'nullable|integer',
            'option1' => 'nullable|string',
            'option2' => 'nullable|string',
        ]);

        $product = Product::findOrFail($productId);
        
        // Parent pivot parametresini kontrol et (eğer varsa)
        $parentPivotId = null;
        if ($request->customization_params_ust_id) {
            // Bu artık pivot ID'si, params_id değil
            $parentPivot = $product->customizationPivotParams()
                ->where('id', $request->customization_params_ust_id)
                ->first();
            
            if (!$parentPivot) {
                return redirect()->back()->with('error', 'Parent parametre bulunamadı!');
            }
            
            $parentPivotId = $parentPivot->id;
        }
        
        // Aynı parametre zaten eklenmiş mi kontrol et
        $existingParam = $product->customizationPivotParams()
            ->where('params_id', $request->params_id)
            ->where('customization_params_ust_id', $parentPivotId ?? 0)
            ->first();
            
        if ($existingParam) {
            return redirect()->back()->with('error', 'Bu parametre zaten eklenmiş!');
        }

        $param = CustomizationParam::findOrFail($request->params_id);
        $product->customizationPivotParams()->create([
            'params_id' => $request->params_id,
            'product_id' => $productId,
            'customization_category_id' => $param->customization_category_id,
            'price' => $request->price,
            'customization_params_ust_id' => $parentPivotId ?? 0,
            'option1' => $request->option1,
            'option2' => $request->option2,
        ]);
       
      
        return redirect()->route('admin.product-customization-params.hierarchical', $productId)
            ->with('success', 'Özelleştirme parametresi eklendi!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($productId, $pivotParamId)
    {
        $product = Product::findOrFail($productId);
        $pivotParam = $product->customizationPivotParams()->with('param.category')->findOrFail($pivotParamId);
        $customizationCategories = CustomizationCategory::with(['params' => function($query) {
            $query->with('children', 'parent');
        }])->get();
        
        return view('admin.product_customization_params.edit', compact('product', 'pivotParam', 'customizationCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $productId, $pivotParamId)
    {
        $request->validate([
            'price' => 'nullable|numeric|min:0',
            'option1' => 'nullable|string',
            'option2' => 'nullable|string',
        ]);

        $product = Product::findOrFail($productId);
        $pivotParam = $product->customizationPivotParams()->findOrFail($pivotParamId);
        
        $pivotParam->update([
            'price' => $request->price,
            'option1' => $request->option1,
            'option2' => $request->option2,
        ]);

        return redirect()->route('admin.product-customization-params.hierarchical', $productId)
            ->with('success', 'Özelleştirme parametresi güncellendi!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($productId, $pivotParamId)
    {
        try {
            $product = Product::findOrFail($productId);
            $pivotParam = $product->customizationPivotParams()->find($pivotParamId);
            
            if (!$pivotParam) {
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'error' => 'Parametre bulunamadı']);
                }
                return redirect()->route('admin.product-customization-params.hierarchical', $productId)
                    ->with('error', 'Parametre bulunamadı');
            }
            
            // Alt parametreleri de sil
            CustomizationPivotParam::where('customization_params_ust_id', $pivotParamId)->delete();
            
            // Ana parametreyi sil
            $pivotParam->delete();

            // AJAX isteği ise JSON döndür
            if (request()->expectsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('admin.product-customization-params.hierarchical', $productId)
                ->with('success', 'Özelleştirme parametresi silindi!');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()]);
            }
            
            return redirect()->route('admin.product-customization-params.hierarchical', $productId)
                ->with('error', 'Silme işlemi başarısız: ' . $e->getMessage());
        }
    }

    /**
     * Get child parameters for a parent parameter
     */
    public function getChildParameters($productId, $parentParamId)
    {
        $childParams = CustomizationParam::where('ust_id', $parentParamId)
            ->orderBy('order')
            ->get(['id', 'key', 'value', 'customization_category_id']);
        
        // Her child parametrenin de child'ları var mı kontrol et
        foreach ($childParams as $childParam) {
            $childParam->has_children = CustomizationParam::where('ust_id', $childParam->id)->exists();
        }
        
        return response()->json($childParams);
    }

    /**
     * Get all levels of parameters for a category (AJAX)
     */
    public function getParametersByLevel($categoryId)
    {
        $allParams = CustomizationParam::where('customization_category_id', $categoryId)
            ->orderBy('ust_id')
            ->orderBy('order')
            ->get(['id', 'key', 'value', 'ust_id']);
        
        // Hiyerarşik yapıyı oluştur
        $hierarchicalParams = [];
        $paramMap = [];
        
        // Önce tüm parametreleri map'e ekle
        foreach ($allParams as $param) {
            $paramMap[$param->id] = $param;
            $param->children = [];
        }
        
        // Parent-child ilişkilerini kur
        foreach ($allParams as $param) {
            if ($param->ust_id > 0 && isset($paramMap[$param->ust_id])) {
                $paramMap[$param->ust_id]->children[] = $param;
            } else {
                $hierarchicalParams[] = $param;
            }
        }
        
        return response()->json($hierarchicalParams);
    }

    /**
     * Get parameter details for AJAX (Tree View)
     */
    public function getParamDetails($productId, $pivotParamId)
    {
        $product = Product::findOrFail($productId);
        $pivotParam = $product->customizationPivotParams()
            ->with(['param.category', 'param.parent'])
            ->findOrFail($pivotParamId);
        
        // Seviye hesapla
        $level = 0;
        $currentParam = $pivotParam;
        while ($currentParam->customization_params_ust_id > 0) {
            $level++;
            $currentParam = $product->customizationPivotParams()
                ->where('id', $currentParam->customization_params_ust_id)
                ->first();
            if (!$currentParam) break;
        }
        
        return view('admin.product_customization_params.partials.param_details', [
            'pivotParam' => $pivotParam,
            'product' => $product,
            'level' => $level
        ]);
    }

    public function updateHierarchy(Request $request, $productId, $pivotParamId)
    {
        try {
            $pivotParam = CustomizationPivotParam::findOrFail($pivotParamId);
            $newParentId = $request->input('new_parent_id', 0);
            $newOrder = $request->input('new_order', 0);
            $allOrders = $request->input('all_orders', []);
            
            \Log::info('Update hierarchy request', [
                'pivot_param_id' => $pivotParamId,
                'new_parent_id' => $newParentId,
                'new_order' => $newOrder,
                'all_orders' => $allOrders
            ]);
            
            // Parent ID'yi ve order'ı güncelle
            $pivotParam->customization_params_ust_id = $newParentId;
            $pivotParam->order = $newOrder;
            $pivotParam->save();
            
            \Log::info('Pivot param updated', [
                'id' => $pivotParam->id,
                'customization_params_ust_id' => $pivotParam->customization_params_ust_id,
                'order' => $pivotParam->order
            ]);
            
            // Tüm parametrelerin order değerlerini güncelle
            if (!empty($allOrders)) {
                foreach ($allOrders as $orderUpdate) {
                    CustomizationPivotParam::where('id', $orderUpdate['param_id'])
                        ->update(['order' => $orderUpdate['new_order']]);
                }
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Update hierarchy error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }


} 