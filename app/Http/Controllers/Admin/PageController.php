<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages = Page::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'text' => 'required|string',
        ]);

        Page::create([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => !empty($request->slug) ? $request->slug : $this->createSlug($request->title),
            'text' => $request->text,
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Sayfa başarıyla oluşturuldu!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Page $page)
    {
        return view('admin.pages.show', compact('page'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'text' => 'required|string',
        ]);

        $page->update([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => !empty($request->slug) ? $request->slug : $this->createSlug($request->title),
            'text' => $request->text,
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Sayfa başarıyla güncellendi!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Sayfa başarıyla silindi!');
    }

    /**
     * Türkçe karakterleri destekleyen slug oluşturma fonksiyonu
     */
    private function createSlug($title)
    {
        $turkishChars = [
            'ç' => 'c', 'Ç' => 'C',
            'ğ' => 'g', 'Ğ' => 'G',
            'ı' => 'i', 'I' => 'I',
            'ö' => 'o', 'Ö' => 'O',
            'ş' => 's', 'Ş' => 'S',
            'ü' => 'u', 'Ü' => 'U'
        ];

        $slug = $title;
        
        // Türkçe karakterleri değiştir
        foreach ($turkishChars as $turkish => $english) {
            $slug = str_replace($turkish, $english, $slug);
        }

        // Laravel'in Str::slug fonksiyonunu kullan
        return Str::slug($slug);
    }
}
