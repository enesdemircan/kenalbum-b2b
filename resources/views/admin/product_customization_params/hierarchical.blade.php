@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">{{ $product->title }} - Hiyerarşik Parametre Yönetimi</h1>
    <div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">← Ürünlere Dön</a>
        <a href="{{ route('admin.product-customization-params.create', $product->id) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Parametre Ekle
        </a>
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

<div class="row">
    <!-- Sol Taraf: Hiyerarşik Ağaç -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-sitemap"></i> Parametre Hiyerarşisi
                </h5>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-success" id="expandAll">
                        <i class="fas fa-expand"></i> Tümünü Aç
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="collapseAll">
                        <i class="fas fa-compress"></i> Tümünü Kapat
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="hierarchicalTree" class="hierarchical-tree">
                    @php
                        $topLevelParams = $pivotParams->where('customization_params_ust_id', 0);
                    @endphp
                    
                    @foreach($topLevelParams as $topParam)
                        @include('admin.product_customization_params.partials.hierarchical_item', [
                            'param' => $topParam,
                            'allParams' => $pivotParams,
                            'level' => 0,
                            'product' => $product
                        ])
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sağ Taraf: Parametre Detayları -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> Parametre Detayları
                </h5>
            </div>
            <div class="card-body" id="paramDetails">
                <div class="text-center py-4">
                    <i class="fas fa-mouse-pointer fa-2x text-muted mb-3"></i>
                    <h6 class="text-muted">Parametre Seçin</h6>
                    <p class="text-muted small">Sol taraftan bir parametre seçerek detaylarını görüntüleyin</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sürükle-Bırak için Sortable.js -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<style>
.hierarchical-tree {
    min-height: 400px;
}

.hierarchical-item {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    margin: 4px 0;
    transition: all 0.2s ease;
}

.hierarchical-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.hierarchical-item.selected {
    border-color: #007bff;
    background-color: #f8f9ff;
}

.hierarchical-item .item-header {
    padding: 12px 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #f1f3f4;
}

.hierarchical-item .item-content {
    padding: 8px 16px;
    background: #f8f9fa;
    font-size: 0.9em;
    color: #6c757d;
}

.hierarchical-item .item-actions {
    display: flex;
    gap: 4px;
}

.hierarchical-item .item-actions .btn {
    padding: 2px 6px;
    font-size: 0.8em;
}

.hierarchical-item .children-container {
    margin-left: 20px;
    padding-left: 16px;
    border-left: 2px solid #e9ecef;
    min-height: 20px;
    padding-top: 5px;
    padding-bottom: 5px;
}

.hierarchical-item .children-container:empty::after {
    content: "Buraya sürükleyin";
    color: #6c757d;
    font-style: italic;
    font-size: 0.8em;
    display: block;
    padding: 10px;
    text-align: center;
    border: 1px dashed #dee2e6;
    border-radius: 4px;
    margin: 5px 0;
}

.empty-drop-zone {
    min-height: 40px;
    border: 2px dashed #dee2e6;
    border-radius: 6px;
    margin: 5px 0;
    background: #f8f9fa;
    transition: all 0.2s ease;
}

.empty-drop-zone:hover {
    border-color: #007bff;
    background: #e3f2fd;
}

.empty-drop-zone.sortable-ghost {
    border-color: #007bff;
    background: #e3f2fd;
    opacity: 0.7;
}

.drop-zone-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 15px;
    color: #6c757d;
    font-size: 0.9em;
}

.drop-zone-content i {
    font-size: 1.2em;
    margin-bottom: 5px;
    color: #007bff;
}

.drop-zone-content span {
    font-style: italic;
}

.hierarchical-item .children-container .hierarchical-item {
    margin: 2px 0;
}

#hierarchicalTree {
    min-height: 200px;
    padding: 10px;
    border: 2px dashed #e9ecef;
    border-radius: 8px;
}

#hierarchicalTree:empty::after {
    content: "Parametreleri buraya sürükleyin";
    color: #6c757d;
    font-style: italic;
    text-align: center;
    display: block;
    padding: 20px;
}

.sortable-ghost {
    opacity: 0.5;
    background: #e3f2fd !important;
}

