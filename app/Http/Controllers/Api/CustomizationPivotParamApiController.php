<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomizationPivotParam;
use App\Models\CustomizationParam;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CustomizationPivotParamApiController extends Controller
{
    /**
     * Display a listing of customization pivot params.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $query = CustomizationPivotParam::with(['product', 'param', 'category']);

            // Filtreleme
            if ($request->has('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            if ($request->has('customization_category_id')) {
                $query->where('customization_category_id', $request->customization_category_id);
            }

            if ($request->has('params_id')) {
                $query->where('params_id', $request->params_id);
            }

            // Sadece ana parametreleri getir (parent_id = 0)
            if ($request->has('main_only') && $request->main_only == true) {
                $query->where('customization_params_ust_id', 0);
            }

            $customizationParams = $query->orderBy('order', 'asc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Özelleştirme parametreleri başarıyla getirildi',
                'data' => $customizationParams
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Özelleştirme parametreleri getirilirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created customization pivot param.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'params_id' => 'required|exists:customization_params,id',
                'customization_category_id' => 'required|exists:customization_categories,id',
                'price' => 'nullable|numeric|min:0',
                'option1' => 'nullable|string',
                'option2' => 'nullable|string',
                'customization_params_ust_id' => 'nullable|integer',
                'order' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Aynı parametre zaten eklenmiş mi kontrol et
            $exists = CustomizationPivotParam::where('product_id', $request->product_id)
                ->where('params_id', $request->params_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu parametre zaten ürüne eklenmiş'
                ], 400);
            }

            $customizationParam = CustomizationPivotParam::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Özelleştirme parametresi başarıyla oluşturuldu',
                'data' => $customizationParam->load(['product', 'param', 'category'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Özelleştirme parametresi oluşturulurken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified customization pivot param.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $customizationParam = CustomizationPivotParam::with([
                'product',
                'param',
                'category'
            ])->findOrFail($id);

            // Alt parametreleri de getir
            $children = $customizationParam->getChildren();

            return response()->json([
                'success' => true,
                'message' => 'Özelleştirme parametresi detayları getirildi',
                'data' => [
                    'customization_param' => $customizationParam,
                    'children' => $children,
                    'has_children' => $customizationParam->hasChildren(),
                    'has_parent' => $customizationParam->hasParent()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Özelleştirme parametresi bulunamadı',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified customization pivot param.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $customizationParam = CustomizationPivotParam::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'product_id' => 'sometimes|exists:products,id',
                'params_id' => 'sometimes|exists:customization_params,id',
                'customization_category_id' => 'sometimes|exists:customization_categories,id',
                'price' => 'sometimes|numeric|min:0',
                'option1' => 'sometimes|string',
                'option2' => 'sometimes|string',
                'customization_params_ust_id' => 'sometimes|integer',
                'order' => 'sometimes|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $customizationParam->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Özelleştirme parametresi başarıyla güncellendi',
                'data' => $customizationParam->load(['product', 'param', 'category'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Özelleştirme parametresi güncellenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified customization pivot param.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $customizationParam = CustomizationPivotParam::findOrFail($id);
            
            // Alt parametreleri kontrol et
            if ($customizationParam->hasChildren()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu parametrenin alt parametreleri var. Önce alt parametreleri silin.'
                ], 400);
            }

            $customizationParam->delete();

            return response()->json([
                'success' => true,
                'message' => 'Özelleştirme parametresi başarıyla silindi'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Özelleştirme parametresi silinirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get children of a customization pivot param
     *
     * @param int $paramId
     * @return JsonResponse
     */
    public function getChildren(int $paramId): JsonResponse
    {
        try {
            $customizationParam = CustomizationPivotParam::findOrFail($paramId);
            $children = $customizationParam->getChildren();

            return response()->json([
                'success' => true,
                'message' => 'Alt parametreler getirildi',
                'data' => [
                    'parent' => $customizationParam,
                    'children' => $children,
                    'children_count' => $children->count()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Alt parametreler getirilirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

