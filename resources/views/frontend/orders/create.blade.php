@extends('frontend.master')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Sortable.js -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<style>
/* Acil Üretim Checkbox Stilleri */
#urgent_production:checked + label {
    color: #f39c12 !important;
}

#urgent_production:checked + label strong {
    color: #e67e22 !important;
}

.card.border-warning:hover {
    box-shadow: 0 4px 12px rgba(243, 156, 18, 0.2);
    transition: box-shadow 0.3s ease;
}

.draggable {
    cursor: move;
    user-select: none;
    transition: opacity 0.2s ease;
}

.draggable:hover {
    opacity: 0.8;
}

.draggable.dragging {
    opacity: 0.5;
    transform: rotate(5deg);
    z-index: 1000;
}

.image-item {
    transition: all 0.2s ease;
}

.image-item:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Dosya önizleme stilleri */
.file-preview-area {
    margin-top: 15px;
}

.sortable-files {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    min-height: 50px;
}

.image-item, .file-item {
    position: relative;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 10px;
    background: white;
    transition: all 0.3s ease;
    cursor: move;
    user-select: none;
}

.image-item:hover, .file-item:hover {
    border-color: #007bff;
    box-shadow: 0 4px 12px rgba(0,123,255,0.15);
}

.image-preview-container, .file-preview-container {
    position: relative;
    text-align: center;
    margin-bottom: 8px;
}

.image-overlay, .file-overlay {
    position: absolute;
    top: 5px;
    right: 5px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.image-item:hover .image-overlay,
.file-item:hover .file-overlay {
    opacity: 1;
}

.remove-file {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    padding: 0;
    font-size: 10px;
    line-height: 1;
}

.file-info {
    text-align: center;
    max-width: 100px;
    word-wrap: break-word;
}

.file-info small {
    font-size: 11px;
    line-height: 1.2;
}

/* Sürükleme sırasında stil */
.dragging {
    opacity: 0.5;
    transform: rotate(5deg);
    z-index: 1000;
}

/* Dosya yükleme alanı için placeholder */
.sortable-files:empty::before {
    content: "Dosyalar burada görünecek";
    color: #6c757d;
    font-style: italic;
    text-align: center;
    width: 100%;
    display: block;
    padding: 20px;
}

/* Sıkıştırma bilgi kutusu */
.compression-info {
    font-size: 12px;
    padding: 8px 12px;
    margin-bottom: 15px;
    border-radius: 6px;
    background-color: #e3f2fd;
    border-color: #2196f3;
    color: #0d47a1;
}

.compression-info i {
    margin-right: 5px;
}

/* Dosya bilgi alanı iyileştirmeleri */
.file-info small {
    font-size: 10px;
    line-height: 1.3;
    display: block;
    margin-bottom: 2px;
}

.text-success {
    color: #28a745 !important;
}

.text-info {
    color: #17a2b8 !important;
}

/* Sıralama input stilleri */
.order-input-container {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
}

.order-input-container label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    display: block;
}

.order-input-container label i {
    margin-right: 8px;
    color: #6f42c1;
}

.order-input {
    font-family: 'Courier New', monospace;
    font-size: 13px;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    padding: 10px;
    transition: all 0.3s ease;
}

.order-input:focus {
    border-color: #6f42c1;
    box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
    outline: none;
}

.order-input:read-only {
    background-color: #f8f9fa;
    cursor: default;
}

.order-input.editing {
    background-color: #fff3cd;
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

/* Input group buton stilleri */
.input-group .btn {
    border-radius: 0;
}

.input-group .btn:first-child {
    border-top-left-radius: 6px;
    border-bottom-left-radius: 6px;
}

.input-group .btn:last-child {
    border-top-right-radius: 6px;
    border-bottom-right-radius: 6px;
}

.edit-order-btn:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.apply-order-btn:hover {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
}

.cancel-order-btn:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

.form-text {
    font-size: 11px;
    color: #6c757d;
    margin-top: 5px;
    line-height: 1.4;
}

/* Progress bar stilleri */
.progress {
    height: 8px;
    border-radius: 4px;
    background-color: #e9ecef;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background-color: #007bff;
    transition: width 0.3s ease;
}

.progress-bar.bg-info {
    background-color: #17a2b8;
}

/* Upload progress stilleri */
.swal2-popup .progress {
    margin: 10px 0;
}

.swal2-popup .progress-bar {
    border-radius: 4px;
}
</style>
<main>
    <div class="mb-md-1 pb-md-3"></div>
    <section class="product-single container">
      <div class="row">
        <div class="container">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="row">
        
                <div class="col-md-12">
                    <h1 class="mb-3">{{ $product->title }}</h1>
                    
                    @if($product->mainCategory)
                    <p class="text-muted mb-3">
                        <i class="fas fa-tag"></i> {{ $product->mainCategory->title }}
                    </p>
                    @endif
                    
                    <div class="mb-4">
                        <h3 class="price-display mb-2">
                            @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1))
                            <span id="base-price">
                            
                                {{$product->price }}</span> 

                                TL
                                @endif
                        </h3>
                        <p class="text-muted">
                          {{ $product->description }}
                        </p>
                    </div>
        
                    <form method="POST" action="" id="product-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="price" id="total-price-input" value="{{ $product->price }}">
                        
                        @if($childProducts && $childProducts->count() > 0)
                        <div class="mb-4">
                            <h4>Paket Seçin</h4>
                            <div class="row">
                                <!-- Paket İstemiyorum seçeneği -->
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100 package-option">
                                        <div class="card-body text-center">
                                            <div class="form-check">
                                                <input class="form-check-input package-radio" 
                                                       type="radio" 
                                                       name="package_selection" 
                                                       value="current" 
                                                       id="package_current" 
                                                       data-price="{{ $product->price }}"
                                                       data-product-id="{{ $product->id }}"
                                                       checked>
                                                <label class="form-check-label" for="package_current">
                                                    @if($product->images)
                                                    <img src="{{ $product->images }}" class="img-fluid mb-2" alt="{{ $product->title }}" style="height: 150px; object-fit: cover;">
                                                    @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center mb-2" style="height: 150px;">
                                                        <i class="fas fa-image fa-3x text-muted"></i>
                                                    </div>
                                                    @endif
                                                    <h6 class="card-title">Paket İstemiyorum</h6>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Alt ürünler -->
                                @foreach($childProducts as $childProduct)
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100 package-option">
                                        <div class="card-body text-center">
                                            <div class="form-check">
                                                <input class="form-check-input package-radio" 
                                                       type="radio" 
                                                       name="package_selection" 
                                                       value="{{ $childProduct->id }}" 
                                                       id="package_{{ $childProduct->id }}" 
                                                       data-price="{{ $childProduct->price }}"
                                                       data-product-id="{{ $childProduct->id }}">
                                                <label class="form-check-label" for="package_{{ $childProduct->id }}">
                                                    @if($childProduct->images)
                                                    <img src="{{ $childProduct->images }}" class="img-fluid mb-2" alt="{{ $childProduct->title }}" style="height: 150px; object-fit: cover;">
                                                    @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center mb-2" style="height: 150px;">
                                                        <i class="fas fa-image fa-3x text-muted"></i>
                                                    </div>
                                                    @endif
                                                    <h6 class="card-title">{{ $childProduct->title }}</h6>
                                                </label>
                                            </div>
                                            <div class="mt-2">
                                                <a href="{{ route('products.show', $childProduct->slug) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i> İncele
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        @if($product->price_difference_per_page > 0 && $product->min_pages > 0 && $product->max_pages > 0)
                        <div class="mb-4">
                            <h4>Yaprak Adeti</h4>
                            <div class="form-group col-md-6">
                                <select name="page_count" id="page-count-select" class="form-control" required>
                                    <option value="">Yaprak adeti seçiniz</option>
                                    @for($i = $product->min_pages; $i <= $product->max_pages; $i++)
                                        <option value="{{ $i }}" {{ $i == 10 ? 'selected' : '' }}>
                                            {{ $i }} yaprak ({{ $i * 2 }} sayfa)
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        @else
                        <!-- Tek sayfa ürün - sayfa seçimi gerekmez -->
                        <input type="hidden" name="page_count" value="1">
                        @endif
                        @if($mainCustomizationParams->count() > 0)
                        <div class="mb-4 customize-div" id="customize-div">
                            @foreach($mainCustomizationParams as $categoryId => $categoryParams)
                                @php
                                    // Kategorinin bilgilerini al (ilk parametreden)
                                    $category = $categoryParams->first()->param->category;
                                @endphp
                          
                            @include('frontend.products.customization-section', [
                                'category' => $category,
                                'categoryParams' => $categoryParams,
                                'product' => $product
                            ])
                            @endforeach
                        </div>
                        @endif

                 
        
                        <!-- Acil Üretim Seçeneği -->
                        @if($product->urgent_price)
                        <div class="mb-4">
                            <div class="card">
                                <div class="card-body"> 
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="urgent_production" id="urgent_production" value="1">
                                        <label class="form-check-label" for="urgent_production">
                                            <strong >Acil Üretim</strong> 
                                            <span class="text-success-2 fw-bold">+ {{ number_format($product->urgent_price, 2) }} ₺</span>
                                            <small class="text-muted d-block">Ek ücret karşılığında ürününüz acil olarak üretilecektir.</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
        
                        <!-- Sipariş Notu -->
                        <div class="mb-4">
                            <label for="order_note" class="form-label">
                                <i class="fas fa-sticky-note"></i> Sipariş Notu (Opsiyonel)
                            </label>
                            <textarea class="form-control" 
                                      id="order_note" 
                                      name="order_note" 
                                      rows="3" 
                                      placeholder="Siparişiniz ile ilgili özel notlarınızı buraya yazabilirsiniz..."
                                      style="resize: vertical;"></textarea>
                            <small class="text-muted">
                                Bu not siparişinizle birlikte kaydedilecektir.
                            </small>
                        </div>
                 
                        <div class="d-grid col-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-shopping-cart"></i> SEPETE EKLE
                            </button>
                        </div>
                        
                       
                    </form>
                </div>
            </div>
        </div>
        
       
      </div>

    </section>
