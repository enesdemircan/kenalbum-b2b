<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountGroup;
use App\Models\MainCategory;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscountGroupController extends Controller
{
    public function index(Request $request)
    {
        $query = DiscountGroup::with(['mainCategory', 'customers']);
        
        // Kampanya adına göre filtreleme
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        
        $discountGroups = $query->orderBy('id', 'desc')->paginate(15);
        
        // Filtre parametrelerini view'a gönder
        $filters = [
            'name' => $request->name
        ];
        
        return view('admin.discount_groups.index', compact('discountGroups', 'filters'));
    }

    public function create()
    {
        $customers = Customer::orderBy('unvan')->get();
        $mainCategories = MainCategory::orderBy('title')->get();
        
        return view('admin.discount_groups.create', compact('customers', 'mainCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'main_category_id' => 'required|exists:main_categories,id',
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:customers,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        DB::transaction(function () use ($request) {
            $discountGroup = DiscountGroup::create([
                'name' => $request->name,
                'description' => $request->description,
                'discount_percentage' => $request->discount_percentage,
                'main_category_id' => $request->main_category_id,
                'is_active' => $request->has('is_active'),
                'start_date' => $request->start_date ?: null,
                'end_date' => $request->end_date ?: null,
            ]);

            // Firmaları ekle
            $discountGroup->customers()->attach($request->customer_ids);
        });

        return redirect()->route('admin.discount-groups.index')
                        ->with('success', 'İndirim grubu başarıyla oluşturuldu.');
    }

    public function show(DiscountGroup $discountGroup)
    {
        $discountGroup->load(['mainCategory', 'customers']);
        return view('admin.discount_groups.show', compact('discountGroup'));
    }

    public function edit(DiscountGroup $discountGroup)
    {
        $customers = Customer::orderBy('unvan')->get();
        $mainCategories = MainCategory::orderBy('title')->get();
        $discountGroup->load('customers');
        
        return view('admin.discount_groups.edit', compact('discountGroup', 'customers', 'mainCategories'));
    }

    public function update(Request $request, DiscountGroup $discountGroup)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'main_category_id' => 'required|exists:main_categories,id',
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:customers,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        DB::transaction(function () use ($request, $discountGroup) {
            $discountGroup->update([
                'name' => $request->name,
                'description' => $request->description,
                'discount_percentage' => $request->discount_percentage,
                'main_category_id' => $request->main_category_id,
                'is_active' => $request->has('is_active'),
                'start_date' => $request->start_date ?: null,
                'end_date' => $request->end_date ?: null,
            ]);

            // Firmaları güncelle
            $discountGroup->customers()->sync($request->customer_ids);
        });

        return redirect()->route('admin.discount-groups.index')
                        ->with('success', 'İndirim grubu başarıyla güncellendi.');
    }

    public function destroy(DiscountGroup $discountGroup)
    {
        $discountGroup->delete();
        return redirect()->route('admin.discount-groups.index')
                        ->with('success', 'İndirim grubu başarıyla silindi.');
    }
}
