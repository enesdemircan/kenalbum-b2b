<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\MainCategory;

use App\Models\CustomizationPivotParam;
use App\Models\ExtraSale;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['mainCategory', 'childProducts', 'parentProduct']);
        
        // Ürün ismine göre filtreleme
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        // Kategoriye göre filtreleme
        if ($request->filled('category_id')) {
            $query->where('main_category_id', $request->category_id);
        }
        
        $products = $query->orderBy('id', 'desc')->paginate(15);
        

        
        // Filtreleme için tüm kategorileri getir
        $allCategories = MainCategory::orderBy('title', 'asc')->get();
        
        // Filtre parametrelerini view'a gönder
        $filters = [
            'search' => $request->search,
            'category_id' => $request->category_id
        ];
      
        return view('admin.products.index', compact('products', 'allCategories', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Kategorileri hiyerarşik olarak organize et
        $categories = MainCategory::where('ust_id', 0)
            ->with('children')
            ->orderBy('title', 'asc')
            ->get();
        
        // Üst ürün seçimi için tüm ürünleri getir
        $availableProducts = Product::where('status', 1)
            ->orderBy('title')
            ->get();
        
        // Extra sales için tüm ürünler
        $allProducts = Product::where('status', 1)
            ->orderBy('title')
            ->get();
        
        return view('admin.products.create', compact('categories', 'availableProducts', 'allProducts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'main_category_id' => 'required|integer',
            'ust_id' => 'nullable|integer|exists:products,id',
            'price' => 'required|numeric',
            'urgent_price' => 'nullable|numeric|min:0',
            'design_service_price' => 'nullable|numeric|min:0',
            'price_difference_per_page' => 'nullable|numeric',
            'decreasing_per_page' => 'nullable|numeric',
            'min_pages' => 'nullable|integer',
            'max_pages' => 'nullable|integer',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB'a çıkardık
            'template_file' => 'nullable|file|mimes:pdf,zip,rar,psd,ai,eps,indd,doc,docx|max:51200', // Max 50MB
            'tags' => 'nullable|string',
            'stock_status' => 'required|in:in_stock,out_of_stock',
            'description' => 'nullable|string',
            'suggested_products' => 'nullable|array',
            'slug' => 'nullable|string|unique:products,slug',
        ]);
        
        $data = $request->all();
        $data['suggested_products'] = $request->suggested_products ?? [];
        
        // NOT NULL sütunlar için varsayılan değerler (boş form alanları NULL gönderir)
        $data['price_difference_per_page'] = $request->filled('price_difference_per_page') ? (int) $request->price_difference_per_page : 0;
        $data['decreasing_per_page'] = $request->filled('decreasing_per_page') ? (int) $request->decreasing_per_page : 0;
        $data['min_pages'] = $request->filled('min_pages') ? (int) $request->min_pages : null;
        $data['max_pages'] = $request->filled('max_pages') ? (int) $request->max_pages : null;
        
        // Slug oluştur
        $slug = $request->slug;
        if (empty($slug)) {
            $slug = $this->generateSlug($request->title);
        }
        $data['slug'] = $slug;
        
        // Resim yükleme işlemi - Thumbnail ve sıkıştırma ile
        if ($request->hasFile('images')) {
            $imageUrls = [];
            $thumbnailUrls = [];
            
            // Intervention Image Manager'ı başlat
            $manager = new ImageManager(new Driver());
            
            foreach ($request->file('images') as $image) {
                $fileName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $image->getClientOriginalExtension();
                $thumbnailFileName = 'thumb_' . $fileName;

                // Orijinal resmi sıkıştır ve kaydet
                $originalImage = $manager->read($image);
                $originalImage->scaleDown(1200); // Maksimum 1200px genişlik
                $originalImage->toJpeg(85)->save(public_path('images/' . $fileName)); // %85 kalite
                
                // Thumbnail oluştur
                $thumbnailImage = $manager->read($image);
                $thumbnailImage->cover(300, 300); // 300x300 thumbnail
                $thumbnailImage->toJpeg(80)->save(public_path('images/thumbnails/' . $thumbnailFileName)); // %80 kalite
                
                $imageUrls[] = '/images/' . $fileName;
                $thumbnailUrls[] = '/images/thumbnails/' . $thumbnailFileName;
            }
            
            $data['images'] = implode(',', $imageUrls);
            $data['thumbnails'] = implode(',', $thumbnailUrls);
        }
        
        // Şablon dosyası yükleme işlemi
        if ($request->hasFile('template_file')) {
            $templateFile = $request->file('template_file');
            $templateFileName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $templateFile->getClientOriginalExtension();
            
            // Storage/app/public/templates klasörüne kaydet
            $templatePath = $templateFile->storeAs('templates', $templateFileName, 'public');
            $data['template_url'] = $templatePath;
        }
        
        $product = Product::create($data);
        
        // Extra sales işlemleri
        if ($request->has('extra_sales') && is_array($request->extra_sales)) {
            foreach ($request->extra_sales as $childProductId) {
                if ($childProductId && $childProductId != $product->id) {
                    ExtraSale::create([
                        'main_product_id' => $product->id,
                        'child_product_id' => $childProductId,
                        'is_active' => true,
                        'sort_order' => 0
                    ]);
                }
            }
        }
        
        return redirect()->route('admin.products.index')->with('success', 'Ürün eklendi!');
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
        $product = Product::findOrFail($id);
        
        // Kategorileri hiyerarşik olarak organize et
        $categories = MainCategory::where('ust_id', 0)
            ->with('children')
            ->orderBy('title', 'asc')
            ->get();
        
        // Üst ürün seçimi için tüm ürünleri getir (kendisi hariç)
        $availableProducts = Product::where('status', 1)
            ->where('id', '!=', $id) // Kendisi hariç
            ->orderBy('title')
            ->get();
        
        // Extra sales için tüm ürünler (kendisi hariç)
        $allProducts = Product::where('status', 1)
            ->where('id', '!=', $id) // Kendisi hariç
            ->orderBy('title')
            ->get();
        
        // Mevcut extra sales ürünleri
        $currentExtraSales = $product->extraSales()->with('childProduct')->get();
        
        return view('admin.products.edit', compact('product', 'categories', 'availableProducts', 'allProducts', 'currentExtraSales'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'main_category_id' => 'required|integer',
            'ust_id' => 'nullable|integer|exists:products,id',
            'price' => 'required|numeric',
            'urgent_price' => 'nullable|numeric|min:0',
            'design_service_price' => 'nullable|numeric|min:0',
            'price_difference_per_page' => 'nullable|numeric',
            'decreasing_per_page' => 'nullable|numeric',
            'min_pages' => 'nullable|integer',
            'max_pages' => 'nullable|integer',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB'a çıkardık
            'template_file' => 'nullable|file|mimes:pdf,zip,rar,psd,ai,eps,indd,doc,docx|max:51200', // Max 50MB
            'tags' => 'nullable|string',
            'stock_status' => 'required|in:in_stock,out_of_stock',
            'description' => 'nullable|string',
            'suggested_products' => 'nullable|array',
            'slug' => 'nullable|string|unique:products,slug,' . $id,
        ]);
        
        $product = Product::findOrFail($id);
        $data = $request->all();
        $data['suggested_products'] = $request->suggested_products ?? [];
        
        // NOT NULL sütunlar için varsayılan değerler (boş form alanları NULL gönderir)
        $data['price_difference_per_page'] = $request->filled('price_difference_per_page') ? (int) $request->price_difference_per_page : 0;
        $data['decreasing_per_page'] = $request->filled('decreasing_per_page') ? (int) $request->decreasing_per_page : 0;
        $data['min_pages'] = $request->filled('min_pages') ? (int) $request->min_pages : null;
        $data['max_pages'] = $request->filled('max_pages') ? (int) $request->max_pages : null;
        
        // Slug oluştur
        $slug = $request->slug;
        if (empty($slug)) {
            $slug = $this->generateSlug($request->title);
        }
        $data['slug'] = $slug;
        
        // Resim yükleme işlemi - Thumbnail ve sıkıştırma ile
        if ($request->hasFile('images')) {
            $imageUrls = [];
            $thumbnailUrls = [];
            
            // Mevcut resimleri koru
            if ($product->images) {
                $existingImages = explode(',', $product->images);
                $imageUrls = array_merge($imageUrls, $existingImages);
            }
            
            if ($product->thumbnails) {
                $existingThumbnails = explode(',', $product->thumbnails);
                $thumbnailUrls = array_merge($thumbnailUrls, $existingThumbnails);
            }
            
            // Intervention Image Manager'ı başlat
            $manager = new ImageManager(new Driver());
            
            // Yeni resimleri ekle
            foreach ($request->file('images') as $image) {
                $fileName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $image->getClientOriginalExtension();
                $thumbnailFileName = 'thumb_' . $fileName;
                
                // Orijinal resmi sıkıştır ve kaydet
                $originalImage = $manager->read($image);
                $originalImage->scaleDown(1200); // Maksimum 1200px genişlik
                $originalImage->toJpeg(85)->save(public_path('images/' . $fileName)); // %85 kalite
                
                // Thumbnail oluştur
                $thumbnailImage = $manager->read($image);
                $thumbnailImage->cover(300, 300); // 300x300 thumbnail
                $thumbnailImage->toJpeg(80)->save(public_path('images/thumbnails/' . $thumbnailFileName)); // %80 kalite
                
                $imageUrls[] = '/images/' . $fileName;
                $thumbnailUrls[] = '/images/thumbnails/' . $thumbnailFileName;
            }
            
            $data['images'] = implode(',', $imageUrls);
            $data['thumbnails'] = implode(',', $thumbnailUrls);
        }
        
        // Şablon dosyası yükleme işlemi
        if ($request->hasFile('template_file')) {
            // Eski template dosyasını sil
            if ($product->template_url) {
                \Storage::disk('public')->delete($product->template_url);
            }
            
            $templateFile = $request->file('template_file');
            $templateFileName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $templateFile->getClientOriginalExtension();
            
            // Storage/app/public/templates klasörüne kaydet
            $templatePath = $templateFile->storeAs('templates', $templateFileName, 'public');
            $data['template_url'] = $templatePath;
        }
        
        $product->update($data);
        
        // Extra sales işlemleri
        // Önce mevcut extra sales'leri temizle
        ExtraSale::where('main_product_id', $product->id)->delete();
        
        // Yeni extra sales'leri ekle
        if ($request->has('extra_sales') && is_array($request->extra_sales)) {
            foreach ($request->extra_sales as $childProductId) {
                if ($childProductId && $childProductId != $product->id) {
                    ExtraSale::create([
                        'main_product_id' => $product->id,
                        'child_product_id' => $childProductId,
                        'is_active' => true,
                        'sort_order' => 0
                    ]);
                }
            }
        }
        
        return redirect()->route('admin.products.index')->with('success', 'Ürün güncellendi!');
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
        
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (auth()->user()->hasRole('editor')) {
            abort(403, 'Editorler silme işlemi yapamaz.');
        }
        $product = Product::findOrFail($id);
        
        // Ürün resimlerini ve thumbnail'larını fiziksel olarak sil
        if ($product->images) {
            $images = explode(',', $product->images);
            foreach ($images as $imageUrl) {
                $fileName = basename(trim($imageUrl));
                $filePath = public_path('images/' . $fileName);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }
        
        if ($product->thumbnails) {
            $thumbnails = explode(',', $product->thumbnails);
            foreach ($thumbnails as $thumbnailUrl) {
                $fileName = basename(trim($thumbnailUrl));
                $filePath = public_path('images/thumbnails/' . $fileName);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }
        
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Ürün ve tüm resimleri silindi!');
    }

    public function storeCustomization(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        // Önce mevcut parametreleri temizle
        CustomizationPivotParam::where('product_id', $id)->delete();
        
        // Seçilen parametreleri kaydet
        if ($request->has('selected_params')) {
            foreach ($request->selected_params as $paramId) {
                $price = $request->prices[$paramId] ?? 0;
                $parentId = $request->parent_ids[$paramId] ?? 0;
                
                // Parametrenin kategorisini otomatik al
                $param = \App\Models\CustomizationParam::find($paramId);
                $categoryId = $param ? $param->customization_category_id : null;
                
                CustomizationPivotParam::create([
                    'product_id' => $id,
                    'params_id' => $paramId,
                    'price' => $price,
                    'option1' => $price, // Geriye uyumluluk için
                    'customization_category_id' => $categoryId,
                    'customization_params_ust_id' => $parentId,
                ]);
            }
        }
        
        return redirect()->route('admin.products.index')->with('success', 'Özelleştirme parametreleri kaydedildi!');
    }

    public function deleteImage(Request $request, $productId, $imageIndex)
    {
        $product = Product::findOrFail($productId);
        
        if (!$product->images) {
            return response()->json(['success' => false, 'message' => 'Resim bulunamadı']);
        }
        
        $images = explode(',', $product->images);
        $thumbnails = $product->thumbnails ? explode(',', $product->thumbnails) : [];
        
        if (!isset($images[$imageIndex])) {
            return response()->json(['success' => false, 'message' => 'Resim indeksi geçersiz']);
        }
        
        $imageUrl = trim($images[$imageIndex]);
        $fileName = basename($imageUrl);
        $filePath = public_path('images/' . $fileName);
        
        // Orijinal dosyayı fiziksel olarak sil
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Thumbnail dosyasını da sil
        $thumbnailFileName = 'thumb_' . $fileName;
        $thumbnailFilePath = public_path('images/thumbnails/' . $thumbnailFileName);
        if (file_exists($thumbnailFilePath)) {
            unlink($thumbnailFilePath);
        }
        
        // Array'lerden resmi kaldır
        unset($images[$imageIndex]);
        if (isset($thumbnails[$imageIndex])) {
            unset($thumbnails[$imageIndex]);
        }
        
        // Yeni string'leri oluştur
        $newImages = implode(',', array_values($images));
        $newThumbnails = implode(',', array_values($thumbnails));
        
        // Veritabanını güncelle
        $product->update([
            'images' => $newImages,
            'thumbnails' => $newThumbnails
        ]);
        
        return response()->json(['success' => true, 'message' => 'Resim ve thumbnail başarıyla silindi']);
    }

    /**
     * Ürün resimlerinin sırasını güncelle.
     * Body: { order: [2, 0, 1] }  (yeni sıralamada eski indeksler)
     */
    public function reorderImages(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $request->validate([
            'order' => 'required|array|min:1',
            'order.*' => 'integer|min:0',
        ]);

        if (!$product->images) {
            return response()->json(['success' => false, 'message' => 'Sıralanacak resim yok.'], 422);
        }

        $images = array_map('trim', explode(',', $product->images));
        $thumbnails = $product->thumbnails ? array_map('trim', explode(',', $product->thumbnails)) : [];
        $newOrder = $request->input('order');

        // Validate indexes are in range and unique
        $count = count($images);
        if (count($newOrder) !== $count || count(array_unique($newOrder)) !== $count) {
            return response()->json(['success' => false, 'message' => 'Sıralama mevcut resim sayısıyla eşleşmiyor.'], 422);
        }
        foreach ($newOrder as $i) {
            if ($i < 0 || $i >= $count) {
                return response()->json(['success' => false, 'message' => 'Geçersiz indeks.'], 422);
            }
        }

        $reorderedImages = [];
        $reorderedThumbnails = [];
        foreach ($newOrder as $oldIndex) {
            $reorderedImages[] = $images[$oldIndex];
            if (isset($thumbnails[$oldIndex])) {
                $reorderedThumbnails[] = $thumbnails[$oldIndex];
            }
        }

        $product->update([
            'images' => implode(',', $reorderedImages),
            'thumbnails' => implode(',', $reorderedThumbnails),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resim sırası kaydedildi.',
            'images' => $reorderedImages,
        ]);
    }
}
