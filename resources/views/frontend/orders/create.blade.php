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
                        
                        @php
                            // Wizard step'leri: dinamik customization step'leri + sabit Ekstralar + Sipariş Özeti
                            $hasPageCount = $product->price_difference_per_page > 0 && $product->min_pages > 0 && $product->max_pages > 0;
                            $hasExtras = isset($extraSales) && $extraSales->count() > 0;
                            $totalSteps = $stepGroups->count() + ($hasExtras ? 1 : 0) + 1; // +Özeti
                        @endphp

                        @if(!$hasPageCount)
                            <input type="hidden" name="page_count" value="1">
                        @endif

                        <!-- Wizard Step Indicator -->
                        <div class="wizard-progress mb-4" id="wizard-progress">
                            @foreach($stepGroups as $i => $step)
                                <div class="wizard-step-indicator{{ $i === 0 ? ' active' : '' }}" data-step-index="{{ $i }}">
                                    <span class="wsi-num">{{ $i + 1 }}</span>
                                    <span class="wsi-label">{{ $step['label'] }}</span>
                                </div>
                            @endforeach
                            @if($hasExtras)
                                <div class="wizard-step-indicator" data-step-index="{{ $stepGroups->count() }}">
                                    <span class="wsi-num">{{ $stepGroups->count() + 1 }}</span>
                                    <span class="wsi-label">Ekstralar</span>
                                </div>
                            @endif
                            <div class="wizard-step-indicator" data-step-index="{{ $totalSteps - 1 }}">
                                <span class="wsi-num">{{ $totalSteps }}</span>
                                <span class="wsi-label">Sipariş Özeti</span>
                            </div>
                        </div>

                        <!-- Wizard Steps Container -->
                        <div class="wizard-steps-container">

                            {{-- Customization step'leri (dinamik) --}}
                            @foreach($stepGroups as $i => $step)
                                <div class="wizard-step{{ $i === 0 ? ' active' : '' }}" data-step-index="{{ $i }}">
                                    <h3 class="wizard-step-title">{{ $step['label'] }}</h3>

                                    {{-- İlk step'in içine page_count dropdown'ı (varsa) --}}
                                    @if($i === 0 && $hasPageCount)
                                        <div class="mb-4">
                                            <h4>Yaprak Adeti</h4>
                                            <div class="form-group col-md-6">
                                                <select name="page_count" id="page-count-select" class="form-control" required>
                                                    <option value="">Yaprak adeti seçiniz</option>
                                                    @for($j = $product->min_pages; $j <= $product->max_pages; $j++)
                                                        <option value="{{ $j }}" {{ $j == 10 ? 'selected' : '' }}>
                                                            {{ $j }} yaprak ({{ $j * 2 }} sayfa)
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    @endif

                                    @foreach($step['categories'] as $categoryId => $categoryParams)
                                        @php $category = $categoryParams->first()->param->category; @endphp
                                        @include('frontend.products.customization-section', [
                                            'category' => $category,
                                            'categoryParams' => $categoryParams,
                                            'product' => $product,
                                        ])
                                    @endforeach
                                </div>
                            @endforeach

                            {{-- Ekstralar step (varsa) --}}
                            @if($hasExtras)
                                <div class="wizard-step" data-step-index="{{ $stepGroups->count() }}">
                                    <h3 class="wizard-step-title">Ekstralar</h3>
                                    <p class="text-muted">Siparişinize ek ürünler ekleyebilirsiniz. Hiçbirini istemiyorsanız "İleri" diyerek atlayabilirsiniz.</p>
                                    <div class="row g-3 extras-grid">
                                        @foreach($extraSales as $extra)
                                            <div class="col-md-4 col-sm-6">
                                                <div class="card h-100 extra-card" data-extra-product-id="{{ $extra['id'] }}">
                                                    @if(!empty($extra['images']))
                                                        @php
                                                            $extraImg = is_array($extra['images']) ? ($extra['images'][0] ?? null) : explode(',', $extra['images'])[0];
                                                        @endphp
                                                        @if($extraImg)
                                                            <img src="{{ $extraImg }}" class="card-img-top" style="height:160px;object-fit:cover;" alt="{{ $extra['title'] }}">
                                                        @endif
                                                    @endif
                                                    <div class="card-body">
                                                        <h6 class="card-title">{{ $extra['title'] }}</h6>
                                                        <p class="text-success fw-bold mb-2">{{ number_format($extra['price'], 2) }} ₺</p>
                                                        <div class="input-group input-group-sm">
                                                            <button type="button" class="btn btn-outline-secondary extra-qty-minus">−</button>
                                                            <input type="number" class="form-control text-center extra-qty" value="0" min="0" max="99">
                                                            <button type="button" class="btn btn-outline-secondary extra-qty-plus">+</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Sipariş Özeti step (her zaman) --}}
                            <div class="wizard-step" data-step-index="{{ $totalSteps - 1 }}">
                                <h3 class="wizard-step-title">Sipariş Özeti</h3>
                                <div id="wizard-summary" class="wizard-summary mb-3">
                                    {{-- JS ile dolduruluyor --}}
                                </div>

                                @if($product->urgent_price)
                                <div class="mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="urgent_production" id="urgent_production" value="1">
                                                <label class="form-check-label" for="urgent_production">
                                                    <strong>Acil Üretim</strong>
                                                    <span class="text-success-2 fw-bold">+ {{ number_format($product->urgent_price, 2) }} ₺</span>
                                                    <small class="text-muted d-block">Ek ücret karşılığında ürününüz acil olarak üretilecektir.</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

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
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg" id="wizardSubmitBtn">
                                        <i class="fas fa-shopping-cart"></i> SEPETE EKLE
                                    </button>
                                </div>

                                <div class="d-grid mt-2">
                                    <button type="button" id="completeOrderBtn" class="btn btn-success btn-lg">
                                        <i class="fas fa-bolt"></i> SİPARİŞİ TAMAMLA
                                    </button>
                                    <small class="text-muted text-center mt-1">
                                        Sepetteki diğer ürünler silinir ve doğrudan teslimat adımına ilerler
                                    </small>
                                </div>

                                <input type="hidden" name="complete_order" id="completeOrderInput" value="0">
                            </div>

                        </div>{{-- /wizard-steps-container --}}

                        <!-- Wizard Navigation -->
                        <div class="wizard-nav d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary" id="wizardPrevBtn" style="visibility:hidden;">
                                <i class="fas fa-arrow-left"></i> Geri
                            </button>
                            <button type="button" class="btn btn-primary" id="wizardNextBtn">
                                İleri <i class="fas fa-arrow-right"></i>
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

    /* ============ WIZARD STYLES ============ */
    .wizard-progress {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        padding: 16px 0;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 24px;
    }
    .wizard-step-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 20px;
        background: #f8f9fa;
        color: #6c757d;
        font-size: 14px;
        cursor: default;
        transition: background .2s, color .2s;
        flex: 1 1 auto;
        min-width: 130px;
        justify-content: center;
    }
    .wizard-step-indicator .wsi-num {
        display: inline-flex;
        width: 24px; height: 24px;
        align-items: center; justify-content: center;
        background: #dee2e6;
        color: #495057;
        border-radius: 50%;
        font-weight: 700;
        font-size: 13px;
    }
    .wizard-step-indicator.active {
        background: #0d6efd;
        color: #fff;
    }
    .wizard-step-indicator.active .wsi-num {
        background: #fff;
        color: #0d6efd;
    }
    .wizard-step-indicator.completed {
        background: #d1e7dd;
        color: #0a3622;
    }
    .wizard-step-indicator.completed .wsi-num {
        background: #198754;
        color: #fff;
    }
    .wizard-step {
        display: none;
    }
    .wizard-step.active {
        display: block;
        animation: wizardFadeIn .2s ease;
    }
    @keyframes wizardFadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .wizard-step-title {
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 2px solid #0d6efd;
        color: #0d6efd;
    }
    .extras-grid .extra-card {
        transition: transform .15s, box-shadow .15s;
    }
    .extras-grid .extra-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,.08);
    }
    .extras-grid .extra-card.has-quantity {
        border: 2px solid #198754;
    }
    .wizard-summary {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 16px 20px;
    }
    .wizard-summary .ws-row {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 1px dashed #dee2e6;
    }
    .wizard-summary .ws-row:last-child {
        border-bottom: none;
        font-weight: 700;
        padding-top: 12px;
        font-size: 1.1em;
    }
    .wizard-summary .ws-section {
        margin-top: 14px;
    }
    .wizard-summary .ws-section h6 {
        color: #0d6efd;
        margin-bottom: 6px;
    }
    @media (max-width: 768px) {
        .wizard-step-indicator .wsi-label { display: none; }
        .wizard-step-indicator { min-width: auto; padding: 8px; }
    }
