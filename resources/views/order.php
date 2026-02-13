@extends('admin.layout')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Sipariş Detayı #{{ $order->order_number }}</h1>
            <p class="page-subtitle">Sipariş tarihi: {{ $order->created_at->format('d.m.Y H:i') }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn-material btn-material-secondary">
            <span class="material-icons">arrow_back</span>
            Geri Dön
        </a>
    </div>

    @if(session('success'))
        <div class="material-alert material-alert-success">
            <span class="material-icons">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div class="material-alert material-alert-danger">
            <span class="material-icons">error</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <!-- Sipariş Bilgileri -->
        <div class="col-md-6">
            <div class="material-card-elevated">
                <div class="material-card-header">
                    <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">receipt</span>Sipariş Bilgileri</h5>
                </div>
                <div class="material-card-body">
                    <div class="material-info-grid">
                        <div class="material-info-item">
                            <span class="material-info-label">Sipariş No</span>
                            <span class="material-info-value">{{ $order->order_number }}</span>
                        </div>
                        <div class="material-info-item">
                            <span class="material-info-label">Tarih</span>
                            <span class="material-info-value">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <div class="material-info-item">
                            <span class="material-info-label">Toplam Tutar</span>
                            <span class="material-info-value" style="color: var(--md-success); font-weight: 500">{{ number_format($order->total_price, 2) }} ₺</span>
                        </div>
                        <div class="material-info-item">
                            <span class="material-info-label">Ödeme Yöntemi</span>
                            <span class="material-info-value">{{ ucfirst($order->payment_method) }}</span>
                        </div>
                        <div class="material-info-item" style="grid-column: 1 / -1">
                            <span class="material-info-label">Notlar</span>
                            <span class="material-info-value">{{ $order->notes ?: 'Not yok' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Müşteri Bilgileri -->
        <div class="col-md-6">
            <div class="material-card-elevated">
                <div class="material-card-header">
                    <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">person</span>Müşteri Bilgileri</h5>
                </div>
                <div class="material-card-body">
                    <div class="material-info-grid">
                        <div class="material-info-item">
                            <span class="material-info-label">Ad Soyad</span>
                            <span class="material-info-value">{{ $order->customer_name }} {{ $order->customer_surname }}</span>
                        </div>
                        <div class="material-info-item">
                            <span class="material-info-label">Telefon</span>
                            <span class="material-info-value">{{ $order->customer_phone }}</span>
                        </div>
                        <div class="material-info-item">
                            <span class="material-info-label">E-posta</span>
                            <span class="material-info-value">{{ $order->user->email }}</span>
                        </div>
                        <div class="material-info-item">
                            <span class="material-info-label">İl/İlçe</span>
                            <span class="material-info-value">{{ $order->city ?? 'Belirtilmemiş' }} / {{ $order->district ?? 'Belirtilmemiş' }}</span>
                        </div>
                        @if($customer && $customer->unvan)
                        <div class="material-info-item">
                            <span class="material-info-label">Firma</span>
                            <span class="material-info-value">{{ $customer->unvan }}</span>
                        </div>
                        @endif
                        <div class="material-info-item" style="grid-column: 1 / -1">
                            <span class="material-info-label">Teslimat Adresi</span>
                            <span class="material-info-value">{{ $order->shipping_address }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alt Sipariş Ürünler -->
    <div class="material-card-elevated">
        <div class="material-card-header">
            <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">inventory_2</span>Alt Sipariş</h5>
        </div>
        <div class="material-card-body">
            <div class="row g-4">
                @foreach($order->cartItems as $item)
                    <div class="col-md-6">
                        <div class="material-card-elevated h-100">
                            <div class="material-card-header" style="background: #f8f9fa">
                                <div class="d-flex align-items-center">
                                    <img src="{{ explode(',', $item->product->images)[0] }}" alt="{{ $item->product->title }}" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; box-shadow: var(--md-shadow-2)" class="me-3">
                                    <div class="flex-grow-1">
                                        <h6 style="margin: 0; font-weight: 500; color: var(--md-text-primary)">{{ $item->product->title }}</h6>
                                        <div style="margin-top: 4px">
                                            <span class="material-badge material-badge-info">Adet: {{ $item->quantity }}</span>
                                            <span class="ms-2 fw-bold" style="color: var(--md-primary)">
                                                <x-price-display :item="$item" :showQuantity="true" :showDiscountBadge="true" />
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="material-card-body">
                                <!-- Ürün Özelleştirme Detayları -->
                                @if($item->notes)
                                    @php
                                        $notes = json_decode($item->notes, true);
                                        $customizations = $notes['customizations'] ?? [];
                                        $totalCustomizationPrice = $notes['total_customization_price'] ?? 0;
                                    @endphp
                                    @if($customizations)
                                        <div class="mb-3">
                                            <x-customization-list :customizations="$customizations" :totalCustomizationPrice="$totalCustomizationPrice" />
                                        </div>
                                    @endif  
                                  
                                    <!-- Sipariş Notu -->
                                    @if(isset($notes['order_note']) && !empty($notes['order_note']))
                                        <div class="material-alert material-alert-info mb-3">
                                            <span class="material-icons">notes</span>
                                            <div>
                                                <strong>Sipariş Notu</strong>
                                                <p class="mb-0">{{ $notes['order_note'] }}</p>
                                            </div>
                                        </div>
                                    @endif
                             
                                @endif
                                
                                <!-- Acil üretim bilgisi -->
                                @if($item->urgent_status == 1)
                                    <div class="material-badge material-badge-warning mb-3">
                                        <span class="material-icons" style="font-size:16px">priority_high</span>
                                        Acil Üretim +{{ number_format($item->product->urgent_price, 2) }} ₺
                                    </div>
                                @endif
                                
                                <!-- Durum Güncelleme -->
                                <div class="mb-3">
                                    <h6 class="fw-bold text-secondary mb-2" style="display: flex; align-items: center; gap: 8px">
                                        <span class="material-icons" style="font-size:20px">sync</span>
                                        Alt Sipariş Durumunu Güncelle
                                    </h6>
                                    <form action="{{ route('admin.orders.update-cart-status', $order->id) }}" method="POST" id="statusForm{{ $item->id }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="cart_id" value="{{ $item->id }}">
                                        <input type="hidden" name="cargo_company" id="cargoCompany{{ $item->id }}" value="">
                                        <input type="hidden" name="payment_type" id="paymentType{{ $item->id }}" value="">
                                        <div class="row g-2">
                                            <div class="col-8">
                                                <select name="order_status_id" class="form-select form-control-material" onchange="checkCargoStatus(this, {{ $item->id }})">
                                                    <option value="">Durum Seçin</option>
                                                    @foreach($orderStatuses as $status)
                                                        <option value="{{ $status->id }}">{{ $status->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <button type="submit" class="btn-material btn-material-warning w-100">
                                                    <span class="material-icons">sync</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                @php
                                    $productStatuses = \App\Models\OrderStatusHistory::where('cart_id', $item->id)
                                        ->with('orderStatus', 'user')
                                        ->orderBy('created_at', 'desc')
                                        ->get();
                                @endphp

                                <!-- Durum Geçmişi -->
                                <div class="timeline-container mb-3">
                                    <h6 class="fw-bold text-secondary mb-2" style="display: flex; align-items: center; gap: 8px">
                                        <span class="material-icons" style="font-size:20px">history</span>
                                        Durum Geçmişi
                                    </h6>
                                    <div class="timeline">
                                        @forelse($productStatuses as $statusHistory)
                                            <div class="timeline-item">
                                                <div class="timeline-marker active">
                                                    <span class="material-icons" style="font-size: 8px; color: white">circle</span>
                                                </div>
                                                <div class="timeline-content d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">{{ $statusHistory->orderStatus->title }}</h6>
                                                        <small class="text-muted">
                                                            {{ $statusHistory->created_at->format('d.m.Y H:i') }}
                                                            @if($statusHistory->user)
                                                                • {{ $statusHistory->user->name }}
                                                            @else
                                                                • <span class="text-muted">Kullanıcı Bulunamadı</span>
                                                            @endif
                                                        </small>
                                                    </div>
                                                    @if(Auth::user()->roles()->where('roles.id', 1)->exists())
                                                        <form action="{{ route('admin.orders.delete-status-history', $statusHistory->id) }}" method="POST" class="ms-2" onsubmit="return confirm('Bu durum geçmişi kaydını silmek istediğinizden emin misiniz?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn-material-icon btn-material-icon-danger" title="Durumu Sil">
                                                                <span class="material-icons">delete</span>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-muted">
                                                <small>Henüz durum geçmişi bulunmuyor</small>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="d-flex flex-wrap gap-2">
                                    @if(!empty($item->s3_zip))
                                        <a href="{{ route('admin.orders.download-cart-files', ['order' => $order->id, 'cart' => $item->id]) }}" class="btn-material btn-material-primary">
                                            <span class="material-icons">download</span>
                                            Dosyaları İndir
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('admin.orders.cargo-pdf', ['order' => $order->id, 'cart' => $item->id]) }}" class="btn-material btn-material-info" target="_blank">
                                        <span class="material-icons">local_shipping</span>
                                        Kargo PDF
                                    </a>
                                </div>
                                
                                <!-- Kargo Bilgileri -->
                                @if($item->cargo_barcode)
                                    <div class="material-card-outlined mt-3">
                                        <div class="material-card-header" style="background: #f8f9fa">
                                            <h6 style="margin: 0; display: flex; align-items: center; gap: 8px">
                                                <span class="material-icons" style="font-size:20px">local_shipping</span>
                                                Kargo Bilgileri
                                            </h6>
                                        </div>
                                        <div class="material-card-body">
                                            <div class="material-info-grid">
                                                <div class="material-info-item">
                                                    <span class="material-info-label">Kargo Barkodu</span>
                                                    <span class="material-info-value">{{ $item->cargo_barcode }}</span>
                                                </div>
                                                <div class="material-info-item">
                                                    <span class="material-info-label">Takip</span>
                                                    <div>
                                                        @if($item->tracking_url)
                                                            <a href="{{ $item->tracking_url }}" target="_blank" class="btn-material btn-material-info btn-material-outlined">
                                                                <span class="material-icons">open_in_new</span>
                                                                Takip Et
                                                            </a>
                                                        @elseif(str_contains($item->cargo_barcode, 'Takip:'))
                                                            @php
                                                                $cartBarcode = $item->barcode;
                                                            @endphp
                                                            <a href="https://sube.kolaygelsin.com/takip?ccode=29532262&musref={{ $cartBarcode }}" target="_blank" class="btn-material btn-material-info btn-material-outlined">
                                                                <span class="material-icons">open_in_new</span>
                                                                Takip Et
                                                            </a>
                                                        @endif
                                                        
                                                        @if($item->barcode_zpl)
                                                            <a href="{{ route('admin.orders.zpl-pdf', ['order' => $order->id, 'cart' => $item->id]) }}" class="btn-material btn-material-success btn-material-outlined mt-2" target="_blank">
                                                                <span class="material-icons">print</span>
                                                                ZPL PDF
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Kargo Firması Seçim Modal -->
    <div class="modal fade" id="cargoModal" tabindex="-1" aria-labelledby="cargoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-material">
            <div class="modal-content material-modal">
                <div class="modal-header material-modal-header">
                    <h5 class="modal-title" id="cargoModalLabel">
                        <span class="material-icons" style="vertical-align:middle;margin-right:8px">local_shipping</span>
                        Kargo Firması Seçin
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body material-modal-body">
                    <p class="text-muted mb-3">Sipariş durumu "Kargoya Verildi" olarak güncellenecek. Lütfen kargo firmasını seçin:</p>
                    
                    <div class="mb-3">
                        <h6 class="fw-bold text-secondary mb-2">Kargo Firması</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="cargo_company_modal" id="everest" value="everest">
                            <label class="form-check-label" for="everest">
                                <strong>Everest Kargo</strong>
                            </label>
                        </div>
                   
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="cargo_company_modal" id="yurtici" value="yurtici">
                            <label class="form-check-label" for="yurtici">
                                <strong>Yurtiçi Kargo</strong>
                            </label>
                        </div>
                   
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="cargo_company_modal" id="kolay_gelsin" value="kolay_gelsin">
                            <label class="form-check-label" for="kolay_gelsin">
                                <strong>Kolay Gelsin</strong>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                       <h6 class="fw-bold text-secondary mb-2">Ödeme Tipi</h6>
                       <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment_type" value="1">
                        <label class="form-check-label">
                            <strong>Alıcı Ödemeli</strong>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_type" value="2">
                        <label class="form-check-label">
                            <strong>Gönderici Ödemeli</strong>
                        </label>
                    </div>
                    </div>
                </div>
                <div class="modal-footer material-modal-footer">
                    <button type="button" class="btn-material btn-material-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn-material btn-material-primary" onclick="confirmCargoSelection()">
                        <span class="material-icons">check</span>
                        Onayla
                    </button>
                </div>
            </div>
        </div>
    </div>

<style>
.timeline-container {
    position: relative;
    padding: 16px 0;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--md-divider);
}

.timeline-item {
    position: relative;
    margin-bottom: 16px;
}

.timeline-marker {
    position: absolute;
    left: -18px;
    top: 3px;
    width: 12px;
    height: 12px;
    background: white;
    border: 2px solid var(--md-divider);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
}

.timeline-marker.active {
    border-color: var(--md-primary);
    background: var(--md-primary);
}

.timeline-content {
    padding-left: 16px;
}

.timeline-content h6 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: var(--md-text-primary);
}

.timeline-content small {
    font-size: 12px;
    color: var(--md-text-secondary);
}
</style>

<script>
let currentCartId = null;
let currentStatusId = null;

function checkCargoStatus(selectElement, cartId) {
    const statusId = selectElement.value;
    const statusText = selectElement.options[selectElement.selectedIndex].text;
    
    // Eğer "Kargoya Verildi" durumu seçildiyse popup göster
    if (statusText.includes('Kargoya Verildi') || statusText.includes('Kargo')) {
        currentCartId = cartId;
        currentStatusId = statusId;
        
        // Modal'ı göster
        const cargoModal = new bootstrap.Modal(document.getElementById('cargoModal'));
        cargoModal.show();
        
        // Form submit'i engelle
        return false;
    }
    
    // Diğer durumlar için normal submit
    return true;
}

function confirmCargoSelection() {
    const selectedCargo = document.querySelector('input[name="cargo_company_modal"]:checked');
    const selectedPaymentType = document.querySelector('input[name="payment_type"]:checked');
    
    if (!selectedCargo) {
        alert('Lütfen bir kargo firması seçin!');
        return;
    }
    if (!selectedPaymentType) {
        alert('Lütfen bir ödeme tipi seçin!');
        return;
    }
    
    const cargoCompany = selectedCargo.value;
    const paymentType = selectedPaymentType.value;
    
    // Hidden input'a kargo firmasını set et
    document.getElementById('cargoCompany' + currentCartId).value = cargoCompany;
    document.getElementById('paymentType' + currentCartId).value = paymentType;
    
    // Modal'ı kapat
    const cargoModal = bootstrap.Modal.getInstance(document.getElementById('cargoModal'));
    cargoModal.hide();
    
    // Form'u submit et
    document.getElementById('statusForm' + currentCartId).submit();
}

// Modal kapandığında form'u reset et
document.getElementById('cargoModal').addEventListener('hidden.bs.modal', function () {
    // Seçili durumu reset et
    if (currentCartId) {
        const statusSelect = document.querySelector(`#statusForm${currentCartId} select[name="order_status_id"]`);
        if (statusSelect) statusSelect.value = '';
    }
    
    // Radio button'ları reset et
    document.querySelectorAll('input[name="cargo_company_modal"]').forEach(radio => {
        radio.checked = false;
    });
    
    document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
        radio.checked = false;
    });
    
    currentCartId = null;
    currentStatusId = null;
});
</script>
@endsection
