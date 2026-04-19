@extends('frontend.master')

@section('content')
<main>
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">SİPARİŞLERİM</h2>
      <div class="row">
        <div class="col-lg-3">
          <ul class="account-nav">
            <li><a href="{{ route('profile.index') }}" class="menu-link menu-link_us-s">DASHBOARD</a></li>
            <li><a href="{{ route('profile.orders') }}" class="menu-link menu-link_us-s menu-link_active">SİPARİŞLERİM</a></li>
            <li><a href="{{ route('profile.addresses') }}" class="menu-link menu-link_us-s">ADRESLERİM</a></li>
            <li><a href="{{ route('profile.detail') }}" class="menu-link menu-link_us-s">HESAP DETAYLARI</a></li>
            @if(auth()->user()->hasRole(1))
            <li><a href="{{ route('admin.dashboard') }}" class="menu-link menu-link_us-s">YÖNETİM PANELİ</a></li>
            @endif
            @if(auth()->user()->hasRole('Satış Müdürü'))
            <li><a href="{{ route('admin.customers.index') }}" class="menu-link menu-link_us-s">MÜŞTERİ LİSTESİ</a></li>
            @endif
            @if(auth()->user()->hasRole(3))
            <li><a href="{{ route('profile.personels') }}" class="menu-link menu-link_us-s">PERSONELLERİM</a></li>
            @endif
            <li>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="menu-link menu-link_us-s border-0 bg-transparent" style="width: 100%; text-align: left; padding: 0; font-size: 14px;font-weight: 500;text-transform: uppercase;">
                        Çıkış Yap
                    </button>
                </form>
            </li>
          </ul>
        </div>
        <div class="col-lg-9">
          <div class="page-content my-account__orders-list">
            
            <!-- Filtreleme Formu -->
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtrele</h5>
              </div>
              <div class="card-body">
                <form method="GET" action="{{ route('profile.orders') }}" class="row g-3">
                  <div class="col-md-4">
                    <label for="phone" class="form-label">Telefon Numarası</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ $filters['phone'] ?? '' }}" placeholder="Telefon numarası ara...">
                  </div>
                  <div class="col-md-3">
                    <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $filters['start_date'] ?? '' }}">
                  </div>
                  <div class="col-md-3">
                    <label for="end_date" class="form-label">Bitiş Tarihi</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $filters['end_date'] ?? '' }}">
                  </div>
                  <div class="col-md-2 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                      <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search"></i> Filtrele
                      </button>
                      <a href="{{ route('profile.orders') }}" class="btn btn-secondary btn-sm" style="padding: 0.375rem 0.25rem;">
                        <i class="fas fa-times"></i> Temizle
                      </a>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            
            @if($orders->count() > 0)
              <div class="accordion" id="ordersAccordion">
                @foreach($orders as $order)
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $order->id }}">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $order->id }}" aria-expanded="false" aria-controls="collapse{{ $order->id }}">
                        <div class="d-flex justify-content-between align-items-center w-100 me-3">
                          <div style="text-align: left;">
                            <strong>{{ $order->order_number }}</strong>
                            <br>
                            <small class="text-muted">{{ $order->customer_name }} {{ $order->customer_surname }}</small>
                          </div>
                          <div class="text-end">
                            <small class="text-muted">{{ $order->created_at->format('d.m.Y H:i') }}</small>
                          </div>
                        </div>
                      </button>
                    </h2>
                    <div id="collapse{{ $order->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $order->id }}" data-bs-parent="#ordersAccordion">
                      <div class="accordion-body">
                        <div class="row">
                          <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">
                              <i class="fas fa-info-circle me-2"></i>Sipariş Bilgileri
                            </h6>
                            <div class="card border-0">
                              <div class="card-body">
                                <p class="mb-2"><strong>Sipariş No:</strong> <span class="text-primary">{{ $order->order_number }}</span></p>
                                <p class="mb-2"><strong>Tarih:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
                                <p class="mb-2"><strong>Ödeme Yöntemi:</strong> {{ ucfirst($order->payment_method) }}</p>
                                @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1))
                                <p class="mb-0"><strong>Toplam:</strong> <b>{{ number_format($order->total_price, 2) }} ₺</b></p>
                                @endif
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">
                              <i class="fas fa-shipping-fast me-2"></i>Teslimat Bilgileri
                            </h6>
                            <div class="card border-0">
                              <div class="card-body">
                                <p class="mb-2"><strong>Ad Soyad:</strong> {{ $order->customer_name }} {{ $order->customer_surname }}</p>
                                <p class="mb-2"><strong>Telefon:</strong> {{ $order->customer_phone }}</p>
                                <p class="mb-2"><strong>İl/İlçe:</strong> {{ $order->city ?? 'Belirtilmemiş' }} / {{ $order->district ?? 'Belirtilmemiş' }}</p>
                                <p class="mb-0"><strong>Adres:</strong> {{ $order->shipping_address }}</p>
                              </div>
                            </div>
                          </div>
                        </div>
                        <br>
                        <h6 class="fw-bold text-primary mb-3">
                          <i class="fas fa-boxes me-2"></i>Ürün Detayları
                        </h6>
                        <div class="row">
                          @foreach($order->cartItems as $item)
                            <div class="col-md-6 mb-4">
                              <div class="card h-100">
                                <div class="card-header bg-light">
                                  <div class="d-flex align-items-center">
                                    <img src="{{ explode(',', $item->product->images)[0] }}" alt="{{ $item->product->title }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;" class="me-3">
                                    <div class="flex-grow-1">
                                      <h6 class="card-title mb-1">{{ $item->product->title }}</h6>
                                      <p class="card-text mb-0">
                                        <small class="text-muted">Adet: {{ $item->quantity }}</small>
                                        @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1))
                                        <span class="ms-2 fw-bold text-primary">
                                          <x-price-display :item="$item" :showQuantity="true" :showDiscountBadge="true" />
                                        </span>
                                        @endif
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
                                     
                                        <x-customization-list :customizations="$customizations" :totalCustomizationPrice="$totalCustomizationPrice" />
                                   
                                    @endif
                                    
                                    <!-- Sipariş Notu -->
                                    @if(isset($notes['order_note']) && !empty($notes['order_note']))
                                      <div class="mb-3 p-3 bg-light border-start border-4 border-info rounded">
                                        <h6 class="fw-bold text-info mb-2">
                                          <i class="fas fa-sticky-note"></i> Sipariş Notu
                                        </h6>
                                        <p class="mb-0 text-dark">{{ $notes['order_note'] }}</p>
                                      </div>
                                    @endif
                                  @endif
                                  @if($item->urgent_status == 1)
                               
                                    <small class="text-success-2 fw-bold">🚨 Acil Üretim @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1)) +{{ number_format($item->product->urgent_price, 2) }} ₺ @endif</small>
                                
                                  @endif
                                  <!-- En Güncel Durum -->
                                  <div class="timeline-container">
                                    <h6 class="fw-bold text-secondary mb-2">Son Durum:</h6>
                                    <div class="timeline">
                                      @php
                                        // Her cart item için en güncel durumu al
                                        $latestStatus = \App\Models\OrderStatusHistory::where('cart_id', $item->id)
                                            ->with('orderStatus')
                                            ->orderBy('created_at', 'desc')
                                            ->first();
                                      @endphp
                                      @if($latestStatus && $latestStatus->orderStatus)
                                        <div class="timeline-item">
                                          <div class="timeline-marker active">
                                            <i class="fas fa-circle text-{{ $latestStatus->orderStatus->id == 2 ? 'warning' : ($latestStatus->orderStatus->id == 3 ? 'info' : ($latestStatus->orderStatus->id == 4 ? 'primary' : ($latestStatus->orderStatus->id == 5 ? 'success' : 'secondary'))) }}"></i>
                                          </div>
                                          <div class="timeline-content">
                                            <h6 class="mb-1">{{ $latestStatus->orderStatus->title }}</h6>
                                            <small class="text-muted">
                                              {{ $latestStatus->created_at->format('d.m.Y H:i') }}
                                            </small>
                                          </div>
                                        </div>
                                      @else
                                        <div class="timeline-item">
                                          <div class="timeline-marker">
                                            <i class="fas fa-circle text-muted"></i>
                                          </div>
                                          <div class="timeline-content">
                                            <h6 class="mb-1">Durum Yok</h6>
                                            <small class="text-muted">Henüz durum güncellemesi yapılmamış</small>
                                          </div>
                                        </div>
                                      @endif
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          @endforeach
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="text-center py-5">
                <i class="fas fa-shopping-bag text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Henüz Siparişiniz Yok</h4>
                <p class="text-muted">İlk siparişinizi vermek için alışverişe başlayın.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                  <i class="fas fa-shopping-cart"></i> Alışverişe Başla
                </a>
              </div>
            @endif
            
            <!-- Sayfalama -->
            @if($orders->hasPages())
              <div class="d-flex justify-content-center mt-4">
                {{ $orders->appends(request()->query())->links() }}
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>
  </main>
<br>
<br>
<style>
.accordion-button:not(.collapsed) {
  background-color: #f8f9fa;
  color: #495057;
}

.accordion-button:focus {
  box-shadow: none;
  border-color: rgba(0, 123, 255, 0.25);
}

.card {
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.badge {
  font-size: 0.8em;
  padding: 0.5em 0.8em;
}

.timeline-container {
  position: relative;
  padding: 5px 0;
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

.card-header {
  border-bottom: 1px solid #dee2e6;
  background-color: #f8f9fa !important;
}

.list-unstyled li {
  padding: 2px 0;
}
</style>

@endsection