.sortable-chosen {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.level-indicator {
    display: inline-block;
    width: 20px;
    height: 20px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    text-align: center;
    line-height: 20px;
    font-size: 0.8em;
    margin-right: 8px;
}

.param-badge {
    font-size: 0.8em;
    padding: 2px 6px;
    border-radius: 12px;
    margin-left: 8px;
}

.param-badge.category {
    background: #e3f2fd;
    color: #1976d2;
}

.param-badge.param {
    background: #f3e5f5;
    color: #7b1fa2;
}

/* Sadece popup için gerekli stiller */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Global değişkenler
    let selectedCustomers = [];
    let currentParamData = null;
    
    // Sortable.js ile sürükle-bırak
    function initializeSortable() {
        // Tüm container'ları bul
        const containers = document.querySelectorAll('.children-container, #hierarchicalTree');
        
        containers.forEach(container => {
            new Sortable(container, {
                group: 'nested',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                fallbackOnBody: true,
                swapThreshold: 0.65,
                forceFallback: false,
                onEnd: function(evt) {
                    updateHierarchy(evt);
                },
                onStart: function(evt) {
                    // Sürükleme başladığında empty drop zone'ları göster
                    document.querySelectorAll('.empty-drop-zone').forEach(zone => {
                        zone.style.opacity = '1';
                    });
                },
                onEnd: function(evt) {
                    updateHierarchy(evt);
                    // Sürükleme bittiğinde empty drop zone'ları gizle (eğer boşsa)
                    document.querySelectorAll('.children-container').forEach(container => {
                        if (container.children.length === 1 && container.querySelector('.empty-drop-zone')) {
                            container.style.display = 'none';
                        }
                    });
                }
            });
        });
    }
    
    // İlk yükleme
    initializeSortable();
    
    // Dinamik içerik eklendiğinde yeniden başlat
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                // Yeni children-container eklendi mi kontrol et
                const newContainers = document.querySelectorAll('.children-container:not([data-sortable-initialized])');
                if (newContainers.length > 0) {
                    newContainers.forEach(container => {
                        new Sortable(container, {
                            group: 'nested',
                            animation: 150,
                            ghostClass: 'sortable-ghost',
                            chosenClass: 'sortable-chosen',
                            fallbackOnBody: true,
                            swapThreshold: 0.65,
                            forceFallback: false,
                            onEnd: function(evt) {
                                updateHierarchy(evt);
                            }
                        });
                        container.setAttribute('data-sortable-initialized', 'true');
                    });
                }
            }
        });
    });
    
    // Observer'ı başlat
    observer.observe(document.getElementById('hierarchicalTree'), {
        childList: true,
        subtree: true
    });
    
    // Parametre seçimi ve toggle butonları
    document.addEventListener('click', function(e) {
        // Toggle children visibility
        if (e.target.closest('.toggle-children')) {
            e.preventDefault();
            e.stopPropagation();
            
            const button = e.target.closest('.toggle-children');
            const targetId = button.dataset.target;
            const container = document.getElementById(targetId);
            const icon = button.querySelector('i');
            
            if (container) {
                const isHidden = container.style.display === 'none' || container.style.display === '';
                if (isHidden) {
                    container.style.display = 'block';
                    icon.className = 'fas fa-chevron-up';
                } else {
                    container.style.display = 'none';
                    icon.className = 'fas fa-chevron-down';
                }
            }
            return;
        }
        
        // Müşteri ekleme butonu
        if (e.target.closest('.add-customer-btn')) {
            e.preventDefault();
            e.stopPropagation();
            
            const button = e.target.closest('.add-customer-btn');
            const modalId = button.dataset.modalId;
            const customizationParamId = button.dataset.customizationParamId;
            const productId = button.dataset.productId;
            
            // Modal'ı aç
            const modal = new bootstrap.Modal(document.getElementById(modalId));
            modal.show();
            
            // Mevcut seçimleri yükle
            loadExistingCustomers(modalId, customizationParamId, productId);
            
            return;
        }
        
        // Delete parameter
        if (e.target.closest('.delete-param')) {
            e.preventDefault();
            e.stopPropagation();
            
            const button = e.target.closest('.delete-param');
            const paramId = button.dataset.paramId;
            
            if (confirm('Bu parametreyi silmek istediğinize emin misiniz? Alt parametreler de silinecektir.')) {
                console.log('Silinecek parametre ID:', paramId);
                console.log('Product ID:', {{ $product->id }});
                
                fetch(`/admin/products/{{ $product->id }}/customization-params/${paramId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Success response:', data);
                    if (data.success) {
                        alert('Parametre başarıyla silindi!');
                        location.reload();
                    } else {
                        alert('Silme hatası: ' + (data.error || 'Bilinmeyen hata'));
                    }
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    alert('Silme hatası: ' + error.message);
                });
            }
            return;
        }
        
        // Parametre seçimi
        if (e.target.closest('.hierarchical-item')) {
            const item = e.target.closest('.hierarchical-item');
            const paramId = item.dataset.paramId;
            
            // Seçili sınıfını kaldır
            document.querySelectorAll('.hierarchical-item').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Yeni seçimi işaretle
            item.classList.add('selected');
            
            // Parametre detaylarını yükle
            loadParamDetails(paramId);
        }
    });
    
    // Tümünü aç/kapat
    document.getElementById('expandAll').addEventListener('click', function() {
        document.querySelectorAll('.children-container').forEach(container => {
            container.style.display = 'block';
        });
    });
    
    document.getElementById('collapseAll').addEventListener('click', function() {
        document.querySelectorAll('.children-container').forEach(container => {
            container.style.display = 'none';
        });
    });
    
    // Modal accessibility fix
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('customerSelectionModal');
        
        // Modal açıldığında aria-hidden'ı kaldır
        modalElement.addEventListener('shown.bs.modal', function() {
            modalElement.removeAttribute('aria-hidden');
        });
        
        // Modal kapandığında aria-hidden'ı geri ekle
        modalElement.addEventListener('hidden.bs.modal', function() {
            modalElement.setAttribute('aria-hidden', 'true');
        });
    });
    
    // Hiyerarşi güncelleme
    function updateHierarchy(evt) {
        const movedItem = evt.item;
        const newParent = evt.to;
        const oldParent = evt.from;
        
        console.log('Update hierarchy:', {
            movedItem: movedItem.dataset.paramId,
            newParent: newParent,
            oldParent: oldParent,
            newIndex: evt.newIndex,
            oldIndex: evt.oldIndex
        });
        
        // Yeni parent ID'sini bul
        let newParentId = 0;
        if (newParent.classList.contains('children-container')) {
            const parentItem = newParent.closest('.hierarchical-item');
            if (parentItem) {
                newParentId = parentItem.dataset.paramId;
                console.log('New parent ID:', newParentId);
            }
        } else if (newParent.id === 'hierarchicalTree') {
            // Ana container'a taşındı
            newParentId = 0;
            console.log('Moved to main container');
        } else if (newParent.classList.contains('empty-drop-zone')) {
            // Empty drop zone'a taşındı
            newParentId = newParent.dataset.parentId;
            console.log('Moved to empty drop zone, parent ID:', newParentId);
        } else {
            // Diğer durumlar için parent'ı bul
            const parentItem = newParent.closest('.hierarchical-item');
            if (parentItem) {
                newParentId = parentItem.dataset.paramId;
                console.log('Found parent ID from closest item:', newParentId);
            }
        }
        
        // Yeni sıra pozisyonunu hesapla
        const newIndex = evt.newIndex;
        
        // Tüm parametrelerin yeni order değerlerini hesapla
        const allItems = Array.from(newParent.children);
        const orderUpdates = [];
        
        allItems.forEach((item, index) => {
            if (item.classList.contains('hierarchical-item')) {
                const paramId = item.dataset.paramId;
                orderUpdates.push({
                    param_id: paramId,
                    new_order: index + 1
                });
            }
        });
        
        console.log('Order updates:', orderUpdates);
        
        // AJAX ile güncelle
        fetch(`/admin/products/{{ $product->id }}/customization-params/${movedItem.dataset.paramId}/update-hierarchy`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                new_parent_id: newParentId,
                new_order: newIndex,
                all_orders: orderUpdates
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Hiyerarşi güncellendi', 'success');
            } else {
                showNotification('Hata oluştu', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Hata oluştu', 'error');
        });
    }
    
    // Parametre detaylarını yükle
    function loadParamDetails(paramId) {
        fetch(`/admin/products/{{ $product->id }}/customization-params/${paramId}/details`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('paramDetails').innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading param details:', error);
        });
    }

    // Mevcut müşteri seçimlerini yükle
    function loadExistingCustomers(modalId, customizationParamId, productId) {
        fetch(`/admin/customization-params-customers/existing?customization_params_id=${customizationParamId}&product_id=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const selectElement = document.querySelector(`#${modalId} select[name="customer_ids[]"]`);
                    if (selectElement) {
                        // Tüm seçimleri temizle
                        Array.from(selectElement.options).forEach(option => {
                            option.selected = false;
                        });
                        
                        // Mevcut seçimleri işaretle
                        data.customer_ids.forEach(customerId => {
                            const option = selectElement.querySelector(`option[value="${customerId}"]`);
                            if (option) {
                                option.selected = true;
                            }
                        });
                    }
                } else {
                    console.error('Error loading existing customers:', data.message);
                }
            })
            .catch(error => {
                console.error('Error loading existing customers:', error);
            });
    }
    
    // Bildirim göster
    function showNotification(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show notification`;
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
});
</script>
@endsection