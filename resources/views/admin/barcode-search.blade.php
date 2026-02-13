@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Toplu Sipariş Durum Güncelleme</h1>
        <p class="page-subtitle">Barcode ile hızlı sipariş durum güncelleme</p>
    </div>
    <a href="{{ route('admin.orders.index') }}" class="btn-material btn-material-secondary">
        <span class="material-icons">arrow_back</span>
        Siparişlere Dön
    </a>
</div>

@if($message)
    <div class="material-alert material-alert-{{ $messageType }}" id="pageAlert" style="position: relative">
        <span class="material-icons">{{ $messageType == 'success' ? 'check_circle' : 'warning' }}</span>
        <span>{{ $message }}</span>
        <button type="button" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer" onclick="this.parentElement.remove()">
            <span class="material-icons">close</span>
        </button>
    </div>
@endif

<div class="row g-4">
    <!-- Barcode Tarama Kartı -->
    <div class="col-md-6">
        <div class="material-card-elevated">
            <div class="material-card-header">
                <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">qr_code_scanner</span>Barcode Tarama</h5>
            </div>
            <div class="material-card-body">
                <div class="mb-3">
                    <label for="barcode" class="form-label" style="font-weight: 500; color: var(--md-text-secondary)">Barcode</label>
                    <input type="text" class="form-control form-control-material" id="barcode" name="barcode" 
                           value="{{ request('barcode') }}" placeholder="13 haneli barcode'u buraya yazın veya tarayın..." 
                           autofocus maxlength="13" pattern="[0-9]{13}" style="font-size: 18px; padding: 16px 0">
                    <div class="form-text" style="margin-top: 8px; color: var(--md-text-secondary)">
                        <span class="material-icons" style="font-size: 16px; vertical-align: middle">info</span>
                        13 haneli barcode yazıldığında otomatik olarak arama yapılır
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sipariş Listesi Kartı -->
    <div class="col-md-6">
        <div class="material-card-elevated">
            <div class="material-card-header d-flex justify-content-between align-items-center">
                <h5 style="margin:0"><span class="material-icons" style="vertical-align:middle;margin-right:8px">list_alt</span>Sipariş Listesi</h5>
                @if(count($cartList) > 0)
                    <button type="button" class="btn-material btn-material-danger" onclick="clearCartList()" title="Tüm seçimleri temizle">
                        <span class="material-icons">delete_sweep</span>
                        Seçimleri Temizle
                    </button>
                @endif
            </div>
            <div class="material-card-body">
                @if(count($cartList) > 0)
                    <div class="material-table-wrapper">
                        <table class="material-table">
                            <thead>
                                <tr>
                                    <th>Sipariş ID</th>
                                    <th>Firma</th>
                                    <th>Ürün</th>
                                    <th style="width: 60px">İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartList as $item)
                                    <tr>
                                        <td>
                                            <strong style="color: var(--md-primary)">{{ $item['cart_id'] }}</strong><br>
                                            <code style="background: #f5f5f5; padding: 2px 6px; border-radius: 4px; font-size: 11px">{{ $item['barcode'] }}</code>
                                        </td>
                                        <td>
                                            <small>{{ $item['company_name'] }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $item['product_title'] }}</small><br>
                                            <span class="material-badge material-badge-secondary">{{ $item['quantity'] }} adet</span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn-material-icon btn-material-icon-danger" onclick="removeFromCartList({{ $item['id'] }})" title="Kaldır">
                                                <span class="material-icons">close</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: 40px">
                        <span class="material-icons" style="font-size: 48px; color: #bdbdbd">shopping_cart</span>
                        <p class="text-muted mt-2 mb-0">Henüz cart eklenmemiş.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Toplu Güncelleme Kartı -->
