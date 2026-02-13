@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">{{ $product->title }} - Özelleştirme Parametreleri</h1>
    <div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">← Ürünlere Dön</a>
        <a href="{{ route('admin.product-customization-params.hierarchical', $product->id) }}" class="btn btn-info">
            <i class="fas fa-sitemap"></i> Hiyerarşik Görünüm
        </a>
        <a href="{{ route('admin.product-customization-params.create', $product->id) }}" class="btn btn-primary">Yeni Parametre Ekle</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif



<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Parametre Hiyerarşisi</h5>
    </div>
    <div class="card-body p-0">
        @if($pivotParams->count() > 0)
            <div class="row g-0">
                <!-- Sol Taraf: Tree View -->
                <div class="col-md-4 border-end">
                    <div class="p-3">
                        <h6 class="mb-3">
                            <i class="fas fa-sitemap"></i> Parametre Ağacı
                        </h6>
                        <div class="tree-view" id="paramTree">
                            @php
                                $topLevelParams = $pivotParams->where('customization_params_ust_id', 0);
                            @endphp
                            
                            @foreach($topLevelParams as $topParam)
                                @include('admin.product_customization_params.partials.tree_item', [
                                    'param' => $topParam,
                                    'allParams' => $pivotParams,
                                    'level' => 0,
                                    'product' => $product
                                ])
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Sağ Taraf: Seçili Parametre Detayları -->
                <div class="col-md-8">
                    <div class="p-3">
                        <div id="paramDetails">
                            <div class="text-center py-5">
                                <i class="fas fa-mouse-pointer fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Parametre Seçin</h5>
                                <p class="text-muted">Sol taraftan bir parametre seçerek detaylarını görüntüleyin</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <p class="text-muted">Bu ürün için henüz özelleştirme parametresi eklenmemiş.</p>
                <a href="{{ route('admin.product-customization-params.create', $product->id) }}" class="btn btn-primary">İlk Parametreyi Ekle</a>
            </div>
        @endif
    </div>
</div>

<style>
.tree-view {
    max-height: 600px;
    overflow-y: auto;
}

.tree-item {
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 4px;
    margin: 2px 0;
    transition: all 0.2s;
}

.tree-item:hover {
    background-color: #f8f9fa;
}

.tree-item.selected {
    background-color: #e3f2fd;
    border-left: 3px solid #2196f3;
}

.tree-item .level-indicator {
    display: inline-block;
    width: 20px;
    text-align: center;
    margin-right: 8px;
}

.tree-item .expand-icon {
    margin-right: 8px;
    transition: transform 0.2s;
}

.tree-item.expanded .expand-icon {
    transform: rotate(90deg);
}

.tree-children {
    margin-left: 20px;
    border-left: 1px solid #dee2e6;
    padding-left: 10px;
}

.param-details {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
}

.param-badge {
    font-size: 0.8em;
    margin-left: 5px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tree item tıklama olayları
    document.querySelectorAll('.tree-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Seçili item'ı güncelle
            document.querySelectorAll('.tree-item').forEach(i => i.classList.remove('selected'));
            this.classList.add('selected');
            
            // Parametre detaylarını yükle
            const paramId = this.dataset.paramId;
            loadParamDetails(paramId);
        });
    });
    
    // Expand/collapse olayları
    document.querySelectorAll('.expand-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const parent = this.closest('.tree-item');
            const children = parent.querySelector('.tree-children');
            
            if (children) {
                parent.classList.toggle('expanded');
                children.style.display = children.style.display === 'none' ? 'block' : 'none';
            }
        });
    });
    
    // Parametre detaylarını yükle
    function loadParamDetails(paramId) {
        const detailsContainer = document.getElementById('paramDetails');
        
        // Loading göster
        detailsContainer.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
            </div>
        `;
        
        // AJAX ile detayları yükle
        fetch(`/admin/products/{{ $product->id }}/customization-params/${paramId}/details`)
            .then(response => response.json())
            .then(data => {
                let imageHtml = '';
                if (data.image_url) {
                    imageHtml = `
                        <div class="mt-3">
                            <strong>Resim:</strong><br>
                            <img src="${data.image_url}" alt="Parametre Resmi" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                        </div>
                    `;
                }
                
                detailsContainer.innerHTML = `
                    <div class="param-details">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">${data.param.key}</h5>
                                <span class="badge bg-info">${data.category}</span>
                            </div>
                            <div>
                                <a href="/admin/products/{{ $product->id }}/customization-params/${paramId}/edit" 
                                   class="btn btn-sm btn-warning">Düzenle</a>
                                <button onclick="deleteParam(${paramId})" class="btn btn-sm btn-danger">Sil</button>
                                <a href="/admin/products/{{ $product->id }}/customization-params/create?parent=${paramId}" 
                                   class="btn btn-sm btn-success">Alt Parametre Ekle</a>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Değer:</strong> ${data.param.value || 'Boş'}</p>
                                <p><strong>Fiyat:</strong> ${data.price ? data.price + ' TL' : '0 TL'}</p>
                                <p><strong>Seviye:</strong> ${data.level}</p>
                                ${data.parent ? `<p><strong>Parent:</strong> ${data.parent}</p>` : ''}
                                <p><strong>Alt Parametre Sayısı:</strong> ${data.children_count}</p>
                            </div>
                            <div class="col-md-6">
                                ${data.options ? `<p><strong>Seçenekler:</strong><br>${data.options}</p>` : ''}
                                ${imageHtml}
                            </div>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                console.error('Parametre detayları yüklenirken hata:', error);
                detailsContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <h6>Hata!</h6>
                        <p>Parametre detayları yüklenirken hata oluştu.</p>
                        <small>${error.message}</small>
                    </div>
                `;
            });
    }
});

// Parametre silme fonksiyonu
function deleteParam(paramId) {
    if (confirm('Bu parametreyi silmek istediğinizden emin misiniz?')) {
        fetch(`/admin/products/{{ $product->id }}/customization-params/${paramId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                alert('Parametre silinirken hata oluştu!');
            }
        })
        .catch(error => {
            console.error('Silme hatası:', error);
            alert('Parametre silinirken hata oluştu!');
        });
    }
}
</script>

@endsection 