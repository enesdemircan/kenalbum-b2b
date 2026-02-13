@php
    $paramData = $pivotParam->param;
    $categoryData = $paramData->category ?? null;
@endphp

<div class="param-details">
    <div class="mb-3">
        <h6 class="fw-bold">{{ $paramData->key }}</h6>
        @if($categoryData)
            <span class="badge bg-primary">{{ $categoryData->title }}</span>
        @endif
        <span class="badge bg-secondary">{{ $categoryData->type ?? 'Bilinmiyor' }}</span>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-2">
                <small class="text-muted">Başlık:</small><br>
                <strong>{{ $categoryData->title ?? 'Bilinmiyor' }}</strong>
            </div>
            
            <div class="mb-2">
                <small class="text-muted">Tip:</small><br>
                <strong>{{ $categoryData->type ?? 'Bilinmiyor' }}</strong>
            </div>
            
            <div class="mb-2">
                <small class="text-muted">Fiyat:</small><br>
                <strong>{{ number_format($pivotParam->price ?? 0, 2) }} TL</strong>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="mb-2">
                <small class="text-muted">Zorunlu:</small><br>
                <strong>{{ $categoryData->required ? 'Evet' : 'Hayır' }}</strong>
            </div>
            
            <div class="mb-2">
                <small class="text-muted">Seviye:</small><br>
                <strong>{{ $level ?? 0 }}</strong>
            </div>
            
            @if($categoryData->description)
            <div class="mb-2">
                <small class="text-muted">Açıklama:</small><br>
                <small>{{ $categoryData->description }}</small>
            </div>
            @endif
        </div>
    </div>
    
    @if($pivotParam->option1 || $pivotParam->option2)
    <div class="mb-3">
        <small class="text-muted">Özel Seçenekler:</small><br>
        <div class="mt-1">
            @if($pivotParam->option1)
                <span class="badge bg-light text-dark me-1">{{ $pivotParam->option1 }}</span>
            @endif
            @if($pivotParam->option2)
                <span class="badge bg-light text-dark me-1">{{ $pivotParam->option2 }}</span>
            @endif
        </div>
    </div>
    @endif
    
    @if($paramData->options)
    <div class="mb-3">
        <small class="text-muted">Parametre Seçenekleri:</small><br>
        <div class="mt-1">
            @foreach(json_decode($paramData->options, true) ?? [] as $option)
                <span class="badge bg-info text-white me-1">{{ $option }}</span>
            @endforeach
        </div>
    </div>
    @endif
    
    @if($paramData->value)
    <div class="mb-3">
        <small class="text-muted">Varsayılan Değer:</small><br>
        <strong>{{ $paramData->value }}</strong>
    </div>
    @endif
    
    <div class="d-flex gap-2 mt-3">
        <a href="{{ route('admin.product-customization-params.edit', [$product->id, $pivotParam->id]) }}" 
           class="btn btn-sm btn-primary">
            <i class="fas fa-edit"></i> Düzenle
        </a>
        <button type="button" class="btn btn-sm btn-danger delete-param" 
                data-param-id="{{ $pivotParam->id }}">
            <i class="fas fa-trash"></i> Sil
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete parameter
    document.querySelectorAll('.delete-param').forEach(button => {
        button.addEventListener('click', function() {
            const paramId = this.dataset.paramId;
            
            if (confirm('Bu parametreyi silmek istediğinize emin misiniz? Alt parametreler de silinecektir.')) {
                fetch(`/admin/products/{{ $product->id }}/customization-params/${paramId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Parametre detaylarını temizle
                        document.getElementById('paramDetails').innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-mouse-pointer fa-2x text-muted mb-3"></i>
                                <h6 class="text-muted">Parametre Seçin</h6>
                                <p class="text-muted small">Sol taraftan bir parametre seçerek detaylarını görüntüleyin</p>
                            </div>
                        `;
                        
                        // Hiyerarşik ağaçtan da kaldır
                        const item = document.querySelector(`[data-param-id="${paramId}"]`);
                        if (item) {
                            item.remove();
                        }
                        
                        showNotification('Parametre silindi', 'success');
                    } else {
                        showNotification('Silme hatası', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Silme hatası', 'error');
                });
            }
        });
    });
});
</script> 