@if(count($cartList) > 0)
    <div class="material-card-elevated mt-4">
        <div class="material-card-header">
            <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">update</span>Toplu Durum Güncelleme</h5>
        </div>
        <div class="material-card-body">
            <form id="bulkUpdateForm">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="order_status_id" class="form-label" style="font-weight: 500; color: var(--md-text-secondary)">Yeni Durum Seçin</label>
                        <select class="form-select form-control-material" id="order_status_id" name="order_status_id" required>
                            <option value="">Durum seçin...</option>
                            @foreach($orderStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->title }}</option>
                            @endforeach
                        </select>
                        <div class="form-text" style="margin-top: 8px; color: var(--md-text-secondary)">
                            <span class="material-icons" style="font-size: 16px; vertical-align: middle">info</span>
                            Seçilen durum tüm seçili cart'lara uygulanacak.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 500; color: var(--md-text-secondary)">Seçili Cart'lar</label>
                        <div class="material-card-outlined" style="padding: 16px">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px">
                                <span class="material-icons" style="color: var(--md-primary)">inventory</span>
                                <strong style="color: var(--md-primary)">{{ count($cartList) }}</strong> 
                                <span style="color: var(--md-text-secondary)">cart seçili</span>
                            </div>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($cartList as $item)
                                    <span class="material-badge material-badge-primary">{{ $item['cart_id'] }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn-material btn-material-warning" style="padding: 12px 32px; font-size: 16px">
                        <span class="material-icons">update</span>
                        Siparişlerin Durumunu Güncelle
                    </button>
                    <button type="button" class="btn-material btn-material-secondary" onclick="clearCartList()" style="padding: 12px 24px">
                        <span class="material-icons">delete_sweep</span>
                        Seçimleri Temizle
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const barcodeInput = document.getElementById('barcode');
    
    // Barcode input varsa işlemleri yap
    if (barcodeInput) {
        // Barcode input'a focus ol
        barcodeInput.focus();
        
        // Barcode input değişikliklerini dinle
        barcodeInput.addEventListener('input', function(e) {
            const value = this.value;
            
            // Sadece rakam girişine izin ver
            this.value = value.replace(/[^0-9]/g, '');
            
            // 13 haneli barcode yazıldığında otomatik arama yap
            if (this.value.length === 13) {
                // Kısa bir gecikme ile arama yap (kullanıcı yazmayı bitirsin)
                setTimeout(() => {
                    performBarcodeSearch(this.value);
                }, 300);
            }
        });
        
        // Enter tuşuna basıldığında da arama yap
        barcodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (this.value.length === 13) {
                    performBarcodeSearch(this.value);
                }
            }
        });
    }
});

function performBarcodeSearch(barcode) {
    // Loading göster
    showAlert('info', 'Barcode aranıyor...');
    
    // AJAX ile arama yap
    fetch(`{{ route('admin.barcode.search') }}?barcode=${barcode}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(html => {
        // Sayfayı yenile
        location.reload();
    })
    .catch(error => {
        console.error('Barcode search error:', error);
        showAlert('danger', 'Arama sırasında bir hata oluştu.');
    });
}

function removeFromCartList(cartId) {
    if (confirm('Bu sipariş\'ı listeden kaldırmak istediğinizden emin misiniz?')) {
        fetch('{{ route("admin.barcode.remove-from-list") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ cart_id: cartId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'Bir hata oluştu.');
        });
    }
}

function clearCartList() {
    if (confirm('Tüm sipariş listesini temizlemek istediğinizden emin misiniz?')) {
        fetch('{{ route("admin.barcode.clear-list") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'Bir hata oluştu.');
        });
    }
}

// Form submit event listener'ı sadece form varsa ekle
const bulkUpdateForm = document.getElementById('bulkUpdateForm');
if (bulkUpdateForm) {
    bulkUpdateForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const orderStatusId = document.getElementById('order_status_id').value;
        if (!orderStatusId) {
            showAlert('danger', 'Lütfen bir durum seçin.');
            return;
        }
        
        const cartIds = @json(array_column($cartList, 'id'));
        
        fetch('{{ route("admin.barcode.update-statuses") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                cart_ids: cartIds,
                order_status_id: orderStatusId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'Bir hata oluştu.');
        });
    });
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `material-alert material-alert-${type}`;
    alertDiv.innerHTML = `
        <span class="material-icons">${type == 'success' ? 'check_circle' : (type == 'info' ? 'info' : 'warning')}</span>
        <span>${message}</span>
        <button type="button" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: inherit" onclick="this.parentElement.remove()">
            <span class="material-icons">close</span>
        </button>
    `;
    
    // CSS stilleri ekle
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 350px;
        max-width: 500px;
        box-shadow: var(--md-shadow-4);
    `;
    
    // En güvenli yöntem: body'ye ekle
    document.body.appendChild(alertDiv);
    
    // 5 saniye sonra otomatik kaldır
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endpush
