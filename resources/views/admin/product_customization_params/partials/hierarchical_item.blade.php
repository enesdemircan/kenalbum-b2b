@php
    $children = $allParams->where('customization_params_ust_id', $param->id);
    $hasChildren = $children->count() > 0;
    $paramData = $param->param;
    $categoryData = $paramData->category ?? null;
@endphp

<div class="hierarchical-item" data-param-id="{{ $param->id }}" data-level="{{ $level }}">
    <div class="item-header">
        <div class="d-flex align-items-center">
            <span class="level-indicator">{{ $level + 1 }}</span>
            <div>
                <strong>{{ $paramData->key }}</strong>
                @if($categoryData)
                    <span class="param-badge category">{{ $categoryData->title }}</span>
                @endif
                <span class="param-badge param">{{ $categoryData->type ?? 'Bilinmiyor' }}</span>
            </div>
        </div>
        
        <div class="item-actions">
            <button type="button" class="btn btn-sm btn-outline-info toggle-children" data-target="children-{{ $param->id }}" title="{{ $hasChildren ? 'Alt kategorileri gizle' : 'Alt kategorileri göster' }}">
                <i class="fas fa-chevron-{{ $hasChildren ? 'down' : 'right' }}"></i>
            </button>
            <a href="{{ route('admin.product-customization-params.edit', [$product->id, $param->id]) }}" 
               class="btn btn-sm btn-outline-primary" title="Düzenle">
                <i class="fas fa-edit"></i>
            </a>
            @if($categoryData && $categoryData->type == 'hidden')
                <button type="button" class="btn btn-sm btn-outline-success add-customer-btn" 
                        data-param-id="{{ $param->id }}" 
                        data-customization-param-id="{{ $paramData->id }}"
                        data-product-id="{{ $product->id }}"
                        data-category-title="{{ $categoryData->title }}"
                        data-modal-id="customerModal{{ $param->id }}"
                        title="Müşteri Ekle">
                    <i class="fas fa-plus"></i>
                </button>
            @endif
            <button type="button" class="btn btn-sm btn-outline-danger delete-param" 
                    data-param-id="{{ $param->id }}" title="Sil">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
    
    <div class="item-content">
        <div class="row">
            <div class="col-md-6">
                <small><strong>Başlık:</strong> {{ $categoryData->title ?? 'Bilinmiyor' }}</small><br>
                <small><strong>Tip:</strong> {{ $categoryData->type ?? 'Bilinmiyor' }}</small>
            </div>
            <div class="col-md-6">
                <small><strong>Fiyat:</strong> {{ number_format($param->price ?? 0, 2) }} TL</small><br>
                <small><strong>Zorunlu:</strong> {{ $categoryData->required ? 'Evet' : 'Hayır' }}</small>
            </div>
        </div>
        @if($categoryData->description)
            <div class="mt-2">
                <small><strong>Açıklama:</strong> {{ $categoryData->description }}</small>
            </div>
        @endif
    </div>
    
    <div class="children-container" id="children-{{ $param->id }}" style="display: {{ $hasChildren ? 'block' : 'none' }};">
        @if($hasChildren)
            @foreach($children as $childParam)
                @include('admin.product_customization_params.partials.hierarchical_item', [
                    'param' => $childParam,
                    'allParams' => $allParams,
                    'level' => $level + 1,
                    'product' => $product
                ])
            @endforeach
        @else
            <!-- Boş drop zone - sürükle-bırak için -->
            <div class="empty-drop-zone" data-parent-id="{{ $param->id }}">
                <div class="drop-zone-content">
                    <i class="fas fa-plus-circle"></i>
                    <span>Buraya sürükleyin</span>
                </div>
            </div>
        @endif
    </div>
</div>

@if($categoryData && $categoryData->type == 'hidden')
<!-- Müşteri Seçimi Modal - {{ $categoryData->title }} -->
<div class="modal fade" id="customerModal{{ $param->id }}" tabindex="-1" aria-labelledby="customerModalLabel{{ $param->id }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.customization-params-customers.add') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel{{ $param->id }}">Müşteri Seçimi - {{ $categoryData->title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="customerSelect{{ $param->id }}" class="form-label">Firma Seçin:</label>
                        <select class="form-select" name="customer_ids[]" id="customerSelect{{ $param->id }}" multiple size="8" required>
                            <option value="">Müşteri seçin...</option>
                            @foreach(\App\Models\Customer::orderBy('unvan', 'asc')->get() as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->unvan }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Birden fazla firma seçmek için Ctrl tuşunu basılı tutun.</div>
                    </div>
                    
                    <!-- Hidden inputs -->
                    <input type="hidden" name="customization_params_id" value="{{ $paramData->id }}">
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
// Bildirim göster fonksiyonu
function showNotification(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container') || document.querySelector('main');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
    }
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}
</script> 