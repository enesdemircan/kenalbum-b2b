<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomizationCategory;

class CustomizationCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = CustomizationCategory::with('parent')->orderBy('order')->paginate(15);
        return view('admin.customization_categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = CustomizationCategory::all();
        return view('admin.customization_categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required',
            'ust_id' => 'required|integer',
            'order' => 'nullable|integer',
            'required' => 'required|boolean',
        ]);
        CustomizationCategory::create([
            'title' => $request->title,
            'type' => $request->type,
            'ust_id' => $request->ust_id,
            'order' => $request->order ?? 0,
            'required' => $request->required,
        ]);
        return redirect()->route('admin.customization-categories.index')->with('success', 'Kategori eklendi!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = CustomizationCategory::findOrFail($id);
        $categories = CustomizationCategory::where('id', '!=', $id)->get();
        return view('admin.customization_categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required',
            'ust_id' => 'required|integer',
            'order' => 'nullable|integer',
            'required' => 'required|boolean',
        ]);
        $category = CustomizationCategory::findOrFail($id);
        $category->update([
            'title' => $request->title,
            'type' => $request->type,
            'ust_id' => $request->ust_id,
            'order' => $request->order ?? 0,
            'required' => $request->required,
        ]);
        return redirect()->route('admin.customization-categories.index')->with('success', 'Kategori güncellendi!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = CustomizationCategory::findOrFail($id);
        $category->delete();
        return redirect()->route('admin.customization-categories.index')->with('success', 'Kategori silindi!');
    }
}
