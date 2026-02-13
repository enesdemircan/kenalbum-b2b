@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Toplu Sipariş Durum Güncelleme</h1>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Siparişlere Dön
    </a>
</div>

@if($message)
    <div class="alert alert-{{ $messageType }} alert-dismissible fade show" role="alert">
        <i class="bi bi-{{ $messageType == 'success' ? 'check-circle' : 'exclamation-triangle' }}"></i>
        {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Barcode Tarama</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="barcode" class="form-label">Barcode</label>
                    <input type="text" class="form-control form-control-lg" id="barcode" name="barcode" 
                           value="{{ request('barcode') }}" placeholder="13 haneli barcode'u buraya yazın veya tarayın..." 
                           autofocus maxlength="13" pattern="[0-9]{13}">
                    <div class="form-text">13 haneli barcode yazıldığında otomatik olarak arama yapılır</div>
                </div>
            </div>
        </div>


    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Sipariş Listesi</h5>
                @if(count($cartList) > 0)
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearCartList()">
                        <i class="bi bi-trash"></i> Listeyi Temizle
                    </button>
                @endif
            </div>
            <div class="card-body">
                @if(count($cartList) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Sipariş ID</th>
                                    <th>Firma</th>
                                    <th>Ürün</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartList as $item)
                                    <tr>
                                        <td>
                                            <small><strong>{{ $item['cart_id'] }}</strong></small><br>
                                            <code class="small">{{ $item['barcode'] }}</code>
                                        </td>
                                        <td>
                                            <small>{{ $item['company_name'] }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $item['product_title'] }}</small><br>
                                            <span class="badge bg-secondary small">{{ $item['quantity'] }} adet</span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFromCartList({{ $item['id'] }})">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Henüz cart eklenmemiş.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@if(count($cartList) > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Toplu Durum Güncelleme</h5>
                </div>
                <div class="card-body">
                    <form id="bulkUpdateForm">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="order_status_id" class="form-label">Yeni Durum Seçin</label>
                                <select class="form-select" id="order_status_id" name="order_status_id" required>
                                    <option value="">Durum seçin...</option>
                                    @foreach($orderStatuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->title }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Seçilen durum tüm seçili cart'lara uygulanacak.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Seçili Cart'lar</label>
                                <div class="border rounded p-3 bg-light">
                                    <strong>{{ count($cartList) }}</strong> cart seçili
                                    <div class="mt-2">
                                        @foreach($cartList as $item)
                                            <span class="badge bg-primary me-1">{{ $item['cart_id'] }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="bi bi-arrow-up-circle"></i> Siparişlerin Durumunu Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="bi bi-${type == 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // CSS stilleri ekle
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 500px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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