<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MainCategory;

class MainCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mainCategories = MainCategory::with('parent')->orderBy('order')->paginate(15);
        return view('admin.main_categories.index', compact('mainCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.main_categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'ust_id' => 'required|integer',
            'order' => 'nullable|integer',
            'slug' => 'nullable|string|unique:main_categories,slug',
        ]);

        // Slug oluştur
        $slug = $request->slug;
        if (empty($slug)) {
            $slug = $this->generateSlug($request->title);
        }

        MainCategory::create([
            'title' => $request->title,
            'slug' => $slug,
            'ust_id' => $request->ust_id,
            'order' => $request->order ?? 0,
        ]);
        return redirect()->route('admin.main-categories.index')->with('success', 'Kategori eklendi!');
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
        $mainCategory = MainCategory::findOrFail($id);
        $mainCategories = MainCategory::where('id', '!=', $id)->get();
        return view('admin.main_categories.edit', compact('mainCategory', 'mainCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'ust_id' => 'required|integer',
            'order' => 'nullable|integer',
            'slug' => 'nullable|string|unique:main_categories,slug,' . $id,
        ]);

        $mainCategory = MainCategory::findOrFail($id);

        // Slug oluştur
        $slug = $request->slug;
        if (empty($slug)) {
            $slug = $this->generateSlug($request->title);
        }

        $mainCategory->update([
            'title' => $request->title,
            'slug' => $slug,
            'ust_id' => $request->ust_id,
            'order' => $request->order ?? 0,
        ]);
        return redirect()->route('admin.main-categories.index')->with('success', 'Kategori güncellendi!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $mainCategory = MainCategory::findOrFail($id);
        $mainCategory->delete();
        return redirect()->route('admin.main-categories.index')->with('success', 'Kategori silindi!');
    }

    /**
     * Generate slug from title
     */
    private function generateSlug($title)
    {
        $slug = \Illuminate\Support\Str::slug($title, '-', 'tr');
        
        // Eğer slug zaten varsa, sonuna sayı ekle
        $originalSlug = $slug;
        $counter = 1;
        
        while (MainCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}
