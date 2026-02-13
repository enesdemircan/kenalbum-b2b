<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'page_count' => 'nullable|integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'customizations' => 'nullable|string',
            'customization_inputs' => 'nullable|array',
            'customization_inputs.*' => 'nullable|string|max:500',
            'customization_files_data' => 'nullable|array',
            'customization_files_data.*' => 'nullable|string',
        ];

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'page_count.min' => 'Sayfa sayısı en az 1 olmalıdır.',
            'total_price.required' => 'Toplam fiyat gereklidir.',
            'total_price.numeric' => 'Toplam fiyat sayısal olmalıdır.',
            'total_price.min' => 'Toplam fiyat 0\'dan büyük olmalıdır.',
            'customization_inputs.*.max' => 'Özelleştirme değeri çok uzun olamaz.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateRequiredCustomizations($validator);
        });
    }

    /**
     * Zorunlu özelleştirmeleri kontrol eder
     */
    private function validateRequiredCustomizations($validator)
    {
        $productId = $this->route('id');
        $product = Product::findOrFail($productId);
        
        // Customizations JSON string'ini array'e çevir
        $customizations = [];
        if ($this->customizations) {
            if (is_string($this->customizations)) {
                $customizations = json_decode($this->customizations, true) ?? [];
            } else {
                $customizations = $this->customizations;
            }
        }
        
        // Kullanıcının customer ID'sini al
        $customerId = auth()->user()->customer_id;
        
        // Debug: Tüm request verilerini logla
        \Log::info('Full request data:', $this->all());
        
        // Ürünün customization parametrelerini al
        $customizationPivotParams = $product->customizationPivotParams()
            ->with(['param.category'])
            ->where('customization_params_ust_id', 0) // Ana kategoriler
            ->get();
        
        foreach ($customizationPivotParams as $pivotParam) {
            $param = $pivotParam->param;
            $category = $param->category;
            
            // Debug: Hangi parametre kontrol ediliyor
            \Log::info("Checking param: {$param->key} (ID: {$param->id}), Category type: {$category->type}, Required: " . ($pivotParam->is_required ? 'Yes' : 'No'));
            
            // Hidden tipindeki kategoriler için özel kontrol
            if ($category->type === 'hidden') {
                // Bu kategori için kullanıcının customer ID'si pivot tablosunda var mı?
                $customerHasAccess = \DB::table('customization_params_customers_pivot')
                    ->where('customization_params_id', $param->id)
                    ->where('customer_id', $customerId)
                    ->exists();
                
                \Log::info("Hidden category check for {$param->key}: Customer access = " . ($customerHasAccess ? 'Yes' : 'No'));
                
                // Eğer kullanıcının erişimi yoksa, bu parametreyi validation'dan çıkar
                if (!$customerHasAccess) {
                    \Log::info("Skipping validation for {$param->key} - no customer access");
                    continue;
                }
            }
            
            // Parametre zorunlu mu?
            if ($pivotParam->is_required) {
                $paramKey = "customization_inputs.{$param->id}";
                
                // Değer var mı kontrol et - hem input hem de select/radio için
                $inputValue = $this->input("customization_inputs.{$param->id}");
                $customizationValue = $this->input("customizations.{$category->id}");
                
                \Log::info("Validation check for param: {$param->key} (ID: {$param->id}), Category: {$category->title} (ID: {$category->id}), Type: {$category->type}");
                \Log::info("Input Value: " . ($inputValue ?? 'NULL') . ", Customization Value: " . ($customizationValue ?? 'NULL'));
                
                // Eğer hem input hem de customization değeri boşsa hata ver
                if (empty($inputValue) && empty($customizationValue)) {
                    $validator->errors()->add($paramKey, "{$param->key} alanı zorunludur.");
                }
            }
        }
    }
} 