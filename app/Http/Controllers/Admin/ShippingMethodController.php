<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller
{
    public function index()
    {
        $methods = ShippingMethod::orderBy('sort_order')->orderBy('title')->paginate(20);
        return view('admin.shipping_methods.index', compact('methods'));
    }

    public function create()
    {
        return view('admin.shipping_methods.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['is_active'] = $request->boolean('is_active');
        ShippingMethod::create($data);
        return redirect()->route('admin.shipping-methods.index')
            ->with('success', 'Kargo metodu eklendi.');
    }

    public function edit($id)
    {
        $method = ShippingMethod::findOrFail($id);
        return view('admin.shipping_methods.edit', compact('method'));
    }

    public function update(Request $request, $id)
    {
        $method = ShippingMethod::findOrFail($id);
        $data = $this->validateData($request, $id);
        $data['is_active'] = $request->boolean('is_active');
        $method->update($data);
        return redirect()->route('admin.shipping-methods.index')
            ->with('success', 'Kargo metodu güncellendi.');
    }

    public function destroy($id)
    {
        $method = ShippingMethod::findOrFail($id);
        if ($method->orders()->exists()) {
            return redirect()->route('admin.shipping-methods.index')
                ->with('error', 'Bu kargo metodu siparişlerde kullanıldığı için silinemez. Pasif yapabilirsiniz.');
        }
        $method->delete();
        return redirect()->route('admin.shipping-methods.index')
            ->with('success', 'Kargo metodu silindi.');
    }

    private function validateData(Request $request, $id = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'code'  => 'required|string|max:50|unique:shipping_methods,code' . ($id ? ',' . $id : ''),
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);
    }
}