</style>
   
  </main>
  @endsection
@section('scripts')

  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 
  <script>
    // ============ WIZARD STATE MACHINE ============
    document.addEventListener('DOMContentLoaded', function() {
        const wizardSteps = Array.from(document.querySelectorAll('.wizard-step[data-step-index]'));
        const wizardIndicators = Array.from(document.querySelectorAll('.wizard-step-indicator[data-step-index]'));
        const prevBtn = document.getElementById('wizardPrevBtn');
        const nextBtn = document.getElementById('wizardNextBtn');
        const submitBtn = document.getElementById('wizardSubmitBtn');
        if (!wizardSteps.length || !nextBtn) return;

        let currentStep = 0;
        const totalSteps = wizardSteps.length;

        function showStep(n) {
            currentStep = Math.max(0, Math.min(n, totalSteps - 1));
            wizardSteps.forEach((s, i) => s.classList.toggle('active', i === currentStep));
            wizardIndicators.forEach((ind, i) => {
                ind.classList.toggle('active', i === currentStep);
                ind.classList.toggle('completed', i < currentStep);
            });
            prevBtn.style.visibility = currentStep > 0 ? 'visible' : 'hidden';
            const isLast = currentStep === totalSteps - 1;
            nextBtn.style.display = isLast ? 'none' : '';
            if (submitBtn) submitBtn.style.display = '';

            if (isLast) renderWizardSummary();

            // Scroll to top of wizard area
            const progress = document.getElementById('wizard-progress');
            if (progress) progress.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function validateCurrentStep() {
            const step = wizardSteps[currentStep];
            if (!step) return true;

            // Validate native required fields
            const requiredFields = step.querySelectorAll('[required]');
            for (const f of requiredFields) {
                if (!f.value || (f.type === 'checkbox' && !f.checked)) {
                    f.focus();
                    Swal.fire({ icon: 'warning', title: 'Eksik Alan', text: 'Lütfen tüm zorunlu alanları doldurun.' });
                    return false;
                }
            }

            // Customization sections marked with data-required="1" — require selection
            const sections = step.querySelectorAll('.customization-section[data-required="1"]');
            for (const section of sections) {
                const radios = section.querySelectorAll('input[type="radio"]');
                const checks = section.querySelectorAll('input[type="checkbox"]');
                const inputs = section.querySelectorAll('input[type="text"], textarea, select');

                if (radios.length > 0) {
                    let ok = false;
                    radios.forEach(r => { if (r.checked) ok = true; });
                    if (!ok) {
                        Swal.fire({ icon: 'warning', title: 'Eksik Seçim', html: 'Lütfen <strong>' + (section.querySelector('h4')?.textContent || 'seçim') + '</strong> yapın.' });
                        section.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return false;
                    }
                } else if (checks.length > 0) {
                    let ok = false;
                    checks.forEach(c => { if (c.checked) ok = true; });
                    if (!ok) {
                        Swal.fire({ icon: 'warning', title: 'Eksik Seçim', html: 'Lütfen <strong>' + (section.querySelector('h4')?.textContent || 'seçim') + '</strong> yapın.' });
                        section.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return false;
                    }
                } else if (inputs.length > 0) {
                    let ok = false;
                    inputs.forEach(inp => { if (inp.value && inp.value.trim()) ok = true; });
                    if (!ok) {
                        Swal.fire({ icon: 'warning', title: 'Eksik Alan', html: 'Lütfen <strong>' + (section.querySelector('h4')?.textContent || 'alanı') + '</strong> doldurun.' });
                        section.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return false;
                    }
                }
            }
            return true;
        }

        prevBtn.addEventListener('click', () => showStep(currentStep - 1));
        nextBtn.addEventListener('click', () => {
            if (validateCurrentStep()) showStep(currentStep + 1);
        });

        // Indicators clickable: only allow jumping back to completed steps
        wizardIndicators.forEach((ind, idx) => {
            ind.addEventListener('click', () => {
                if (idx < currentStep) showStep(idx);
            });
            ind.style.cursor = 'pointer';
        });

        // Extras step quantity controls
        document.querySelectorAll('.extra-card').forEach(card => {
            const minus = card.querySelector('.extra-qty-minus');
            const plus = card.querySelector('.extra-qty-plus');
            const input = card.querySelector('.extra-qty');
            if (!input) return;
            const updateClass = () => {
                card.classList.toggle('has-quantity', parseInt(input.value || '0', 10) > 0);
            };
            if (minus) minus.addEventListener('click', () => {
                input.value = Math.max(0, (parseInt(input.value || '0', 10) || 0) - 1);
                updateClass();
            });
            if (plus) plus.addEventListener('click', () => {
                input.value = Math.min(99, (parseInt(input.value || '0', 10) || 0) + 1);
                updateClass();
            });
            input.addEventListener('input', updateClass);
        });

        // Build summary on last step
        function renderWizardSummary() {
            const summary = document.getElementById('wizard-summary');
            if (!summary) return;

            const productTitle = @json($product->title);
            const productPrice = @json((float) ($product->price ?? 0));
            const lines = [];

            // Page count
            const pageCountSel = document.getElementById('page-count-select');
            if (pageCountSel && pageCountSel.value) {
                const opt = pageCountSel.options[pageCountSel.selectedIndex];
                lines.push({ label: 'Yaprak Adeti', value: opt.text });
            }

            // Customization selections
            wizardSteps.forEach(step => {
                if (step.querySelector('.extras-grid')) return; // skip extras
                if (step.id === 'wizard-summary') return;
                step.querySelectorAll('.customization-section').forEach(section => {
                    const title = section.querySelector('h4')?.textContent?.trim();
                    if (!title) return;

                    const checkedRadio = section.querySelector('input[type="radio"]:checked');
                    if (checkedRadio) {
                        const lab = section.querySelector('label[for="' + checkedRadio.id + '"]');
                        const val = (lab?.innerText || lab?.textContent || checkedRadio.value).trim();
                        lines.push({ label: title, value: val.split('\n')[0].slice(0, 80) });
                        return;
                    }
                    const checkedBoxes = section.querySelectorAll('input[type="checkbox"]:checked');
                    if (checkedBoxes.length) {
                        const vals = Array.from(checkedBoxes).map(cb => {
                            const lab = section.querySelector('label[for="' + cb.id + '"]');
                            return (lab?.innerText || lab?.textContent || cb.value).trim().split('\n')[0];
                        });
                        lines.push({ label: title, value: vals.join(', ').slice(0, 120) });
                        return;
                    }
                    const txt = section.querySelector('input[type="text"], textarea, select');
                    if (txt && txt.value && txt.value.trim()) {
                        lines.push({ label: title, value: txt.value.trim().slice(0, 120) });
                    }
                });
            });

            // Extras
            const selectedExtras = [];
            document.querySelectorAll('.extra-card.has-quantity').forEach(card => {
                const qty = parseInt(card.querySelector('.extra-qty')?.value || '0', 10);
                if (qty <= 0) return;
                const title = card.querySelector('.card-title')?.textContent?.trim() || 'Ekstra';
                const priceText = card.querySelector('.text-success')?.textContent?.trim() || '';
                selectedExtras.push({ title, qty, priceText });
            });

            let html = '<div class="ws-section"><h6>Ürün</h6>';
            html += '<div class="ws-row"><span>' + productTitle + '</span><span>' + productPrice.toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + ' ₺</span></div>';
            html += '</div>';

            if (lines.length) {
                html += '<div class="ws-section"><h6>Seçimler</h6>';
                lines.forEach(l => {
                    html += '<div class="ws-row"><span>' + escapeHtml(l.label) + '</span><span>' + escapeHtml(l.value) + '</span></div>';
                });
                html += '</div>';
            }

            if (selectedExtras.length) {
                html += '<div class="ws-section"><h6>Ekstralar</h6>';
                selectedExtras.forEach(e => {
                    html += '<div class="ws-row"><span>' + escapeHtml(e.title) + ' × ' + e.qty + '</span><span>' + escapeHtml(e.priceText) + '</span></div>';
                });
                html += '</div>';
            }

            summary.innerHTML = html;
        }

        function escapeHtml(s) {
            return String(s).replace(/[&<>"']/g, m => ({
                '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
            })[m]);
        }

        // Init
        showStep(0);

        // Expose extras collection helper for form submission
        window.collectWizardExtras = function() {
            const extras = [];
            document.querySelectorAll('.extra-card.has-quantity').forEach(card => {
                const productId = card.getAttribute('data-extra-product-id');
                const qty = parseInt(card.querySelector('.extra-qty')?.value || '0', 10);
                if (productId && qty > 0) extras.push({ product_id: parseInt(productId, 10), quantity: qty });
            });
            return extras;
        };
    });
    // ============ /WIZARD STATE MACHINE ============

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
                
                // Dosya yükleme listener'ları kaldırıldı: file/files type customization
                // wizard'da render edilmiyor; dosyalar artık checkout'ta tek ZIP olarak yükleniyor.

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
        
        // "Siparişi Tamamla" butonu: tek kullanimlik intent flag'i ile submit'i tetikle
        // Submit handler intent'i okur, _completeOrderMode'a aktarir; basarili olunca cart yerine checkout'a gider
        window._completeOrderMode = false;
        var _nextSubmitIntent = null;
        var completeOrderBtn = document.getElementById('completeOrderBtn');
        if (completeOrderBtn) {
            completeOrderBtn.addEventListener('click', function() {
                _nextSubmitIntent = 'complete';
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }
            });
        }

        // Form gönderilmeden önce kontrol
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Form submit'i engelle

            // Submit kaynagina gore complete mode'u ayarla (tek seferlik intent)
            window._completeOrderMode = (_nextSubmitIntent === 'complete');
            _nextSubmitIntent = null;

            // Complete mode'u backend'e bildirmek icin hidden input'u guncelle
            var completeInput = document.getElementById('completeOrderInput');
            if (completeInput) {
                completeInput.value = window._completeOrderMode ? '1' : '0';
            }
            
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
            
            // Wizard "Ekstralar" step'inden seçilen ekstra ürünler
            try {
                const extras = (typeof window.collectWizardExtras === 'function') ? window.collectWizardExtras() : [];
                extras.forEach((e, idx) => {
                    formData.append(`extras[${idx}][product_id]`, e.product_id);
                    formData.append(`extras[${idx}][quantity]`, e.quantity);
                });
                if (extras.length) console.log('Wizard extras attached:', extras);
            } catch (e) {
                console.warn('extras collection failed:', e);
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

                Swal.close();

                if (data.success && data.cart_id) {
                    // Yeni akış: dosya yükleme cart-add seviyesinde değil, checkout'ta tek ZIP olarak yapılır.
                    // "Siparişi Tamamla" modunda direkt checkout'a yönlendir.
                    if (window._completeOrderMode) {
                        window.location.href = '{{ route("cart.checkout") }}';
                        return;
                    }

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
        var ZOOM_SELECTOR = '.radio-option-image';

        function addZoomButton(img) {
            if (img.dataset.zoomProcessed === '1') return;
            if (!img.getAttribute('src')) return;
            img.dataset.zoomProcessed = '1';

            var wrapper = document.createElement('span');
            wrapper.style.cssText = 'position:relative; display:inline-block; line-height:0;';
            img.parentNode.insertBefore(wrapper, img);
            wrapper.appendChild(img);

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'img-zoom-btn';
            btn.innerHTML = '<i class="fas fa-search-plus"></i>';
            btn.title = 'Görseli büyüt';
            btn.style.cssText = 'position:absolute; bottom:4px; right:4px; background:rgba(0,0,0,0.65); color:#fff; border:0; border-radius:50%; width:24px; height:24px; display:flex; align-items:center; justify-content:center; cursor:pointer; z-index:10; padding:0; font-size:11px; line-height:1;';

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

        function scanFor(root) {
            if (!root || !root.querySelectorAll) return;
            if (root.matches && root.matches(ZOOM_SELECTOR)) addZoomButton(root);
            root.querySelectorAll(ZOOM_SELECTOR).forEach(addZoomButton);
        }

        document.addEventListener('DOMContentLoaded', function() {
            scanFor(document);

            // AJAX ile yuklenen child-parameters icin de calissin
            var observer = new MutationObserver(function(mutations) {
                for (var i = 0; i < mutations.length; i++) {
                    var added = mutations[i].addedNodes;
                    for (var j = 0; j < added.length; j++) {
                        if (added[j].nodeType === 1) scanFor(added[j]);
                    }
                }
            });
            observer.observe(document.body, { childList: true, subtree: true });
        });
    })();
    </script>
@endsection
