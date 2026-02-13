@extends('admin.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}">
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Ürün Düzenle</h1>
        <p class="page-subtitle">{{ $product->title }}</p>
    </div>
    <a href="{{ route('admin.products.index') }}" class="btn-material btn-material-secondary">
        <span class="material-icons">arrow_back</span>
        Geri Dön
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Temel Bilgiler -->
            <div class="material-card-elevated mb-4">
                <div class="material-card-header">
                    <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">info</span>Temel Bilgiler</h5>
                </div>
                <div class="material-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Başlık</label>
                                <input type="text" name="title" id="title" class="form-control form-control-material" value="{{ $product->title }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug (URL)</label>
                                <input type="text" name="slug" id="slug" class="form-control form-control-material" value="{{ $product->slug }}" placeholder="Boş bırakılırsa otomatik oluşturulur">
                                <div class="form-text">Örnek: "Albüm Ürünü" → "album-urunu"</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="main_category_id" class="form-label">Ana Kategori</label>
                                <select name="main_category_id" id="main_category_id" class="form-select form-control-material category-select" required>
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
                                <select name="stock_status" id="stock_status" class="form-select form-control-material" required>
                                    <option value="in_stock" @if($product->stock_status == 'in_stock') selected @endif>Stokta Var</option>
                                    <option value="out_of_stock" @if($product->stock_status == 'out_of_stock') selected @endif>Stokta Yok</option>
                                </select>
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div>

            <!-- Fiyat Bilgileri -->
            <div class="material-card-elevated mb-4">
                <div class="material-card-header">
                    <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">payments</span>Fiyat Bilgileri</h5>
                </div>
                <div class="material-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Fiyat</label>
                                <div class="input-group">
                                    <input type="number" name="price" id="price" class="form-control form-control-material" step="0.01" value="{{ $product->price }}" required>
                                    <span class="input-group-text">₺</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="urgent_price" class="form-label">Acil Üretim Fiyatı</label>
                                <div class="input-group">
                                    <input type="number" name="urgent_price" id="urgent_price" class="form-control form-control-material" step="0.01" min="0" value="{{ $product->urgent_price }}">
                                    <span class="input-group-text">₺</span>
                                </div>
                                <small class="text-muted">Acil üretim için ek ücret (opsiyonel)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price_difference_per_page" class="form-label"> Artan Sayfa Yüzdesi</label>
                                <div class="input-group">
                                    <input type="number" name="price_difference_per_page" id="price_difference_per_page" class="form-control form-control-material" min="0" step="1" value="{{ $product->price_difference_per_page }}">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">0 ise tek sayfa ürün (sayfa seçimi yapılmaz)</small>
                            </div>
                        </div>
                
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="decreasing_per_page" class="form-label">Azalan Sayfa Yüzdesi</label>
                                <div class="input-group">
                                    <input type="number" name="decreasing_per_page" id="decreasing_per_page" class="form-control form-control-material" step="1" min="0" value="{{ $product->decreasing_per_page ?? 0 }}">
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
                                <input type="number" name="min_pages" id="min_pages" class="form-control form-control-material" value="{{ $product->min_pages }}" min="0">
                                <small class="text-muted">0 ise tek sayfa ürün</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="max_pages" class="form-label">Max Sayfa</label>
                                <input type="number" name="max_pages" id="max_pages" class="form-control form-control-material" value="{{ $product->max_pages }}" min="0">
                                <small class="text-muted">0 ise tek sayfa ürün</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tags" class="form-label">Etiketler</label>
                                <input type="text" name="tags" id="tags" class="form-control form-control-material" placeholder="Etiketleri virgül ile ayırarak yazın" value="{{ $product->tags }}">
                                <small class="text-muted">Etiketleri virgül (,) ile ayırın</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resimler -->
            <div class="material-card-elevated mb-4">
                <div class="material-card-header">
                    <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">image</span>Ürün Resimleri</h5>
                </div>
                <div class="material-card-body">
                    <div class="mb-3">
                        <input type="file" name="images[]" id="images" class="form-control form-control-material" multiple accept="image/*">
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
                                                        class="btn-material btn-material-danger btn-sm position-absolute" 
                                                        style="top: 5px; right: 5px; z-index: 10;"
                                                        onclick="deleteImage({{ $product->id }}, {{ $index }}, '{{ trim($image) }}')"
                                                        title="Resmi Sil">
                                                    <span class="material-icons" style="font-size:18px">delete</span>
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
            <div class="material-card-elevated mb-4">
                <div class="material-card-header">
                    <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">description</span>Şablon Dosyası</h5>
                </div>
                <div class="material-card-body">
                    <div class="mb-3">
                        <label for="template_file" class="form-label">Tasarım Şablonu</label>
                        <input type="file" name="template_file" id="template_file" class="form-control form-control-material" accept=".pdf,.zip,.rar,.psd,.ai,.eps,.indd,.doc,.docx">
                        <small class="text-muted">Müşterilerin ürünü tasarlaması için kullanacağı şablon dosyası. Desteklenen formatlar: PDF, ZIP, RAR, PSD, AI, EPS, INDD, DOC, DOCX. Maksimum dosya boyutu: 50MB</small>
                        
                        @if($product->template_url)
                            <div class="mt-3">
                                <label class="form-label">Mevcut Şablon</label>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ Storage::url($product->template_url) }}" class="btn-material btn-material-primary btn-sm" download>
                                        <span class="material-icons" style="font-size:18px">download</span> Şablonu İndir
                                    </a>
                                    <span class="text-muted">{{ basename($product->template_url) }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Açıklama -->
            <div class="material-card-elevated mb-4">
                <div class="material-card-header">
                    <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">notes</span>Açıklama</h5>
                </div>
                <div class="material-card-body">
                    <div class="mb-3">
                        <textarea name="description" id="description" class="form-control form-control-material" rows="4" placeholder="Ürün açıklaması">{{ $product->description }}</textarea>
                    </div>
                </div>
            </div>



            <!-- Ek Satış Ürünleri -->
            <div class="material-card-elevated mb-4">
                <div class="material-card-header">
                    <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">add_shopping_cart</span>Ek Satış Ürünleri</h5>
                </div>
                <div class="material-card-body">
                    <div class="mb-3">
                        <label for="extra_sales" class="form-label">Ek Satış Ürünleri</label>
                        <select name="extra_sales[]" id="extra_sales" class="form-select form-control-material" multiple>
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
                <a href="{{ route('admin.products.index') }}" class="btn-material btn-material-secondary">
                    <span class="material-icons">close</span>
                    İptal
                </a>
                <button type="submit" class="btn-material btn-material-success">
                    <span class="material-icons">save</span>
                    Güncelle
                </button>
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
    alertDiv.className = `material-alert material-alert-${type === 'success' ? 'success' : type === 'danger' ? 'danger' : 'info'} alert-dismissible fade show`;
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