<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    public function index($productId)
    {
        $product = Product::findOrFail($productId);
        $details = $product->details()->orderBy('id')->get();
        
        return view('admin.product_details.index', compact('product', 'details'));
    }

    public function create($productId)
    {
        $product = Product::findOrFail($productId);
        return view('admin.product_details.create', compact('product'));
    }

    public function store(Request $request, $productId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'text' => 'required|string'
        ]);

        $product = Product::findOrFail($productId);
        
        $product->details()->create([
            'title' => $request->title,
            'text' => $request->text
        ]);

        return redirect()->route('admin.product-details.index', $productId)
            ->with('success', 'Ürün detayı başarıyla eklendi!');
    }

    public function edit($productId, $detailId)
    {
        $product = Product::findOrFail($productId);
        $detail = ProductDetail::where('product_id', $productId)
            ->where('id', $detailId)
            ->firstOrFail();
        
        return view('admin.product_details.edit', compact('product', 'detail'));
    }

    public function update(Request $request, $productId, $detailId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'text' => 'required|string'
        ]);

        $detail = ProductDetail::where('product_id', $productId)
            ->where('id', $detailId)
            ->firstOrFail();
        
        $detail->update([
            'title' => $request->title,
            'text' => $request->text
        ]);

        return redirect()->route('admin.product-details.index', $productId)
            ->with('success', 'Ürün detayı başarıyla güncellendi!');
    }

    public function destroy($productId, $detailId)
    {
        $detail = ProductDetail::where('product_id', $productId)
            ->where('id', $detailId)
            ->firstOrFail();
        
        $detail->delete();

        return redirect()->route('admin.product-details.index', $productId)
            ->with('success', 'Ürün detayı başarıyla silindi!');
    }
} 