<br>

<style>
.product-detail-content {
    padding: 20px 0;
}

.detail-text {
    line-height: 1.6;
    color: #333;
}

.detail-text p {
    margin-bottom: 15px;
}

.detail-text h1, .detail-text h2, .detail-text h3, .detail-text h4, .detail-text h5, .detail-text h6 {
    margin-top: 20px;
    margin-bottom: 10px;
    color: #222;
}

.detail-text ul, .detail-text ol {
    margin-bottom: 15px;
    padding-left: 20px;
}

.detail-text li {
    margin-bottom: 5px;
}

.detail-text img {
    max-width: 100%;
    height: auto;
    margin: 10px 0;
}

.detail-text blockquote {
    border-left: 4px solid #007bff;
    padding-left: 15px;
    margin: 15px 0;
    font-style: italic;
    color: #666;
}

.detail-text table {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
}

.detail-text table th,
.detail-text table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.detail-text table th {
    background-color: #f8f9fa;
    font-weight: bold;
}

/* Radio button resim stilleri */
.radio-with-image {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 9px 2px 9px 7px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    background: #fff;
}

.radio-with-image:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
}

.radio-with-image .form-check-input:checked + .form-check-label {
    color: #007bff;
}

.radio-with-image .form-check-input:checked + .form-check-label .radio-option-image {
    border-color: #007bff !important;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);
}

.radio-option-image {
    transition: all 0.3s ease;
}

.radio-content {
    flex: 1;
}



.radio-content .text-success-2,
.radio-content .text-success {
    font-size: 0.9em;
    font-weight: 500;
}

.radio-content .text-muted {
    font-size: 0.8em;
    margin-top: 3px;
}

