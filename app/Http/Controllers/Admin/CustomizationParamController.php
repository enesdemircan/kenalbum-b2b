<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomizationParam;
use App\Models\CustomizationCategory;
use Illuminate\Support\Facades\Storage;

class CustomizationParamController extends Controller
{
    public function index($categoryId)
    {
        $category = CustomizationCategory::findOrFail($categoryId);
        $params = CustomizationParam::where('customization_category_id', $categoryId)->orderBy('order')->paginate(15);
        return view('admin.customization_params.index', compact('category', 'params'));
    }

    public function create($categoryId)
    {
        $category = CustomizationCategory::findOrFail($categoryId);
        return view('admin.customization_params.create', compact('category'));
    }

    public function store(Request $request, $categoryId)
    {
        $category = CustomizationCategory::findOrFail($categoryId);
        $data = $request->validate([
            'key' => 'required|string|max:255',
            'order' => 'nullable|integer',
            'value' => 'nullable|string|max:255',
            'value_file' => 'nullable|file|image|max:2048',
            'option2' => 'nullable|string|in:true,false',
        ]);
        
        $data['customization_category_id'] = $categoryId;
        $data['order'] = $data['order'] ?? 0; // Default order değeri
        $data['value'] = $data['value'] ?? ''; // Default value değeri
        $data['option2'] = $data['option2'] ?? 'false'; // Default option2 değeri
        
        // Eğer dosya yüklendiyse, dosya yolunu value olarak kaydet
        if ($request->hasFile('value_file')) {
            $data['value'] = $request->file('value_file')->store('customization_params', 'public');
        }
        
        CustomizationParam::create($data);
        return redirect()->route('admin.customization-params.index', $categoryId)->with('success', 'Parametre eklendi!');
    }

    public function edit($categoryId, $paramId)
    {
        $category = CustomizationCategory::findOrFail($categoryId);
        $param = CustomizationParam::findOrFail($paramId);
        return view('admin.customization_params.edit', compact('category', 'param'));
    }

    public function update(Request $request, $categoryId, $paramId)
    {
        $param = CustomizationParam::findOrFail($paramId);
        $data = $request->validate([
            'key' => 'required|string|max:255',
            'order' => 'nullable|integer',
            'value' => 'nullable|string|max:255',
            'value_file' => 'nullable|file|image|max:2048',
            'option2' => 'nullable|string|in:true,false',
        ]);
        
        $data['order'] = $data['order'] ?? 0; // Default order değeri
        $data['value'] = $data['value'] ?? ''; // Default value değeri
        $data['option2'] = $data['option2'] ?? 'false'; // Default option2 değeri
        
        // Eğer dosya yüklendiyse, dosya yolunu value olarak kaydet
        if ($request->hasFile('value_file')) {
            // Eski dosyayı sil
            if ($param->value && Storage::disk('public')->exists($param->value)) {
                Storage::disk('public')->delete($param->value);
            }
            $data['value'] = $request->file('value_file')->store('customization_params', 'public');
        }
        
        $param->update($data);
        return redirect()->route('admin.customization-params.index', $categoryId)->with('success', 'Parametre güncellendi!');
    }

    public function destroy($categoryId, $paramId)
    {
        $param = CustomizationParam::findOrFail($paramId);
        if ($param->value && Storage::disk('public')->exists($param->value)) {
            Storage::disk('public')->delete($param->value);
        }
        $param->delete();
        return redirect()->route('admin.customization-params.index', $categoryId)->with('success', 'Parametre silindi!');
    }

    /**
     * Get parameters by category ID (AJAX)
     */
    public function getParamsByCategory($categoryId)
    {
        $params = CustomizationParam::where('customization_category_id', $categoryId)
            ->where('ust_id', 0) // Sadece ana parametreleri getir
            ->orderBy('order')
            ->get(['id', 'key', 'value']);
        
        return response()->json($params);
    }

    /**
     * Get parent parameters for a specific parameter (AJAX)
     */
    public function getParentParams($paramId)
    {
        $param = CustomizationParam::findOrFail($paramId);
        $parentParams = CustomizationParam::where('customization_category_id', $param->customization_category_id)
            ->where('ust_id', 0) // Sadece ana parametreleri getir
            ->where('id', '!=', $paramId) // Kendisi hariç
            ->orderBy('order')
            ->get(['id', 'key', 'value']);
        
        return response()->json($parentParams);
    }
}
