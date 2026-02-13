<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomizationParamsCustomersPivot;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomizationParamsCustomerController extends Controller
{
    /**
     * Add customers to customization parameter
     */
    public function add(Request $request)
    {
        try {
            $request->validate([
                'customization_params_id' => 'required|exists:customization_params,id',
                'product_id' => 'required|exists:products,id',
                'customer_ids' => 'required|array',
                'customer_ids.*' => 'exists:customers,id'
            ]);

            $customizationParamsId = $request->customization_params_id;
            $productId = $request->product_id;
            $customerIds = $request->customer_ids;

            // Önce mevcut kayıtları temizle
            CustomizationParamsCustomersPivot::where([
                'customization_params_id' => $customizationParamsId,
                'product_id' => $productId
            ])->delete();

            // Yeni kayıtları ekle
            foreach ($customerIds as $customerId) {
                CustomizationParamsCustomersPivot::create([
                    'customer_id' => $customerId,
                    'customization_params_id' => $customizationParamsId,
                    'product_id' => $productId
                ]);
            }

            return redirect()->back()->with('success', 'Firmalar başarıyla güncellendi.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove customers from customization params
     */
    public function remove(Request $request)
    {
        try {
            $request->validate([
                'customization_params_id' => 'required|integer|exists:customization_params,id',
                'product_id' => 'required|integer|exists:products,id',
                'customer_ids' => 'required|array',
                'customer_ids.*' => 'integer|exists:customers,id'
            ]);

            $deletedCount = CustomizationParamsCustomersPivot::where('customization_params_id', $request->customization_params_id)
                ->where('product_id', $request->product_id)
                ->whereIn('customer_id', $request->customer_ids)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} müşteri başarıyla kaldırıldı.",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('CustomizationParamsCustomerController::remove error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Müşteri kaldırma işlemi sırasında bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customers for a specific customization param
     */
    public function getCustomers(Request $request, $customizationParamsId, $productId)
    {
        try {
            $customers = CustomizationParamsCustomersPivot::where('customization_params_id', $customizationParamsId)
                ->where('product_id', $productId)
                ->with('customer')
                ->get()
                ->pluck('customer');

            return response()->json([
                'success' => true,
                'customers' => $customers
            ]);

        } catch (\Exception $e) {
            Log::error('CustomizationParamsCustomerController::getCustomers error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Müşteri bilgileri alınırken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get existing customers for a customization parameter
     */
    public function getExistingCustomers(Request $request)
    {
        try {
            $request->validate([
                'customization_params_id' => 'required|exists:customization_params,id',
                'product_id' => 'required|exists:products,id'
            ]);

            $customizationParamsId = $request->customization_params_id;
            $productId = $request->product_id;

            $existingCustomers = CustomizationParamsCustomersPivot::where([
                'customization_params_id' => $customizationParamsId,
                'product_id' => $productId
            ])->pluck('customer_id')->toArray();

            return response()->json([
                'success' => true,
                'customer_ids' => $existingCustomers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Müşteri listesi alınırken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