/* Seçili radio button için özel stil */
.radio-with-image .form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.radio-with-image .form-check-input:checked + .form-check-label {
    background-color: rgba(0, 123, 255, 0.05);
    border-radius: 8px;
    padding: 10px;
    margin: -10px;
}

    .image-item {
        position: relative;
        transition: all 0.2s ease;
    }
    
    .image-item:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .sortable-ghost {
        opacity: 0.5;
        background: #e3f2fd !important;
    }
    
    .sortable-chosen {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
    
    .image-item .position-absolute {
        z-index: 10;
    }
    
    /* Önizleme stilleri */
    .preview-container {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        background: #f8f9fa;
        margin-top: 15px;
    }
    
    .image-preview-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .image-item {
        cursor: move;
        transition: all 0.2s ease;
    }
    
    .image-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .image-item .card {
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }
    
    .image-item .card:hover {
        border-color: #007bff;
    }
    
    .remove-file {
        position: absolute;
        top: 5px;
        right: 5px;
        z-index: 10;
        opacity: 0.8;
        transition: opacity 0.2s ease;
    }
    
    .remove-file:hover {
        opacity: 1;
    }
    
    .sortable-ghost {
        opacity: 0.5;
        background: #e3f2fd !important;
        transform: rotate(5deg);
    }
    
    .sortable-chosen {
        box-shadow: 0 8px 25px rgba(0,0,0,0.2) !important;
        transform: scale(1.05);
        z-index: 1000;
    }
    
    .sortable-drag {
        opacity: 0.8;
        transform: rotate(5deg);
    }
    
    .file-icon {
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    /* Dosya önizleme stilleri */
    .file-preview-container {
        margin-top: 15px;
    }

    .file-preview-item {
        margin-bottom: 10px;
    }

    .file-preview-item .card {
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }

    .file-preview-item .card:hover {
        border-color: #007bff;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
    }

    .upload-progress {
        margin-top: 8px;
    }

    .upload-progress .progress {
        height: 4px;
        border-radius: 2px;
    }

    .upload-progress .progress-bar {
        transition: width 0.3s ease;
    }

    .remove-file {
        opacity: 0.7;
        transition: opacity 0.2s ease;
    }

    .remove-file:hover {
        opacity: 1;
    }

    .file-name {
        font-weight: 500;
        color: #333;
    }

    .file-size {
        font-size: 0.8em;
        color: #6c757d;
    }
</style>
   
  </main>
  @endsection
@section('scripts')

  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const form = document.getElementById('product-form');
        if (!form) {
            return;
        }
        
        // jQuery ile basit child parametre yönetimi
        function initializeCustomizationSystem() {
            
            // Event listener'ları ekle
            function attachEventListeners() {
                
                // Ana parametreler için event listener
                $('.customization-radio, .customization-select').off('change.customization').on('change.customization', function() {
                    var $element = $(this);
                    var paramId = $element.val();
                    var pivotId = $element.data('pivot-id');
                    var hasChildren = $element.attr('data-has-children') === 'true';
                    
                    // Eğer select ise, seçili option'dan pivot ID'sini al
                    if ($element.is('select') && paramId) {
                        var $selectedOption = $element.find('option:selected');
                        pivotId = $selectedOption.data('pivot-id');
                        hasChildren = $selectedOption.attr('data-has-children') === 'true';
                    }
                    
                    if (hasChildren && pivotId) {
                        // Sadece bu element'in kendi container'ını temizle
                        // Kardeş elementlerin container'larını etkilememek için daha spesifik seçim
                        var $currentSection = $element.closest('.customization-section');
                        // Sadece bu section'ın doğrudan child container'larını temizle
                        $currentSection.children('.child-parameters-container').empty().hide();
                        loadChildParameters($element, pivotId);
                    } else {
                        // Child parametreleri gizle
                        var $currentSection = $element.closest('.customization-section');
                        $currentSection.children('.child-parameters-container').empty().hide();
                    } 
                    
                    updatePrice();
                });
                
                // Child parametreler için event listener (recursive)
                $('.child-customization-radio, .child-customization-select').off('change.childCustomization').on('change.childCustomization', function() {
                    var $element = $(this);
                    var paramId = $element.val();
                    var pivotId = $element.data('pivot-id');
                    var hasChildren = $element.attr('data-has-children') === 'true';
                    
                    // Eğer select ise, seçili option'ın data-has-children değerini kontrol et
                    if ($element.is('select')) {
                        var $selectedOption = $element.find('option:selected');
                        if ($selectedOption.length > 0) {
                            hasChildren = $selectedOption.attr('data-has-children') === 'true';
                            pivotId = $selectedOption.data('pivot-id');
                        }
                    }
                    
                    console.log('Child element changed:', {
                        element: $element,
                        paramId: paramId,
                        pivotId: pivotId,
                        hasChildren: hasChildren,
                        elementType: $element.attr('type') || 'select'
                    });
                    
                    if (hasChildren && pivotId) {
                        console.log('Loading child parameters for pivotId:', pivotId);
                        // Sadece bu element'in kendi container'ını temizle
                        // Kardeş elementlerin container'larını etkilememek için daha spesifik seçim
                        var $elementContainer = $element.closest('.customization-section, .child-parameters-container');
                        // Sadece bu container'ın doğrudan child container'larını temizle
                        $elementContainer.children('.child-parameters-container').empty().hide();
                        loadChildParameters($element, pivotId);
                    } else {
                        console.log('No children or no pivotId, hiding child containers');
                        // Sadece bu element'in alt child parametrelerini gizle
                        var $elementContainer = $element.closest('.child-parameters-container');
                        $elementContainer.children('.child-parameters-container').empty().hide();
                    }
                    
                    updatePrice();
                });
                
                // Checkbox'lar için event listener
                $('.customization-checkbox, .child-customization-checkbox').off('change.checkbox').on('change.checkbox', function() {
                    updatePrice();
                });
                
                // Acil üretim checkbox'ı için event listener
                $('#urgent_production').off('change.urgent').on('change.urgent', function() {
                    updatePrice();
                });
                
                // Input'lar için event listener
                $('.customization-input, .child-customization-input').off('change.input').on('change.input', function() {
                    updatePrice();
                });
                
                // Paket seçimi için event listener
                $('.package-radio').off('change.package').on('change.package', function() {
                    updatePrice();
                    updateBasePrice();
                });
                
                // Yaprak adeti seçimi için event listener
                $('#page-count-select').off('change.pageCount').on('change.pageCount', function() {
                    updatePrice();
                    
                    // Hidden input'ları güncelle
                    var selectedPageCount = $(this).val();
                    $('input[name="page_count"]').val(selectedPageCount);
                    
                    // Debug log
                    console.log('Page count changed to:', selectedPageCount);
                    console.log('Hidden input value:', $('input[name="page_count"]').val());
                });
                
                // Dosya yükleme için event listener
                $('.customization-file').off('change.fileUpload').on('change.fileUpload', function() {
                    handleFileUpload(this);
                });
                
                // ZIP dosyası yükleme için event listener (files tipi)
                $('.customization-zip-file').off('change.zipUpload').on('change.zipUpload', function() {
                    handleZipFileUpload(this);
                });
                
            } 
             
            // Child parametreleri yükle 
            function loadChildParameters($element, paramId) {
                
                // Loading indicator göster
                var $parentSection = $element.closest('.customization-section, .child-parameters-container');
                var $container = $parentSection.find('.child-parameters-container').first();
                if ($container.length === 0) {
                    $container = $('<div class="child-parameters-container mt-3"></div>');
                    $parentSection.append($container);
                }
                
                // Önce sadece bu container'ın kendi child'larını temizle (alt seviyedeki)
                // Üst seviyedeki container'ları etkilememek için sadece kendi child'larını temizle
                $container.children('.child-parameters-container').empty().hide();
                $container.html('<div class="text-center"><small class="text-muted">Yükleniyor...</small></div>').show();
                
                $.ajax({
                    url: '/products/{{ $product->id }}/customization-params/' + paramId + '/children',
                    method: 'GET',
                    timeout: 10000, // 10 saniye timeout
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('AJAX Success:', response);
                        
                        if (response && response.html) {
                            $container.html(response.html);
                            
                            // Yeni gelen child elementlere event listener ekle
                            setTimeout(function() {
                                attachEventListeners();
                            }, 100);
                        } else {
                            console.log('No HTML in response');
                            $container.empty().hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            status: status,
                            error: error,
                            responseText: xhr.responseText,
                            statusCode: xhr.status
                        });
                        
                        // Error mesajı göster
                        $container.html('<div class="text-center text-danger"><small>Yükleme hatası. Lütfen tekrar deneyin.<br>Hata: ' + error + ' (Status: ' + xhr.status + ')</small></div>');
                        
                        // 3 saniye sonra error mesajını kaldır
                        setTimeout(function() {
                            $container.empty().hide();
                        }, 3000);
                    }
                });
            }
            
            // Base price güncelleme
            function updateBasePrice() {
                var $selectedPackage = $('.package-radio:checked');
                if ($selectedPackage.length > 0) {
                    var newBasePrice = parseFloat($selectedPackage.data('price')) || 0;
                    return newBasePrice;
                }
                return {{ $product->price }};
            }
            
            // Fiyat güncelleme fonksiyonu
            function updatePrice() {
                var basePrice = updateBasePrice();
                var totalPrice = basePrice;
                
                // Tüm seçili parametrelerin fiyatlarını topla
                $('.customization-radio:checked, .child-customization-radio:checked, .customization-checkbox:checked, .child-customization-checkbox:checked').each(function() {
                    var price = parseFloat($(this).data('price')) || 0;
                    totalPrice += price;
                });
                
                // Select'lerin fiyatlarını topla
                $('.customization-select, .child-customization-select').each(function() {
                    var $selectedOption = $(this).find('option:selected');
                    if ($selectedOption.val()) {
                        var price = parseFloat($selectedOption.data('price')) || 0;
                        totalPrice += price;
                    }
                });
                
                // Input'ların fiyatlarını topla
                $('.customization-input, .child-customization-input').each(function() {
                    if ($(this).val().trim()) {
                        var price = parseFloat($(this).data('price')) || 0;
                        totalPrice += price;
                    }
                });
                
                // Acil üretim fiyatını ekle
                if ($('#urgent_production').is(':checked')) {
                    var urgentPrice = {{ $product->urgent_price ?? 0 }};
                    totalPrice += urgentPrice;
                }
                
                // Yaprak adeti fiyatını hesapla (sadece sayfa seçimi varsa)
                var $pageCountSelect = $('#page-count-select');
                if ($pageCountSelect.length > 0 && $pageCountSelect.val()) {
                    var selectedPages = parseInt($pageCountSelect.val());
                    var basePages = 10; // 10 yaprak temel fiyat
                    
                    if (selectedPages !== basePages) {
                        // Özelleştirme fiyatlarını hesapla
                        var customizationTotal = 0;
                        $('.customization-radio:checked, .child-customization-radio:checked, .customization-checkbox:checked, .child-customization-checkbox:checked').each(function() {
                            customizationTotal += parseFloat($(this).data('price')) || 0;
                        });
                        
                        $('.customization-select, .child-customization-select').each(function() {
                            var $selectedOption = $(this).find('option:selected');
                            if ($selectedOption.val()) {
                                customizationTotal += parseFloat($selectedOption.data('price')) || 0;
                            }
                        });
                        
                        $('.customization-input, .child-customization-input').each(function() {
                            if ($(this).val().trim()) {
                                customizationTotal += parseFloat($(this).data('price')) || 0;
                            }
                        });
                        
                        // Toplam fiyat = Temel fiyat + Özelleştirme fiyatları
                        var totalBasePrice = basePrice + customizationTotal;
                        
                        // Product verilerini JavaScript'e aktar
                        var priceDifferencePerPage = {{ $product->price_difference_per_page ?? 0 }};
                        var decreasingPerPage = {{ $product->decreasing_per_page ?? 0 }};
                        
                        var pricePercentage;
                        var pageCost = 0;
                        
                        if (selectedPages < 10) {
                            // 10 yaprak altında azalma yüzdesi kullan
                            pricePercentage = decreasingPerPage  / 100;
                        } else {
                            // 10 yaprak üstünde artış yüzdesi kullan (price_difference_per_page'i yüzdeye çevir)
                            pricePercentage = priceDifferencePerPage / 100;
                        }
                        
                        // Yaprak birim fiyatı hesapla
                        var pricePerPage = totalBasePrice * pricePercentage;
                        
                        var pageDifference = selectedPages - basePages;
                        pageCost = pageDifference * pricePerPage;
                        totalPrice += pageCost;
                    }
                }
                
                // Fiyatı güncelle
                $('#base-price').text(totalPrice.toFixed(2));
                
                // Hidden input'ı da güncelle
                $('#total-price-input').val(totalPrice.toFixed(2));
                
                // Debug log
                console.log('Price updated - Display:', totalPrice.toFixed(2), 'Hidden Input:', $('#total-price-input').val());
                console.log('Hidden input element:', $('#total-price-input').length ? 'Found' : 'Not found');
                console.log('Hidden input value after update:', $('#total-price-input').val());
                
            }
            
            // Customize div'i göster
            var customizeDiv = document.getElementById('customize-div');
            if (customizeDiv) {
                customizeDiv.style.display = 'block';
            }
            
            // Sayfa yüklendiğinde fiyatı güncelle
            updatePrice();
            
            // Event listener'ları ekle
            attachEventListeners();
            
        }
        
        // DOM yüklenme sırası için güvenli başlatma
        $(document).ready(function() {
            initializeCustomizationSystem();
            
            // Eğer elementler hala yüklenmemişse tekrar dene
            setTimeout(function() {
                var customizeDiv = document.getElementById('customize-div');
                if (customizeDiv && customizeDiv.style.display === 'none') {
                    customizeDiv.style.display = 'block';
                }
            }, 100);
        });
        
        // Ek güvenlik için window.load event'i
        $(window).on('load', function() {
            // Eğer elementler hala yüklenmemişse tekrar dene
            if ($('.customization-radio').length === 0) {
                setTimeout(function() {
                    initializeCustomizationSystem();
                }, 500);
            }
        });
        
        // Required customization validation
        function validateRequiredCustomizations() {
            const requiredCategories = [];
            const missingCategories = [];
            
            // Required kategorileri topla (ana kategoriler ve child kategoriler)
            $('.customization-section').each(function() {
                const categoryId = $(this).data('category');
                const categoryTitle = $(this).find('h4').text().trim();
                const isRequired = $(this).data('required') === 1;
                const categoryType = $(this).data('type');
                
                // Hidden kategoriler için özel kontrol
                if (categoryType === 'hidden') {
                    // Hidden kategoriler sadece görünürse kontrol et
                    if ($(this).is(':visible')) {
                        if (isRequired) {
                            requiredCategories.push({
                                id: categoryId,
                                title: categoryTitle,
                                element: $(this)
                            });
                        }
                    }
                    // Görünmeyen hidden kategorileri atla
                    return;
                }
                
                if (isRequired) {
                    requiredCategories.push({
                        id: categoryId,
                        title: categoryTitle,
                        element: $(this)
                    });
                }
            });
            
            // Her required kategori için kontrol et
            requiredCategories.forEach(category => {
                const section = category.element;
                let hasValue = false;
                console.log(section);
                // Kategori tipine göre kontrol et
                const categoryType = section.data('type');
                
                if (categoryType === 'radio' || categoryType === 'hidden') {
                    // Radio/radio-like input kontrolü
                    hasValue = section.find('input[type="radio"]:checked').length > 0;
                } else if (categoryType === 'checkbox') {
                    // Checkbox kontrolü
                    hasValue = section.find('input[type="checkbox"]:checked').length > 0;
                } else if (categoryType === 'select') {
                    // Select kontrolü
                    const selectValue = section.find('select').val();
                    hasValue = selectValue && selectValue !== '';
                } else if (categoryType === 'input') {
                    // Text input kontrolü
                    const inputValue = section.find('input[type="text"]').val();
                    hasValue = inputValue && inputValue.trim() !== '';
                } else if (categoryType === 'file' || categoryType === 'files') {
                    // File input kontrolü
                    const fileInput = section.find('input[type="file"]')[0];
                    hasValue = fileInput && fileInput.files.length > 0;
                }
                
                if (!hasValue) {
                    missingCategories.push(category.title);
                }
            });
            
            // Eksik kategoriler varsa uyarı göster
            if (missingCategories.length > 0) {
                Swal.fire({
                    title: 'Eksik Bilgiler!',
                    html: `
                        <div class="text-start">
                            <p class="mb-3">Aşağıdaki zorunlu alanları doldurmanız gerekiyor:</p>
                            <ul class="text-start">
                                ${missingCategories.map(cat => `<li><strong>${cat}</strong></li>`).join('')}
                            </ul>
                        </div>
                    `,
                    icon: 'warning',
                    confirmButtonText: 'Tamam',
                    confirmButtonColor: '#3085d6'
                });
                return false;
            }
            
            return true;
        }
        
        // Form gönderilmeden önce kontrol
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Form submit'i engelle
            
            // Son fiyat kontrolü - hidden input'ları güncelle
            var selectedPageCount = $('#page-count-select').val();
            if (selectedPageCount) {
                $('input[name="page_count"]').val(selectedPageCount);
                console.log('Form submit - Final page count:', selectedPageCount);
                console.log('Form submit - Hidden input value:', $('input[name="page_count"]').val());
            }
            
          
            
            // Required validation kontrolü
            if (!validateRequiredCustomizations()) {
                console.log('Required validation failed');
                return false;
            }
            
            console.log('Required validation passed');
            
            // Loading göster
            Swal.fire({
                title: 'Sepete Ekleniyor...',
                text: 'Lütfen bekleyin',
                allowOutsideClick: false,
                showConfirmButton: false,
                showCloseButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Form verilerini topla (dosyalar hariç)
            const formData = new FormData();
            
            // Form elementlerini tara ve dosya olmayanları ekle
            const formElements = this.elements;
            for (let i = 0; i < formElements.length; i++) {
                const element = formElements[i];
                
                // Dosya input'larını atla
                if (element.type === 'file') {
                    continue;
                }
                
                // Checkbox'lar için özel kontrol
                if (element.type === 'checkbox') {
                    if (element.checked) {
                        formData.append(element.name, element.value);
                        console.log('Checkbox added:', element.name, '=', element.value);
                    }
                }
                // Radio button'lar için özel kontrol
                else if (element.type === 'radio') {
                    if (element.checked) {
                        formData.append(element.name, element.value);
                        console.log('Radio added:', element.name, '=', element.value);
                    }
                }
                // Diğer elementler
                else if (element.name && element.value !== undefined) {
                    formData.append(element.name, element.value);
                    console.log('Input added:', element.name, '=', element.value);
                }
            }
            
            console.log('Form data collected, sending to cart');
            
            // AJAX ile sepete ekle
            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Cart add response:', data);
                
                if (data.success && data.cart_id) {
                    console.log('Cart created successfully, starting file upload...');
                    // Chunk sistemi ile dosyaları yükle
                    uploadFilesWithChunks(data.cart_id, data.extra_sales);
                } else {
                    // Loading'i kapat
                    Swal.close();
                    
                    if (data.success) {
                        // Extra sales ürünleri varsa modal göster
                        if (data.extra_sales && data.extra_sales.length > 0) {
                            showExtraSalesModal(data.extra_sales);
                        } else {
                            // Başarılı mesajı göster
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı!',
                                text: 'Ürün sepete eklendi.',
                                confirmButtonText: 'Sepete Git',
                                cancelButtonText: 'Alışverişe Devam Et',
                                showCancelButton: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '{{ route("cart.index") }}';
                                }
                            });
                        }
                    } else {
                        // Hata mesajı göster
                        let errorMessage = 'Bir hata oluştu!';
                        if (data.errors && Array.isArray(data.errors)) {
                            errorMessage = '<ul style="text-align: left; margin: 0; padding-left: 20px;">';
                            data.errors.forEach(error => {
                                errorMessage += '<li>' + error + '</li>';
                            });
                            errorMessage += '</ul>';
                        } else if (data.message) {
                            errorMessage = data.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Lütfen aşağıdaki alanları doldurun:',
                            html: errorMessage,
                            confirmButtonText: 'Tamam',
                            width: '500px'
                        });
                    }
                }
            })
            .catch(error => {
                // Loading'i kapat
                Swal.close();
                
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Bir hata oluştu. Lütfen tekrar deneyin.',
                    confirmButtonText: 'Tamam'
                });
            });
        });
        

        
        // Dosya ikonu belirleme
        function getFileIcon(mimeType) {
            if (mimeType.startsWith('image/')) return 'fas fa-image';
            if (mimeType.startsWith('video/')) return 'fas fa-video';
            if (mimeType.startsWith('audio/')) return 'fas fa-music';
            if (mimeType.includes('pdf')) return 'fas fa-file-pdf';
            if (mimeType.includes('word') || mimeType.includes('document')) return 'fas fa-file-word';
            if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return 'fas fa-file-excel';
            if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) return 'fas fa-file-powerpoint';
            return 'fas fa-file';
        }

        // ZIP dosyası yükleme işlemi (files tipi için basit)
        function handleZipFileUpload(input) {
            console.log('ZIP file upload:', input);
            
            const file = input.files[0];
            const paramId = $(input).data('param-id');
            const previewContainer = $(`#file_preview_${paramId}`);
            
            if (!file) return;
            
            // Preview container'ı temizle
            previewContainer.empty();
            
            // Dosya türü kontrolü
            const allowedExtensions = ['.zip', '.rar', '.7z'];
            const fileName = file.name.toLowerCase();
            const isAllowed = allowedExtensions.some(ext => fileName.endsWith(ext));
            
            if (!isAllowed) {
                previewContainer.html(`
                    <div class="alert alert-danger alert-sm">
                        <i class="fas fa-exclamation-triangle"></i>
                        Sadece .zip, .rar veya .7z dosyaları yükleyebilirsiniz!
                    </div>
                `);
                $(input).val(''); // Input'u temizle
                return;
            }
            
            // Dosya boyutu kontrolü (200MB üzeri uyarı)
            const maxRecommendedSize = 200 * 1024 * 1024; // 200MB
            if (file.size > maxRecommendedSize) {
                const sizeInMB = Math.round(file.size / 1024 / 1024);
                Swal.fire({
                    icon: 'warning',
                    title: 'Büyük Dosya Uyarısı',
                    html: `
                        <p>Seçtiğiniz dosya <strong>${sizeInMB} MB</strong> boyutunda.</p>
                        <p class="text-muted">Büyük dosyaların yüklenmesi uzun sürebilir (5-10 dakika).</p>
                        <p>Devam etmek istiyor musunuz?</p>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Evet, Devam Et',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        $(input).val(''); // Input'u temizle
                        return;
                    }
                });
            }
            
            // Basit dosya bilgisi göster
            const fileInfo = $(`
                <div class="alert alert-success alert-sm">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-archive fa-2x me-3 text-primary"></i>
                        <div class="flex-grow-1">
                            <strong>${file.name}</strong>
                            <br>
                            <small class="text-muted">Boyut: ${formatFileSize(file.size)}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('.alert').parent().empty(); $('#param_file_${paramId}').val('');">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `);
            
            previewContainer.append(fileInfo);
            
            console.log('ZIP file ready for upload:', file.name);
        }
        
        // Dosya yükleme işlemi (file tipi için - resim vs)
        function handleFileUpload(input) {
            console.log(input);
            
            const files = input.files;
            const paramId = $(input).data('param-id');
            const categoryId = $(input).data('category-id');
            const previewContainer = $(`#file_preview_${paramId}`);
            
            if (files.length === 0) return;
            
            // Preview container'ı temizle
            previewContainer.empty();
            
            // Dosya önizleme alanı oluştur
            const previewArea = $('<div class="file-preview-area"></div>');
            const sortableContainer = $('<div class="sortable-files" data-param-id="' + paramId + '"></div>');
            
            // Sıkıştırma ayarları (server limit için optimize edildi)
            const compressionSettings = {
                maxWidth: 1920,  // Maksimum genişlik (daha gerçekçi)
                maxHeight: 1920, // Maksimum yükseklik
                quality: 0.85,   // JPEG kalitesi (0.1 - 1.0) - biraz düşürüldü
                format: 'jpeg'  // Format
            };
            
            // Sıkıştırma bilgisi göster
            const compressionInfo = $(`
                <div class="compression-info alert alert-info alert-sm mb-2">
                    <i class="fas fa-info-circle"></i>
                    <strong>Sıkıştırma Ayarları:</strong> 
                    Maksimum boyut: ${compressionSettings.maxWidth}×${compressionSettings.maxHeight}px, 
                    Kalite: ${Math.round(compressionSettings.quality * 100)}%
                </div>
            `);
            previewArea.append(compressionInfo);
            
            // Sıralama input'u ekle
            const orderInput = $(`
                <div class="order-input-container mt-3" style="display: none;"> 
                
                    <div class="input-group">
                        <input type="text" 
                               class="form-control order-input" 
                               id="order_input_${paramId}"
                               placeholder="Dosya sırası burada görünecek..."
                               readonly>
                    </div>
                   
                </div>
            `);
            previewArea.append(orderInput);
            
            // Edit butonuna event listener ekle
            orderInput.find('.edit-order-btn').on('click', function() {
                enableOrderEditing(paramId);
            });
            
            // Apply butonuna event listener ekle
            orderInput.find('.apply-order-btn').on('click', function() {
                applyManualOrder(paramId);
            });
            
            // Cancel butonuna event listener ekle
            orderInput.find('.cancel-order-btn').on('click', function() {
                cancelOrderEditing(paramId);
            });
            
            // Her dosya için önizleme oluştur
            Array.from(files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    createImagePreview(file, index, paramId, sortableContainer, compressionSettings);
                } else {
                    createFilePreview(file, index, paramId, sortableContainer);
                }
            });
            
            previewArea.append(sortableContainer);
            previewContainer.append(previewArea);
            
            // Sortable özelliğini etkinleştir
            initializeSortable(sortableContainer[0], paramId);
            
            // İlk sıralamayı göster
            setTimeout(() => {
                updateOrderInput(sortableContainer[0], paramId);
            }, 100);
        }
        
        // Resim önizleme oluştur (sıkıştırılmış)
        function createImagePreview(file, index, paramId, container, settings) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Canvas ile resmi sıkıştır
                compressImage(e.target.result, settings.maxWidth, settings.maxHeight, settings.quality, function(compressedDataUrl, compressionRatio, newDimensions) {
                    // Preview için sabit boyut (85x85)
                    const previewSize = 85;
                    
                    const imageItem = $(`
                        <div class="image-item draggable" data-file-index="${index}" data-param-id="${paramId}">
                            <div class="image-preview-container">
                                <img src="${compressedDataUrl}" class="img-thumbnail" alt="Önizleme" style="width: ${previewSize}px; height: ${previewSize}px; object-fit: cover;">
                                <div class="image-overlay">
                                    <button type="button" class="btn btn-sm btn-danger remove-file" data-file-index="${index}" data-param-id="${paramId}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="file-info">
                                <small class="text-muted">${file.name}</small>
                                <small class="text-muted d-block">${formatFileSize(file.size)}</small>
                                <small class="text-success d-block">Sıkıştırıldı: ${newDimensions.width}×${newDimensions.height}</small>
                            </div>
                        </div>
                    `);
                    
                    container.append(imageItem);
                    
                    // Dosya kaldırma event'i
                    imageItem.find('.remove-file').on('click', function() {
                        removeFile(index, paramId);
                    });
                });
            };
            reader.readAsDataURL(file);
        }
        
        // Resim sıkıştırma fonksiyonu (gelişmiş)
        function compressImage(src, maxWidth, maxHeight, quality, callback) {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();
            
            img.onload = function() {
                // Orijinal boyutları al
                let { width, height } = img;
                const originalSize = width * height;
                
                // En-boy oranını koru ve boyutları hesapla
                let newWidth, newHeight;
                
                if (width > height) {
                    if (width > maxWidth) {
                        newWidth = maxWidth;
                        newHeight = Math.round((height * maxWidth) / width);
                    } else {
                        newWidth = width;
                        newHeight = height;
                    }
                } else {
                    if (height > maxHeight) {
                        newHeight = maxHeight;
                        newWidth = Math.round((width * maxHeight) / height);
                    } else {
                        newWidth = width;
                        newHeight = height;
                    }
                }
                
                // Canvas boyutunu ayarla
                canvas.width = newWidth;
                canvas.height = newHeight;
                
                // Resmi çiz
                ctx.drawImage(img, 0, 0, newWidth, newHeight);
                
                // Format seçimi (JPEG daha iyi sıkıştırma sağlar)
                let format = 'image/jpeg';
                let finalQuality = quality;
                
                // PNG dosyaları için daha yüksek kalite
                if (src.includes('image/png')) {
                    format = 'image/png';
                    finalQuality = Math.min(quality + 0.1, 1.0); // PNG için biraz daha yüksek kalite
                }
                
                // Sıkıştırılmış resmi al
                const compressedDataUrl = canvas.toDataURL(format, finalQuality);
                
                // Sıkıştırma oranını hesapla
                const compressionRatio = Math.round((1 - (compressedDataUrl.length / (originalSize * 4))) * 100);
                
                // Yeni boyutları hazırla
                const newDimensions = {
                    width: newWidth,
                    height: newHeight
                };
                
                // Canvas'ı temizle
                canvas.width = 0;
                canvas.height = 0;
                
                callback(compressedDataUrl, compressionRatio, newDimensions);
            };
            
            img.src = src;
        }
        
        // Dosya önizleme oluştur (resim olmayan dosyalar için)
        function createFilePreview(file, index, paramId, container) {
            const fileItem = $(`
                <div class="file-item draggable" data-file-index="${index}" data-param-id="${paramId}">
                    <div class="file-preview-container">
                        <i class="${getFileIcon(file.type)} fa-2x text-primary"></i>
                        <div class="file-overlay">
                            <button type="button" class="btn btn-sm btn-danger remove-file" data-file-index="${index}" data-param-id="${paramId}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="file-info">
                        <small class="text-muted">${file.name}</small>
                        <small class="text-muted d-block">${formatFileSize(file.size)}</small>
                    </div>
                </div>
            `);
            
            container.append(fileItem);
            
            // Dosya kaldırma event'i
            fileItem.find('.remove-file').on('click', function() {
                removeFile(index, paramId);
            });
        }
        
        // Dosya kaldırma
        function removeFile(fileIndex, paramId) {
            const input = $(`#param_file_${paramId}`);
            const container = $(`.sortable-files[data-param-id="${paramId}"]`);
            const fileItem = container.find(`[data-file-index="${fileIndex}"]`);
            
            // Dosya item'ını kaldır
            fileItem.remove();
            
            // Input'tan dosyayı kaldır (FileList'i güncelle)
            const dt = new DataTransfer();
            const files = input[0].files;
            
            for (let i = 0; i < files.length; i++) {
                if (i !== parseInt(fileIndex)) {
                    dt.items.add(files[i]);
                }
            }
            
            input[0].files = dt.files;
            
            // Eğer hiç dosya kalmadıysa preview'ı temizle
            if (dt.files.length === 0) {
                $(`#file_preview_${paramId}`).empty();
            } else {
                // Sıralama input'unu güncelle
                updateOrderInput(container, paramId);
            }
        }
        
        // Sortable özelliğini etkinleştir
        function initializeSortable(container, paramId) {
            if (container) {
                new Sortable(container, {
                    animation: 150,
                    ghostClass: 'dragging',
                    onEnd: function(evt) {
                        // Sıralama değiştiğinde input'taki dosya sırasını güncelle
                        updateFileOrder(container, paramId);
                    }
                });
            }
        }
        
        // Dosya sırasını güncelle
        function updateFileOrder(container, paramId) {
            const input = $(`#param_file_${paramId}`);
            const dt = new DataTransfer();
            const originalFiles = input[0].files;
            
            // Yeni sıraya göre dosyaları yeniden düzenle
            const newOrder = [];
            container.querySelectorAll('.image-item, .file-item').forEach(item => {
                const fileIndex = parseInt(item.dataset.fileIndex);
                newOrder.push(fileIndex);
            });
            
            // Dosyaları yeni sıraya göre ekle
            newOrder.forEach(index => {
                dt.items.add(originalFiles[index]);
            });
            
            input[0].files = dt.files;
            
            // Sıralama input'unu güncelle
            updateOrderInput(container, paramId);
        }
        
        // Sıralama input'unu güncelle
        function updateOrderInput(container, paramId) {
            const orderInput = $(`#order_input_${paramId}`);
            const fileNames = [];
            
            // Sıraya göre dosya adlarını topla
            container.querySelectorAll('.image-item, .file-item').forEach((item, index) => {
                const fileName = item.querySelector('.file-info small').textContent;
                const newFileName = $(item).attr('data-new-filename');
                
                // Yeni dosya adı varsa onu kullan
                const displayName = newFileName || fileName;
                fileNames.push(`${index + 1}. ${displayName}`);
            });
            
            // Input'a sırayı yaz
            orderInput.val(fileNames.join(' → '));
        }
        
        // Sıralama düzenlemeyi etkinleştir
        function enableOrderEditing(paramId) {
            const orderInput = $(`#order_input_${paramId}`);
            const editBtn = $(`.edit-order-btn[data-param-id="${paramId}"]`);
            const applyBtn = $(`.apply-order-btn[data-param-id="${paramId}"]`);
            const cancelBtn = $(`.cancel-order-btn[data-param-id="${paramId}"]`);
            
            // Input'u düzenlenebilir yap
            orderInput.prop('readonly', false).focus();
            
            // Butonları göster/gizle
            editBtn.hide();
            applyBtn.show();
            cancelBtn.show();
            
            // Input stilini değiştir
            orderInput.addClass('editing');
        }
        
        // Manuel sıralamayı uygula
        function applyManualOrder(paramId) {
            const orderInput = $(`#order_input_${paramId}`);
            const editBtn = $(`.edit-order-btn[data-param-id="${paramId}"]`);
            const applyBtn = $(`.apply-order-btn[data-param-id="${paramId}"]`);
            const cancelBtn = $(`.cancel-order-btn[data-param-id="${paramId}"]`);
            
            // Input'u readonly yap
            orderInput.prop('readonly', true);
            
            // Butonları göster/gizle
            editBtn.show();
            applyBtn.hide();
            cancelBtn.hide();
            
            // Input stilini değiştir
            orderInput.removeClass('editing');
            
            // Manuel sıralamayı uygula
            const newOrder = orderInput.val();
            if (newOrder) {
                applyFileRenaming(paramId, newOrder);
            }
        }
        
        // Sıralama düzenlemeyi iptal et
        function cancelOrderEditing(paramId) {
            const orderInput = $(`#order_input_${paramId}`);
            const editBtn = $(`.edit-order-btn[data-param-id="${paramId}"]`);
            const applyBtn = $(`.apply-order-btn[data-param-id="${paramId}"]`);
            const cancelBtn = $(`.cancel-order-btn[data-param-id="${paramId}"]`);
            
            // Input'u readonly yap
            orderInput.prop('readonly', true);
            
            // Butonları göster/gizle
            editBtn.show();
            applyBtn.hide();
            cancelBtn.hide();
            
            // Input stilini değiştir
            orderInput.removeClass('editing');
            
            // Orijinal sıralamayı geri yükle
            const container = $(`.sortable-files[data-param-id="${paramId}"]`);
            updateOrderInput(container[0], paramId);
        }
        
        // Dosya yeniden adlandırma işlemi
        function applyFileRenaming(paramId, orderString) {
            const container = $(`.sortable-files[data-param-id="${paramId}"]`);
            const fileItems = container.find('.image-item, .file-item');
            
            // Sıralama string'ini parse et
            const orderParts = orderString.split(' → ');
            const fileOrder = [];
            
            orderParts.forEach(part => {
                const match = part.match(/^(\d+)\.\s*(.+)$/);
                if (match) {
                    const index = parseInt(match[1]) - 1;
                    const fileName = match[2];
                    fileOrder.push({ index, fileName });
                }
            });
            
            // Dosyaları yeniden adlandır (sıralı: 0.jpg, 1.jpg, 2.jpg...)
            fileOrder.forEach((item, newIndex) => {
                const fileItem = fileItems.eq(item.index);
                if (fileItem.length > 0) {
                    // Yeni dosya adı oluştur (sıralı: 0, 1, 2...)
                    const extension = getFileExtension(item.fileName);
                    const newFileName = generateSequentialFileName(newIndex, extension); // 0'dan başla
                    
                    // Dosya item'ını güncelle
                    fileItem.find('.file-info small').first().text(newFileName);
                    
                    // Data attribute'ları güncelle
                    fileItem.attr('data-new-filename', newFileName);
                    fileItem.attr('data-new-order', newIndex);
                    
                    console.log(`File renamed: ${item.fileName} → ${newFileName} (Order: ${newIndex})`);
                }
            });
            
            // Sıralama input'unu güncelle
            updateOrderInput(container[0], paramId);
        }
        
        // Dosya uzantısını al
        function getFileExtension(fileName) {
            const lastDotIndex = fileName.lastIndexOf('.');
            return lastDotIndex !== -1 ? fileName.substring(lastDotIndex) : '';
        }
        
        // Sıralı dosya adı oluştur (1.jpg, 2.jpg, 3.jpg...)
        function generateSequentialFileName(order, extension) {
            return `${order}${extension}`;
        }
        
        // Rastgele dosya adı oluştur (6 karakterli sayı) - eski sistem için
        function generateRandomFileName(extension) {
            const randomNumber = Math.floor(Math.random() * 900000) + 100000; // 100000-999999
            return `${randomNumber}${extension}`;
        }
        
        // ============================================
        // SIMPLE & RELIABLE CHUNK UPLOAD SYSTEM
        // Auto-adaptive chunk size with fallback
        // ============================================
        
        const CHUNK_SIZES = [10240, 5120, 2048, 1024, 512]; // KB: 10MB, 5MB, 2MB, 1MB, 512KB
        let currentChunkSizeIndex = 0; // Start with 10MB (index 0) - ÇOK DAHA HIZLI!
        
        const uploadStats = {
            startTime: null,
            totalFiles: 0,
            uploadedFiles: 0,
            totalChunks: 0,
            uploadedChunks: 0,
            failedAttempts: 0,
            currentChunkSize: CHUNK_SIZES[currentChunkSizeIndex]
        };

        // Main upload function - SIMPLE & CLEAN
        async function uploadFilesWithChunks(cartId, extraSales = null) {
            uploadStats.startTime = Date.now();
            const chunkSize = CHUNK_SIZES[currentChunkSizeIndex] * 1024; // KB to bytes
            const files = getAllFilesFromForm();
            
            if (files.length === 0) {
                console.log('No files to upload');
                completeOrderProcess(cartId, null, extraSales);
                return;
            } 
            
            console.log(`Starting chunk upload for ${files.length} files`);
            
            // Upload stats başlat
            uploadStats.startTime = Date.now();
            uploadStats.totalFiles = files.length;
            uploadStats.uploadedFiles = 0;
            uploadStats.totalChunks = 0;
            uploadStats.uploadedChunks = 0;
            uploadStats.failedAttempts = 0;
            
            // Upload progress başlat (ilk gösterim)
            Swal.fire({
                title: 'Dosyalar Yükleniyor...',
                html: `
                    <div class="text-center">
                        <div class="mb-3">
                            <strong>Dosya İlerlemesi:</strong> 0/${files.length} (0%)
                            <div class="progress mt-2">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                `,
                allowOutsideClick: false,
                showConfirmButton: false,
                showCloseButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Toplam chunk sayısını hesapla
            let totalChunks = 0;
            files.forEach(file => {
                totalChunks += Math.ceil(file.size / chunkSize);
            });
            
            uploadStats.totalChunks = totalChunks;
            console.log(`📊 Upload starting: ${totalChunks} chunks from ${files.length} files (chunk size: ${chunkSize/1024}KB)`);
            
            let completedFiles = 0;
            let completedChunks = 0;
            let failedFiles = [];
            
            // Her dosyayı SIRAYLA yükle (aynı anda değil!)
            for (let fileIndex = 0; fileIndex < files.length; fileIndex++) {
                const file = files[fileIndex];
                
                console.log(`📤 Starting upload for file ${fileIndex + 1}/${files.length}: ${file.name}`);
                
                // Progress callback fonksiyonu (bu dosya için)
                const onChunkProgress = () => {
                    completedChunks++;
                    const chunkProgress = Math.round((completedChunks / totalChunks) * 100);
                    updateUploadProgress(completedFiles, files.length, totalChunks, completedChunks, chunkProgress);
                };
                
                // Dosyayı yükle ve sonucu bekle (chunk progress callback ile)
                const fileCompleted = await new Promise((resolve) => {
                    uploadFileInChunks(file, fileIndex, cartId, chunkSize, onChunkProgress, resolve);
                });
                
                completedFiles++;
                
                if (fileCompleted) {
                    console.log(`✅ File ${fileIndex + 1}/${files.length} completed: ${file.name}`);
                } else {
                    failedFiles.push({ index: fileIndex, name: file.name });
                    console.error(`❌ File ${fileIndex + 1}/${files.length} failed: ${file.name}`);
                }
            }
            
            // Tüm dosyalar tamamlandı - başarı kontrolü
            console.log(`Upload summary: ${completedFiles - failedFiles.length}/${files.length} files successful`);
            
            if (failedFiles.length > 0) {
                // Bazı dosyalar başarısız
                console.error('❌ Failed files:', failedFiles);
                
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Sunucu Bağlantı Hatası!',
                    html: `
                        <div class="text-start">
                            <p><strong>Dosyalar yüklenemedi:</strong></p>
                            <ul>
                                ${failedFiles.map(f => `<li>${f.name}</li>`).join('')}
                            </ul>
                            <hr>
                            <p class="text-danger"><strong>Muhtemel Neden:</strong></p>
                            <p class="small">Sunucu bağlantınız kesildi (ERR_CONNECTION_CLOSED)</p>
                            <p class="text-muted mt-3"><strong>Çözüm Önerileri:</strong></p>
                            <ol class="small">
                                <li>Dosya boyutunu küçültün (max 50MB önerilen)</li>
                                <li>Farklı bir ağdan deneyin (WiFi → Mobil data)</li>
                                <li>Daha küçük ZIP dosyaları hazırlayın</li>
                                <li>Destek ekibiyle iletişime geçin: 05398636262</li>
                            </ol>
                        </div>
                    `,
                    width: '600px',
                    confirmButtonText: 'Tamam'
                });
                
                // Sepet item'ını sil (hatalı)
                await fetch(`/cart/remove/${cartId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                });
                
            } else {
                // Tüm dosyalar başarılı - yükleme popup'ını kapat
                const totalDuration = Date.now() - uploadStats.startTime;
                uploadStats.uploadedFiles = files.length;
                console.log('✅ All files uploaded successfully, starting merge process...');
                console.log('📊 UPLOAD STATS:', {
                    totalDuration: `${Math.round(totalDuration/1000)}s`,
                    totalFiles: uploadStats.totalFiles,
                    uploadedFiles: uploadStats.uploadedFiles,
                    totalChunks: uploadStats.totalChunks,
                    uploadedChunks: uploadStats.uploadedChunks,
                    successRate: `${Math.round((uploadStats.uploadedChunks / uploadStats.totalChunks) * 100)}%`,
                    failedAttempts: uploadStats.failedAttempts,
                    avgChunkTime: `${Math.round(totalDuration / uploadStats.totalChunks)}ms`,
                    chunkSize: `${chunkSize/1024}KB`
                });
                
                // Yükleme tamamlandı - popup'ı kapat
                Swal.close();
                
                // Merge işlemini arka planda başlat (popup olmadan)
                mergeAllFiles(cartId, extraSales);
            }
        }
        
        // Dosyayı chunk'lar halinde yükle (SEQUENTIAL - güvenlik için sırayla)
        async function uploadFileInChunks(file, fileIndex, cartId, chunkSize, onProgress, callback) {
            const totalChunksForFile = Math.ceil(file.size / chunkSize);
            let uploadedChunks = 0;
            let hasError = false;
            
            console.log(`Uploading file: ${file.name} in ${totalChunksForFile} chunks (sequential)`);
            
            // Chunk'ları SIRAYLA yükle (paralel değil - timeout sorununu önler)
            for (let chunkIndex = 0; chunkIndex < totalChunksForFile; chunkIndex++) {
                if (hasError) break;
                
                const start = chunkIndex * chunkSize;
                const end = Math.min(start + chunkSize, file.size);
                
                // Chunk'ı oluştur ve yükle (her chunk ayrı scope'ta - memory leak önleme)
                const chunk = file.slice(start, end);
                
                try {
                    // Chunk'ı yükle (5 retry ile - connection stability için)
                    const success = await uploadChunkWithRetry(
                        chunk, fileIndex, chunkIndex, totalChunksForFile, 
                        cartId, file.name, 5 // Max 5 retry with exponential backoff
                    );
                    
                    if (success) {
                        uploadedChunks++;
                        if (onProgress) onProgress();
                        console.log(`File ${fileIndex}: ${uploadedChunks}/${totalChunksForFile} chunks completed`);
                    } else {
                        hasError = true;
                        console.error(`Chunk ${chunkIndex} upload failed for ${file.name}`);
                        break;
                    }
                } catch (error) {
                    hasError = true;
                    console.error(`Chunk ${chunkIndex} exception:`, error);
                    break;
                }
                
                // Chunk'lar arası delay - MİNİMUM (HIZLI UPLOAD)
                if (chunkIndex < totalChunksForFile - 1) {
                    // Büyük chunk size (10MB) kullandığımız için delay'i minimize ediyoruz
                    // Sadece connection stability için çok kısa bir delay
                    const baseDelay = 50; // 50ms = ÇOK HIZLI! (önceden 100ms idi)
                    
                    // Her 50 chunk'ta kısa pause (connection refresh) - daha az sıklıkta
                    if ((chunkIndex + 1) % 50 === 0) {
                        console.log(`⏸️ Connection refresh after ${chunkIndex + 1} chunks (200ms pause)`);
                        await new Promise(resolve => setTimeout(resolve, 200)); // 200ms pause (önceden 500ms idi)
                    } else {
                        await new Promise(resolve => setTimeout(resolve, baseDelay)); // 50ms delay
                    }
                }
            }
            
            // Tüm chunk'lar başarılı mı?
            if (uploadedChunks === totalChunksForFile && !hasError) {
                console.log(`✅ File ${file.name} completed successfully`);
                callback(true);
            } else {
                console.error(`❌ File ${file.name} failed - uploaded ${uploadedChunks}/${totalChunksForFile} chunks`);
                callback(false);
            }
        }
        
        // Retry mekanizması ile chunk yükle - ENHANCED with exponential backoff
        async function uploadChunkWithRetry(chunk, fileIndex, chunkIndex, totalChunks, cartId, fileName, maxRetries = 5) {
            let lastError = null;
            const chunkStartTime = Date.now();
            
            for (let attempt = 1; attempt <= maxRetries; attempt++) {
                try {
                    const attemptStartTime = Date.now();
                    const success = await uploadChunk(chunk, fileIndex, chunkIndex, totalChunks, cartId, fileName);
                    const attemptDuration = Date.now() - attemptStartTime;
                    
                    if (success) {
                        if (attempt > 1) {
                            console.log(`✅ Chunk ${chunkIndex} succeeded on attempt ${attempt}/${maxRetries} (${attemptDuration}ms)`);
                            uploadStats.failedAttempts += (attempt - 1);
                        }
                        uploadStats.uploadedChunks++;
                        return true;
                    }
                    
                    // Başarısız ama exception yok - retry with exponential backoff
                    if (attempt < maxRetries) {
                        // Exponential backoff: 3s, 6s, 12s, 24s
                        const delay = Math.min(3000 * Math.pow(2, attempt - 1), 30000);
                        console.warn(`⚠️ Chunk ${chunkIndex} failed (no exception), retrying in ${delay}ms... (attempt ${attempt}/${maxRetries})`);
                        await new Promise(resolve => setTimeout(resolve, delay));
                    }
                    
                } catch (error) {
                    lastError = error;
                    const errorInfo = {
                        chunkIndex: chunkIndex,
                        attempt: attempt,
                        maxRetries: maxRetries,
                        errorMessage: error.message,
                        errorType: error.name,
                        chunkSize: chunk.size,
                        timestamp: new Date().toISOString()
                    };
                    
                    console.error(`❌ Chunk ${chunkIndex} error on attempt ${attempt}/${maxRetries}:`, errorInfo);
                    
                    if (attempt < maxRetries) {
                        // Connection closed için daha uzun bekleme
                        const isConnectionClosed = error.message?.includes('Failed to fetch') || 
                                                  error.message?.includes('NetworkError') ||
                                                  error.name === 'AbortError';
                        
                        if (isConnectionClosed) {
                            // 502 Bad Gateway için HIZLI retry (CloudFlare timeout)
                            const delay = 1000 * attempt; // 1s, 2s, 3s, 4s, 5s (HIZLI!)
                            console.warn(`🔌 502 BAD GATEWAY - Quick retry in ${delay}ms (attempt ${attempt}/${maxRetries})`);
                            await new Promise(resolve => setTimeout(resolve, delay));
                        } else {
                            // Diğer hatalar için normal backoff
                            const delay = 2000 * attempt;
                            await new Promise(resolve => setTimeout(resolve, delay));
                        }
                    }
                }
            }

            // Tüm retry'lar başarısız
            const failureInfo = {
                chunkIndex: chunkIndex,
                totalAttempts: maxRetries,
                lastError: lastError?.message,
                lastErrorType: lastError?.name,
                chunkSize: chunk.size,
                totalDuration: Date.now() - chunkStartTime
            };
            
            console.error(`❌ Chunk ${chunkIndex} PERMANENTLY FAILED after ${maxRetries} attempts`, failureInfo);
            uploadStats.failedAttempts += maxRetries;
            
            return false;
        }
        
        // Tek chunk yükle (Promise döndürür) - FIXED: Connection stability
        function uploadChunk(chunk, fileIndex, chunkIndex, totalChunks, cartId, fileName) {
            return new Promise(async (resolve) => {
                // AbortController manuel oluştur (timeout yerine daha güvenilir)
                const controller = new AbortController();
                const timeoutId = setTimeout(() => {
                    console.warn(`⏱️ Chunk ${chunkIndex} timeout (60s), aborting...`);
                    controller.abort();
                }, 60000); // 60 saniye (CloudFlare proxy timeout 100s, bizim 60s)
                
                try {
                    // FormData oluştur (her seferinde yeni)
                    const formData = new FormData();
                    
                    // Chunk'ı file olarak ekle (Blob yerine File daha stabil)
                    const chunkFile = new File([chunk], `chunk_${chunkIndex}.tmp`, {
                        type: 'application/octet-stream'
                    });
                    
                    formData.append('chunk', chunkFile);
                    formData.append('file_index', fileIndex);
                    formData.append('chunk_index', chunkIndex);
                    formData.append('total_chunks', totalChunks);
                    formData.append('cart_id', cartId);
                    formData.append('file_name', fileName);

                    console.log(`📤 Uploading chunk ${chunkIndex}/${totalChunks} (${chunk.size} bytes)`);

                    // Fetch ile gönder
                    const response = await fetch('/upload-chunk', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || 
                                            document.querySelector('input[name="_token"]')?.value || '',
                            'Accept': 'application/json',
                            // Connection'ı her request'te temizle (pool sorununu önler)
                            'Connection': 'close'
                        },
                        signal: controller.signal,
                        // Credentials ve cache ayarları
                        credentials: 'same-origin',
                        cache: 'no-store',
                        keepalive: false // Connection pool kullanma
                    });
                    
                    clearTimeout(timeoutId);
                    
                    console.log(`📥 Chunk ${chunkIndex} response:`, {
                        status: response.status,
                        ok: response.ok
                    });
                    
                    if (!response.ok) {
                        const text = await response.text();
                        console.error('❌ HTTP Error:', {
                            status: response.status,
                            body: text.substring(0, 200)
                        });
                        resolve(false);
                        return;
                    }
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        console.log(`✅ Chunk ${chunkIndex} uploaded successfully`);
                        resolve(true);
                    } else {
                        console.error('❌ Chunk upload failed:', data.message);
                        resolve(false);
                    }
                    
                } catch (error) {
                    clearTimeout(timeoutId);
                    
                    // Timeout vs Network error ayır
                    if (error.name === 'AbortError') {
                        console.error(`⏱️ Chunk ${chunkIndex} TIMEOUT (90s)`);
                    } else if (error.message === 'Failed to fetch') {
                        console.error(`🔌 Chunk ${chunkIndex} CONNECTION CLOSED:`, {
                            error: error.message,
                            chunkSize: chunk.size,
                            fileIndex: fileIndex,
                            chunkIndex: chunkIndex
                        });
                    } else {
                        console.error(`❌ Chunk ${chunkIndex} ERROR:`, {
                            type: error.constructor.name,
                            message: error.message
                        });
                    }
                    
                    resolve(false);
                }
            });
        }
        
        // Tüm dosyaları merge et - FIXED: Connection stability
        function mergeAllFiles(cartId, extraSales = null) {
            console.log('Starting file merge process...');
            
            // Merge işlemi arka planda devam ediyor - popup gösterme
            
            fetch('/merge-files', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Connection': 'close' // Connection pool sorununu önle
                },
                body: JSON.stringify({
                    cart_id: cartId
                }),
                credentials: 'same-origin',
                cache: 'no-store',
                keepalive: false
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Files merged successfully, creating ZIP...');
                    createZipFile(cartId, extraSales);
                } else {
                    console.error('Merge failed:', data.message);
                    showUploadError('Dosya birleştirme hatası: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Merge error:', error);
                showUploadError('Dosya birleştirme hatası');
            });
        }
        
        // ZIP dosyasını R2'ye yükle - FIXED: Connection stability
        function createZipFile(cartId, extraSales = null) {
            console.log('Uploading to R2...');
            
            // ZIP işlemi arka planda devam ediyor - popup gösterme
            
            fetch('/create-zip', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Connection': 'close' // Connection pool sorununu önle
                },
                body: JSON.stringify({
                    cart_id: cartId
                }),
                credentials: 'same-origin',
                cache: 'no-store',
                keepalive: false
            })
            .then(response => response.json())
            .then(data => {
                console.log('ZIP creation response:', data);
                
                if (data.success) {
                    if (data.status === 'completed') {
                        // ZIP işlemi tamamlandı (sync mode)
                        console.log('✅ ZIP created successfully (sync)');
                        
                        // Extra sales kontrolü
                        if (extraSales && extraSales.length > 0) {
                            showExtraSalesModal(extraSales);
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı!',
                                text: 'Ürün sepete eklendi ve dosyalar başarıyla işlendi.',
                                confirmButtonText: 'Sepete Git',
                                cancelButtonText: 'Alışverişe Devam Et',
                                showCancelButton: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '{{ route("cart.index") }}';
                                }
                            });
                        }
                    } else if (data.status === 'processing') {
                        // Arka planda işleniyor (async mode - fallback)
                        // Popup açmadan sessizce polling yap
                        console.log('ZIP processing started in background (silent polling)');
                        startZipStatusPollingSilent(cartId, extraSales);
                    }
                } else {
                    console.error('ZIP creation failed:', data.message);
                    showUploadError('ZIP oluşturma hatası: ' + data.message);
                }
            })
            .catch(error => {
                console.error('ZIP creation error:', error);
                showUploadError('ZIP oluşturma hatası');
            });
        }
        

        
        // ZIP işlemi durumunu sessizce takip et (popup açmadan)
        function startZipStatusPollingSilent(cartId, extraSales = null) {
            let pollCount = 0;
            const maxPolls = 60; // Maksimum 60 kez (5 dakika, 5 saniyede bir)
            
            console.log('Starting silent ZIP status polling...');
            
            const pollInterval = setInterval(function() {
                pollCount++;
                
                fetch(`/check-zip-status/${cartId}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('ZIP status:', data);
                        
                        if (data.success && data.status === 'completed') {
                            // İşlem tamamlandı
                            clearInterval(pollInterval);
                            console.log('✅ ZIP processing completed (silent polling)');
                            
                            // Extra sales kontrolü
                            if (extraSales && extraSales.length > 0) {
                                showExtraSalesModal(extraSales);
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Başarılı!',
                                    text: 'Ürün sepete eklendi ve dosyalar başarıyla işlendi.',
                                    confirmButtonText: 'Sepete Git',
                                    cancelButtonText: 'Alışverişe Devam Et',
                                    showCancelButton: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = '{{ route("cart.index") }}';
                                    }
                                });
                            }
                        } else if (data.status === 'error' || data.status === 'failed') {
                            // İşlem başarısız
                            clearInterval(pollInterval);
                            console.error('❌ ZIP processing failed');
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'İşlem Başarısız!',
                                text: 'Dosya işleme sırasında bir hata oluştu. Lütfen tekrar deneyin veya destek ekibiyle iletişime geçin.',
                                confirmButtonText: 'Tamam'
                            });
                        } else if (pollCount >= maxPolls) {
                            // Timeout (çok uzun sürdü)
                            clearInterval(pollInterval);
                            console.warn('⏱️ ZIP processing timeout (silent polling)');
                            
                            Swal.fire({
                                icon: 'warning',
                                title: 'İşlem Devam Ediyor',
                                html: `
                                    <p>Dosyalarınız hala işleniyor.</p>
                                    <p class="text-muted">Büyük dosyaların işlenmesi zaman alabilir.</p>
                                    <p><strong>Ürün sepetinize eklendi</strong>, işlem tamamlandığında bilgilendirileceksiniz.</p>
                                `,
                                confirmButtonText: 'Sepete Git',
                                cancelButtonText: 'Alışverişe Devam Et',
                                showCancelButton: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '{{ route("cart.index") }}';
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Status check error:', error);
                        // Hata olsa bile devam et (retry)
                    });
                
            }, 5000); // 5 saniyede bir kontrol et
        }

        // Sipariş işlemini tamamla
        function completeOrderProcess(cartId, zipPath = null, extraSales = null) {
            console.log('Order process completed');
             
            // Loading'i kapat
            Swal.close();
            
            // Extra sales kontrolü
            if (extraSales && extraSales.length > 0) {
                console.log('Extra sales available, showing modal');
                showExtraSalesModal(extraSales);
            } else {
                // Başarı mesajı göster
                Swal.fire({  
                    icon: 'success',
                    title: 'Başarılı!',
                    text: 'Ürün sepete eklendi ve dosyalar yüklendi.',
                    confirmButtonText: 'Sepete Git',
                    cancelButtonText: 'Alışverişe Devam Et',
                    showCancelButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("cart.index") }}';
                    }
                });
            }
        }
        
        // Upload progress güncelle
        function updateUploadProgress(completedFiles, totalFiles, totalChunks, completedChunks, chunkProgress, status = null) {
            if (status) {
                Swal.update({
                    title: 'Dosyalar İşleniyor...',
                    html: `
                        <div class="text-center">
                            <p>${status}</p>
                        </div>
                    `
                });
            } else {
                Swal.update({
                    html: `
                        <div class="text-center">
                            <div class="mb-3">
                                <strong>Yükleme İlerlemesi:</strong> ${completedChunks}/${totalChunks} (${chunkProgress}%)
                                <div class="progress mt-2">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: ${chunkProgress}%"></div>
                                </div>
                                <small class="text-muted mt-2 d-block">Dosya: ${completedFiles}/${totalFiles}</small>
                            </div>
                        </div>
                    `
                });
            }
        }
        
        // Chunk progress güncelle
        function updateChunkProgress(fileIndex, completedChunks, totalChunks) {
            console.log(`File ${fileIndex}: ${completedChunks}/${totalChunks} chunks completed`);
        }
        
        // Upload hatası göster
        function showUploadError(message) {
            Swal.close();
            
            Swal.fire({
                icon: 'error',
                title: 'Yükleme Hatası!',
                text: message,
                confirmButtonText: 'Tamam'
            });
        }
        
        // Form'dan tüm dosyaları al
        function getAllFilesFromForm() {
            const files = [];
            
            // Tüm file input'ları tara
            document.querySelectorAll('input[type="file"]').forEach(input => {
                if (input.files && input.files.length > 0) {
                    const inputType = $(input).data('input-type');
                    
                    Array.from(input.files).forEach(file => {
                        // ZIP dosyası mı kontrol et (files tipi)
                        if (inputType === 'zip' || $(input).hasClass('customization-zip-file')) {
                            // ZIP dosyası - direkt ekle, yeniden adlandırma yok
                            files.push(file);
                            console.log(`ZIP file added: ${file.name}`);
                        } else {
                            // Normal dosya (file tipi) - yeniden adlandırma kontrol et
                            const paramId = $(input).data('param-id');
                            const container = $(`.sortable-files[data-param-id="${paramId}"]`);
                            
                            if (container.length > 0) {
                                // Dosya item'ını bul
                                const fileItems = container.find('.image-item, .file-item');
                                const fileIndex = Array.from(fileItems).findIndex(item => 
                                    item.querySelector('.file-info small').textContent === file.name
                                );
                                
                                if (fileIndex !== -1) {
                                    const fileItem = fileItems.eq(fileIndex);
                                    const newFileName = fileItem.attr('data-new-filename');
                                    
                                    if (newFileName) {
                                        // Yeni dosya adı ile File objesi oluştur
                                        const renamedFile = new File([file], newFileName, {
                                            type: file.type,
                                            lastModified: file.lastModified
                                        });
                                        files.push(renamedFile);
                                        console.log(`File renamed for upload: ${file.name} → ${newFileName}`);
                                    } else {
                                        files.push(file);
                                    }
                                } else {
                                    files.push(file);
                                }
                            } else {
                                files.push(file);
                            }
                        }
                    });
                }
            });
            
            console.log(`Total files collected: ${files.length}`);
            return files;
        }


      
        // Dosya boyutunu formatla
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        // Extra Sales Modal
        function showExtraSalesModal(extraSales) {
            if (extraSales && extraSales.length > 0) {
                // AJAX ile modal HTML'ini yükle
                fetch('/modal/extra-sales', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        extra_sales: extraSales
                    })
                })
                .then(response => response.text())
                .then(html => {
                    // Eski modal'ı kaldır
                    $('#extraSalesModal').remove();
                    
                    // Yeni modal'ı ekle
                    $('body').append(html);
                    
                    // Modal'ı göster
                    $('#extraSalesModal').modal('show');
                })
                .catch(error => {
                    console.error('Modal yükleme hatası:', error);
                    // Hata durumunda basit bir modal göster
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Ürün sepete eklendi.',
                        confirmButtonText: 'Sepete Git',
                        cancelButtonText: 'Alışverişe Devam Et',
                        showCancelButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route("cart.index") }}';
                        }
                    });
                });
            }
        }
        

        // Başarı mesajını göster
        function showSuccessMessage() {
            Swal.fire({
                icon: 'success',
                title: 'Başarılı!',
                text: 'Ürün sepete eklendi ve dosyalar yüklendi.',
                confirmButtonText: 'Sepete Git',
                cancelButtonText: 'Alışverişe Devam Et',
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("cart.index") }}';
                }
            });
        }

        // Şablon indirme linki güncelleme - Ebat seçildiğinde
        $(document).on('change', '.customization-select[data-category="1"], .customization-radio[data-category-id="1"]', function() {
            const productId = {{ $product->id }};
            let sizeTitle = '';

            // Select ise
            if ($(this).is('select')) {
                sizeTitle = $(this).find('option:selected').data('title');
            } 
            // Radio button ise
            else if ($(this).is('input[type="radio"]')) {
                sizeTitle = $(this).data('title');
            }
            
            if (sizeTitle) {
                // "20x50 Albüm" -> "20x50" (boşluktan önceki kısmı al)
                const sizePart = sizeTitle.split(' ')[0].toUpperCase();
                
                // Template URL'ini oluştur
                const templateUrl = `/storage/templates/${productId}/${sizePart}.psd`;
                
                // Link'i güncelle
                $('#template-link').attr('href', templateUrl);
                $('#template-download-link').show();
            } else {
                $('#template-download-link').hide();
            }
        });

    });
    </script>

    <script>
    (function() {
        function addZoomButton(img) {
            if (img.dataset.zoomProcessed === '1') return;
            if (!img.getAttribute('src')) return;
            img.dataset.zoomProcessed = '1';

            var wrapper = document.createElement('span');
            wrapper.style.cssText = 'position:relative; display:inline-block;';
            img.parentNode.insertBefore(wrapper, img);
            wrapper.appendChild(img);

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'img-zoom-btn';
            btn.innerHTML = '<i class="fas fa-search-plus"></i>';
            btn.title = 'Görseli büyüt';
            btn.style.cssText = 'position:absolute; bottom:6px; right:6px; background:rgba(0,0,0,0.65); color:#fff; border:0; border-radius:50%; width:32px; height:32px; display:flex; align-items:center; justify-content:center; cursor:pointer; z-index:10; padding:0; font-size:14px; line-height:1;';

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var src = img.getAttribute('src');
                if (window.Swal) {
                    Swal.fire({
                        imageUrl: src,
                        imageAlt: img.alt || 'Görsel',
                        showConfirmButton: false,
                        showCloseButton: true,
                        background: '#fff',
                        width: 'auto',
                        padding: '1em'
                    });
                } else {
                    window.open(src, '_blank');
                }
            });

            wrapper.appendChild(btn);
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.package-option img').forEach(addZoomButton);
        });
    })();
    </script>
@endsection