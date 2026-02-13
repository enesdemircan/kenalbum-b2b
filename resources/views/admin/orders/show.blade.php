@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-shopping-cart"></i> Sipariş Detayı #{{ $order->order_number }}
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Geri Dön
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="row">
                            <!-- Sipariş Bilgileri -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <i class="fas fa-info-circle"></i> Sipariş Bilgileri
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Sipariş No:</strong></td>
                                                <td><span class="text-primary">{{ $order->order_number }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tarih:</strong></td>
                                                <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Toplam Tutar:</strong></td>
                                                <td><span class="fw-bold text-success">{{ number_format($order->total_price, 2) }} ₺</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Ödeme Yöntemi:</strong></td>
                                                <td>{{ ucfirst($order->payment_method) }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Notlar:</strong></td>
                                                <td>{{ $order->notes ?: 'Not yok' }}</td>
                                            </tr>
                                            
                                           
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Müşteri Bilgileri -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <i class="fas fa-user"></i> Müşteri Bilgileri
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Ad Soyad:</strong></td>
                                                <td>{{ $order->customer_name }} {{ $order->customer_surname }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Telefon:</strong></td>
                                                <td>{{ $order->customer_phone }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>E-posta:</strong></td>
                                                <td>{{ $order->user->email }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Teslimat Adresi:</strong></td>
                                                <td>{{ $order->shipping_address }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>İl/İlçe:</strong></td>
                                                <td>{{ $order->city ?? 'Belirtilmemiş' }} / {{ $order->district ?? 'Belirtilmemiş' }}</td>
                                            </tr>
                                            @if($customer && $customer->unvan)
                                            <tr>
                                                <td><strong>Firma:</strong></td>
                                                <td>{{ $customer->unvan }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sipariş Durumu Güncelleme -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-cogs"></i> Sipariş Durumu Güncelleme
                                </h5>
                            </div>
                            <div class="card-body">

                            </div>
                        </div>

       
                      <br>

                        <!-- Ürün Durumları -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-boxes"></i> Alt Sipariş
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($order->cartItems as $item)
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100">
                                                <div class="card-header">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ explode(',', $item->product->images)[0] }}" alt="{{ $item->product->title }}" 
                                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;" class="me-3">
                                                        <div class="flex-grow-1">
                                                            <h6 class="card-title mb-1">{{ $item->product->title }}</h6>
                                                            <p class="card-text mb-0">
                                                                <small class="text-muted">Adet: {{ $item->quantity }}</small>
                                                                <span class="ms-2 fw-bold text-primary">
                                                                    <x-price-display :item="$item" :showQuantity="true" :showDiscountBadge="true" />
                                                                </span>
                                                            </p>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                                <div class="card-body">
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
                                                            <div class="mb-3 p-3 bg-light border-start border-4 border-info">
                                                                <h6 class="fw-bold text-info mb-2">
                                                                    <i class="fas fa-sticky-note"></i> Sipariş Notu
                                                                </h6>
                                                                <p class="mb-0 text-dark">{{ $notes['order_note'] }}</p>
                                                            </div>
                                                        @endif
                                                 
                                                    @endif
                                                         <!-- Acil üretim bilgisi -->
                                                         @if($item->urgent_status == 1)
                                                            
                                                         <small class="text-success fw-bold">🚨 Acil Üretim +{{ number_format($item->product->urgent_price, 2) }} ₺ </small>
                                                 
                                                 @endif
                                                    
                                                    <!-- Basit Ürün Durum Güncelleme -->
                                                 
                                                        <h6 class="fw-bold text-secondary mb-2">Alt Sipariş Durumunu Güncelle</h6>
                                                        <form action="{{ route('admin.orders.update-cart-status', $order->id) }}" method="POST" id="statusForm{{ $item->id }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="cart_id" value="{{ $item->id }}">
                                                            <input type="hidden" name="cargo_company" id="cargoCompany{{ $item->id }}" value="">
                                                            <input type="hidden" name="payment_type" id="paymentType{{ $item->id }}" value="">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <select name="order_status_id" class="form-select form-select-sm" onchange="checkCargoStatus(this, {{ $item->id }})">
                                                                        <option value="">Durum Seçin</option>
                                                                        @foreach($orderStatuses as $status)
                                                                            <option value="{{ $status->id }}">{{ $status->title }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <button type="submit" class="btn btn-warning btn-sm">
                                                                        <i class="fas fa-sync-alt"></i> Güncelle
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>


                                                        @php
                                                            $productStatuses = \App\Models\OrderStatusHistory::where('cart_id', $item->id)
                                                                ->with('orderStatus', 'user')
                                                                ->orderBy('created_at', 'desc')
                                                                ->get();
                                                        @endphp

                                                    <!-- Durum Geçmişi -->
                                                    <div class="timeline-container">
                                                        <h6 class="fw-bold text-secondary mb-2">Durum Geçmişi:</h6>
                                                        <div class="timeline">
                                                            @forelse($productStatuses as $statusHistory)
                                                                <div class="timeline-item">
                                                                    <div class="timeline-marker active">
                                                                        <i class="fas fa-circle text-{{ $statusHistory->orderStatus->id == 2 ? 'warning' : ($statusHistory->orderStatus->id == 3 ? 'info' : ($statusHistory->orderStatus->id == 4 ? 'primary' : ($statusHistory->orderStatus->id == 5 ? 'success' : 'secondary'))) }}"></i>
                                                                    </div>
                                                                    <div class="timeline-content d-flex justify-content-between align-items-start">
                                                                        <div>
                                                                            <h6 class="mb-1">{{ $statusHistory->orderStatus->title }}</h6>
                                                                            <small class="text-muted">
                                                                                {{ $statusHistory->created_at->format('d.m.Y H:i') }}
                                                                                @if($statusHistory->user)
                                                                                    • {{ $statusHistory->user->name }}
                                                                                @else
                                                                                    • <span class="text-muted">Kullanıcı Bulunamadı (ID: {{ $statusHistory->user_id }})</span>
                                                                                @endif
                                                                            </small>
                                                                        </div>
                                                                        @if(Auth::user()->roles()->where('roles.id', 1)->exists())
                                                                            <form action="{{ route('admin.orders.delete-status-history', $statusHistory->id) }}" method="POST" class="ms-2">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bu durum geçmişi kaydını silmek istediğinizden emin misiniz?')" title="Durumu Sil">
                                                                                    <i class="fas fa-trash"></i>
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
                                                        @if(!empty($item->s3_zip))
                                                            <div class="mt-3">
                                                                <a href="{{ route('admin.orders.download-cart-files', ['order' => $order->id, 'cart' => $item->id]) }}"
                                                                   class="btn btn-primary btn-sm">
                                                                    <i class="fas fa-image"></i> Dosyaları İndir
                                                                </a>
                                                                <small class="text-muted d-block mt-1">Müşteri tarafından yüklenen görseller (cart_{{ $item->id }})</small>
                                                            </div>
                                                        @endif
                                                        <!-- Kargo PDF Butonu -->
                                                        <div class="mt-3">
                                                            <a href="{{ route('admin.orders.cargo-pdf', ['order' => $order->id, 'cart' => $item->id]) }}" 
                                                               class="btn btn-primary btn-sm" target="_blank">
                                                                <i class="fas fa-truck"></i> Kargo PDF
                                                            </a>
                                                            <small class="text-muted d-block mt-1">Teslimat bilgileri ile etiket yazdırma PDF'i</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Kargo Bilgileri -->
                                                    @if($item->cargo_barcode)
                                                        <div class="mb-3">
                                                            <h6 class="fw-bold text-secondary mb-2">
                                                <i class="fas fa-truck"></i> Kargo Bilgileri
                                            </h6>
                                            <div class="card bg-light">
                                                <div class="card-body p-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <strong>Kargo Barkodu:</strong><br>
                                                            <span class="text-primary fw-bold">{{ $item->cargo_barcode }}</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Takip Linki:</strong><br>
                                                            @if($item->tracking_url)
                                                                <a href="{{ $item->tracking_url }}" 
                                                                   target="_blank" class="btn btn-info btn-sm">
                                                                    <i class="fas fa-external-link-alt"></i> Takip Et
                                                                </a>
                                                                @if(str_contains($item->cargo_barcode, 'Takip:'))
                                                                    @php
                                                                        $trackingNumber = explode('Takip:', $item->cargo_barcode)[1];
                                                                        $trackingNumber = trim(str_replace(')', '', $trackingNumber));
                                                                    @endphp
                                                                    <br>
                                                                    <small class="text-muted">Takip No: {{ $trackingNumber }}</small>
                                                                @endif
                                                            @elseif(str_contains($item->cargo_barcode, 'Takip:'))
                                                                @php
                                                                    $trackingNumber = explode('Takip:', $item->cargo_barcode)[1];
                                                                    $trackingNumber = trim(str_replace(')', '', $trackingNumber));
                                                                    $cartBarcode = $item->barcode; // Cart'ın barcode değeri
                                                                @endphp
                                                                <a href="https://sube.kolaygelsin.com/takip?ccode=29532262&musref={{ $cartBarcode }}" 
                                                                   target="_blank" class="btn btn-info btn-sm">
                                                                    <i class="fas fa-external-link-alt"></i> Takip Et
                                                                </a>
                                                                <br>
                                                                <small class="text-muted">Takip No: {{ $trackingNumber }}</small>
                                                            @endif
                                                            
                                                            <!-- ZPL Yazdır Butonu -->
                                                            @if($item->barcode_zpl)
                                                                <br><br>
                                                                <strong>Barcode Yazdır:</strong><br>
                                                                <a href="{{ route('admin.orders.zpl-pdf', ['order' => $order->id, 'cart' => $item->id]) }}" 
                                                                   class="btn btn-success btn-sm" target="_blank">
                                                                    <i class="fas fa-file-pdf"></i> ZPL PDF
                                                                </a>
                                                                <small class="text-muted d-block mt-1">PDF olarak indir ve yazdır</small>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kargo Firması Seçim Modal -->
    <div class="modal fade" id="cargoModal" tabindex="-1" aria-labelledby="cargoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cargoModalLabel">
                        <i class="fas fa-truck"></i> Kargo Firması Seçin
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Sipariş durumu "Kargoya Verildi" olarak güncellenecek. Lütfen kargo firmasını seçin:</p>
                    
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <h6 class="fw-bold text-secondary mb-2">Kargo Firması</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="cargo_company_modal" id="everest" value="everest">
                                <label class="form-check-label" for="everest">
                                    <strong>Everest Kargo</strong>
                                </label>
                            </div>
                       
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="cargo_company_modal" id="yurtici" value="yurtici">
                                <label class="form-check-label" for="yurtici">
                                    <strong>Yurtiçi Kargo</strong>
                                </label>
                            </div>
                       
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="cargo_company_modal" id="kolay_gelsin" value="kolay_gelsin">
                                <label class="form-check-label" for="kolay_gelsin">
                                    <strong>Kolay Gelsin</strong>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                           <h6 class="fw-bold text-secondary mb-2">Ödeme Tipi</h6>
                           <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_type"  value="1">
                            <label class="form-check-label" for="">
                                <strong>Alıcı Ödemeli</strong>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_type"  value="2">
                            <label class="form-check-label" for="">
                                <strong>Gönderici Ödemeli</strong>
                            </label>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" onclick="confirmCargoSelection()">
                        <i class="fas fa-check"></i> Onayla
                    </button>
                </div>
            </div>
        </div>
    </div>

<style>
.timeline-container {
    position: relative;
    padding: 20px 0;
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
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 5px;
}

.timeline-marker {
    position: absolute;
    left: -18px;
    top: 3px;
    width: 10px;
    height: 10px;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
}

.timeline-marker.active {
    border-color: #007bff;
    background: #007bff;
}

.timeline-marker i {
    font-size: 12px;
    color: white;
}

.timeline-marker:not(.active) i {
    color: #6c757d;
}

.timeline-content {
    padding-left: 10px;
}

.timeline-content h6 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
}

.timeline-content small {
    font-size: 12px;
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
        document.querySelector(`#statusForm${currentCartId} select[name="order_status_id"]`).value = '';
    }
    
    // Radio button'ları reset et
    document.querySelectorAll('input[name="cargo_company_modal"]').forEach(radio => {
        radio.checked = false;
    });
       // Radio button'ları reset et
       document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
        radio.checked = false;
    });
    
    currentCartId = null;
    currentStatusId = null;
});

        // ZPL PDF oluşturma fonksiyonu (artık kullanılmıyor - backend route kullanılıyor)
        function printZplBarcode(zplData, barcodeInfo) {
            alert('Bu özellik artık backend route ile çalışıyor. ZPL PDF butonuna tıklayın.');
        }

// ZPL verisini yazıcıya gönderme (placeholder)
function sendToPrinter(zplData) {
    alert('ZPL verisi yazıcıya gönderilecek:\n\n' + zplData.substring(0, 100) + '...\n\nBu özellik için barcode yazıcı sürücüsü gerekli.');
}

// ZPL'i PDF'e çevirme fonksiyonu
function generateZplPDF(zplData, barcodeInfo) {
    try {
        // jsPDF kütüphanesini yükle
        if (typeof jsPDF === 'undefined') {
            // jsPDF CDN'den yükle
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
            script.onload = () => createZplPDF(zplData, barcodeInfo);
            document.head.appendChild(script);
        } else {
            createZplPDF(zplData, barcodeInfo);
        }
    } catch (error) {
        console.error('PDF oluşturma hatası:', error);
        alert('PDF oluşturma sırasında hata oluştu: ' + error.message);
    }
}

// PDF oluşturma fonksiyonu
function createZplPDF(zplData, barcodeInfo) {
    try {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        // PDF başlığı
        doc.setFontSize(20);
        doc.text('🚚 ZPL Barcode Etiketi', 20, 20);
        
        // Kargo bilgileri
        doc.setFontSize(12);
        doc.text('Kargo Bilgileri:', 20, 40);
        doc.setFontSize(10);
        doc.text(`Barcode: ${barcodeInfo}`, 20, 50);
        doc.text(`Tarih: ${new Date().toLocaleString('tr-TR')}`, 20, 60);
        
        // ZPL verisi
        doc.setFontSize(12);
        doc.text('ZPL Verisi (Yazıcı Komutları):', 20, 80);
        doc.setFontSize(8);
        
        // ZPL verisini satırlara böl
        const zplLines = zplData.split('\n');
        let yPosition = 90;
        
        zplLines.forEach((line, index) => {
            if (yPosition > 250) {
                doc.addPage();
                yPosition = 20;
            }
            doc.text(line.trim(), 20, yPosition);
            yPosition += 5;
        });
        
        // PDF'i indir
        const fileName = `ZPL_Barcode_${barcodeInfo.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().getTime()}.pdf`;
        doc.save(fileName);
        
        alert('PDF başarıyla oluşturuldu ve indirildi!');
        
    } catch (error) {
        console.error('PDF oluşturma hatası:', error);
        alert('PDF oluşturma sırasında hata oluştu: ' + error.message);
    }
}
</script>
@endsection 