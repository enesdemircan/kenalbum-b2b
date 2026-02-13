@extends('admin.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">Ürün Düzenle</h1>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Geri
            </a>
        </div>
        
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Temel Bilgiler -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Temel Bilgiler</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Başlık</label>
                                <input type="text" name="title" id="title" class="form-control" value="{{ $product->title }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug (URL)</label>
                                <input type="text" name="slug" id="slug" class="form-control" value="{{ $product->slug }}" placeholder="Boş bırakılırsa otomatik oluşturulur">
                                <div class="form-text">Örnek: "Albüm Ürünü" → "album-urunu"</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="main_category_id" class="form-label">Ana Kategori</label>
                                <select name="main_category_id" id="main_category_id" class="form-select category-select" required>
                                    <option value="">Kategori Seçin</option>
                                    
                                    @foreach($categories as $category)
                                        <!-- Ana Kategori -->
                                        <option value="{{ $category->id }}" @if($product->main_category_id == $category->id) selected @endif>{{ $category->title }}</option>
                                        
                                        <!-- Alt Kategoriler -->
                                        @if($category->children->count() > 0)
                                            @foreach($category->children as $child)
                                                <option value="{{ $child->id }}" @if($product->main_category_id == $child->id) selected @endif>&nbsp;&nbsp;&nbsp;&nbsp;└─ {{ $child->title }}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                                <small class="text-muted">Kategoriler hiyerarşik olarak gösterilmektedir.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock_status" class="form-label">Stok Durumu</label>
                                <select name="stock_status" id="stock_status" class="form-select" required>
                                    <option value="in_stock" @if($product->stock_status == 'in_stock') selected @endif>Stokta Var</option>
                                    <option value="out_of_stock" @if($product->stock_status == 'out_of_stock') selected @endif>Stokta Yok</option>
                                </select>
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div>

            <!-- Fiyat Bilgileri -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-currency-dollar"></i> Fiyat Bilgileri</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Fiyat</label>
                                <div class="input-group">
                                    <input type="number" name="price" id="price" class="form-control" step="0.01" value="{{ $product->price }}" required>
                                    <span class="input-group-text">₺</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="urgent_price" class="form-label">Acil Üretim Fiyatı</label>
                                <div class="input-group">
                                    <input type="number" name="urgent_price" id="urgent_price" class="form-control" step="0.01" min="0" value="{{ $product->urgent_price }}">
                                    <span class="input-group-text">₺</span>
                                </div>
                                <small class="text-muted">Acil üretim için ek ücret (opsiyonel)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price_difference_per_page" class="form-label"> Artan Sayfa Yüzdesi</label>
                                <div class="input-group">
                                    <input type="number" name="price_difference_per_page" id="price_difference_per_page" class="form-control" min="0" step="1" value="{{ $product->price_difference_per_page }}">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">0 ise tek sayfa ürün (sayfa seçimi yapılmaz)</small>
                            </div>
                        </div>
                
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="decreasing_per_page" class="form-label">Azalan Sayfa Yüzdesi</label>
                                <div class="input-group">
                                    <input type="number" name="decreasing_per_page" id="decreasing_per_page" class="form-control" step="1" min="0"  value="{{ $product->decreasing_per_page ?? 0 }}">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">10 yaprak altında uygulanacak azalma yüzdesi</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="min_pages" class="form-label">Min Sayfa</label>
                                <input type="number" name="min_pages" id="min_pages" class="form-control" value="{{ $product->min_pages }}" min="0">
                                <small class="text-muted">0 ise tek sayfa ürün</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="max_pages" class="form-label">Max Sayfa</label>
                                <input type="number" name="max_pages" id="max_pages" class="form-control" value="{{ $product->max_pages }}" min="0">
                                <small class="text-muted">0 ise tek sayfa ürün</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tags" class="form-label">Etiketler</label>
                                <input type="text" name="tags" id="tags" class="form-control" placeholder="Etiketleri virgül ile ayırarak yazın" value="{{ $product->tags }}">
                                <small class="text-muted">Etiketleri virgül (,) ile ayırın</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resimler -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-images"></i> Ürün Resimleri</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                        <small class="text-muted">Yeni resimler ekleyebilirsiniz. Mevcut resimler korunacaktır. Maksimum dosya boyutu: 5MB. Yeni resimler otomatik olarak sıkıştırılacak ve thumbnail'lar oluşturulacaktır.</small>
                        
                        @if($product->images)
                            <div class="mt-3">
                                <label class="form-label">Mevcut Resimler</label>
                                <div class="row">
                                    @php
                                        $existingImages = explode(',', $product->images);
                                        $existingThumbnails = $product->thumbnails ? explode(',', $product->thumbnails) : [];
                                    @endphp
                                    @foreach($existingImages as $index => $image)
                                        <div class="col-md-3 mb-2" id="image-container-{{ $index }}">
                                            <div class="border rounded p-2 position-relative">
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm position-absolute" 
                                                        style="top: 5px; right: 5px; z-index: 10;"
                                                        onclick="deleteImage({{ $product->id }}, {{ $index }}, '{{ trim($image) }}')"
                                                        title="Resmi Sil">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                @if(isset($existingThumbnails[$index]))
                                                    <img src="{{ trim($existingThumbnails[$index]) }}" class="img-fluid" style="max-height: 100px; object-fit: cover;">
                                                @else
                                                    <img src="{{ trim($image) }}" class="img-fluid" style="max-height: 100px;">
                                                @endif
                                                <div class="small text-muted mt-1">{{ basename($image) }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <div id="imagePreview" class="mt-2"></div>
                    </div>
                </div>
            </div>

            <!-- Şablon Dosyası -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-arrow-down"></i> Şablon Dosyası</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="template_file" class="form-label">Tasarım Şablonu</label>
                        <input type="file" name="template_file" id="template_file" class="form-control" accept=".pdf,.zip,.rar,.psd,.ai,.eps,.indd,.doc,.docx">
                        <small class="text-muted">Müşterilerin ürünü tasarlaması için kullanacağı şablon dosyası. Desteklenen formatlar: PDF, ZIP, RAR, PSD, AI, EPS, INDD, DOC, DOCX. Maksimum dosya boyutu: 50MB</small>
                        
                        @if($product->template_url)
                            <div class="mt-3">
                                <label class="form-label">Mevcut Şablon</label>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ Storage::url($product->template_url) }}" class="btn btn-sm btn-primary" download>
                                        <i class="bi bi-download"></i> Şablonu İndir
                                    </a>
                                    <span class="text-muted">{{ basename($product->template_url) }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Açıklama -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-text-paragraph"></i> Açıklama</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <textarea name="description" id="description" class="form-control" rows="4" placeholder="Ürün açıklaması">{{ $product->description }}</textarea>
                    </div>
                </div>
            </div>



            <!-- Ek Satış Ürünleri -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-cart-plus"></i> Ek Satış Ürünleri</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="extra_sales" class="form-label">Ek Satış Ürünleri</label>
                        <select name="extra_sales[]" id="extra_sales" class="form-select" multiple>
                            @foreach($allProducts as $availableProduct)
                                <option value="{{ $availableProduct->id }}"
                                    @if($currentExtraSales->where('child_product_id', $availableProduct->id)->count() > 0) selected @endif>
                                    {{ $availableProduct->title }} ({{ $availableProduct->mainCategory->title }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Bu ürün sepete eklendiğinde önerilecek ek ürünler. Ctrl/Cmd tuşu ile çoklu seçim yapabilirsiniz.</small>
                    </div>
                </div>
            </div>

            <!-- Butonlar -->
            <div class="d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Güncelle
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> İptal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (this.files) {
        Array.from(this.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'd-inline-block me-2 mb-2';
                div.innerHTML = `
                    <div class="border rounded p-2" style="max-width: 150px;">
                        <img src="${e.target.result}" class="img-fluid" style="max-height: 100px;">
                        <div class="small text-muted mt-1">${file.name}</div>
                    </div>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
});

function deleteImage(productId, imageIndex, imageUrl) {
    if (confirm('Bu resmi ve thumbnail\'ını silmek istediğinize emin misiniz?')) {
        fetch(`/admin/products/${productId}/images/${imageIndex}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Resim container'ını kaldır
                const container = document.getElementById(`image-container-${imageIndex}`);
                if (container) {
                    container.remove();
                }
                
                // Başarı mesajı göster
                showAlert('success', data.message || 'Resim ve thumbnail başarıyla silindi!');
                
                // Eğer hiç resim kalmadıysa, mevcut resimler bölümünü gizle
                const remainingImages = document.querySelectorAll('[id^="image-container-"]');
                if (remainingImages.length === 0) {
                    const existingImagesSection = document.querySelector('.mt-3');
                    if (existingImagesSection) {
                        existingImagesSection.remove();
                    }
                }
            } else {
                showAlert('danger', data.message || 'Resim silinirken hata oluştu!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Resim silinirken hata oluştu!');
        });
    }
}

function showAlert(type, message) {
    // Mevcut alert'leri temizle
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Yeni alert oluştur
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Alert'i sayfanın üstüne ekle
    const container = document.querySelector('.row.justify-content-center');
    container.insertBefore(alertDiv, container.firstChild);
    
    // 3 saniye sonra otomatik kaldır
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}
</script>

@endsection 