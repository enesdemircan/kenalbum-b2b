@extends('frontend.master')

@section('content')
<main>
  <div class="mb-4 pb-4"></div>
  <section class="profile-shell container">

    <div class="profile-shell__head">
      <div>
        <h1 class="profile-shell__heading"><i class="fas fa-bag-shopping"></i> Siparişlerim</h1>
        <p class="profile-shell__sub">Geçmiş ve devam eden siparişlerinizi takip edin.</p>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-3">
        @include('frontend.profile._sidebar', ['active' => 'orders'])
      </div>

      <div class="col-lg-9">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <div class="profile-card profile-filter-form">
          <div class="profile-card__head">
            <h3 class="profile-card__title"><i class="fas fa-filter"></i> Filtrele</h3>
          </div>
          <form method="GET" action="{{ route('profile.orders') }}" class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Telefon</label>
              <input type="text" class="form-control" name="phone" value="{{ $filters['phone'] ?? '' }}" placeholder="Telefon ara">
            </div>
            <div class="col-md-3">
              <label class="form-label">Başlangıç Tarihi</label>
              <input type="date" class="form-control" name="start_date" value="{{ $filters['start_date'] ?? '' }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">Bitiş Tarihi</label>
              <input type="date" class="form-control" name="end_date" value="{{ $filters['end_date'] ?? '' }}">
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
              <button type="submit" class="cm-btn cm-btn--primary" style="padding:10px 14px; font-size:.85rem;"><i class="fas fa-search"></i></button>
              <a href="{{ route('profile.orders') }}" class="cm-btn cm-btn--ghost" style="padding:10px 14px; font-size:.85rem;"><i class="fas fa-times"></i></a>
            </div>
          </form>
        </div>

        @if($orders->count() > 0)
          <div class="profile-orders-list mt-3">
            @foreach($orders as $order)
              <div class="profile-order-item">
                <button class="profile-order-item__head" type="button" data-bs-toggle="collapse" data-bs-target="#orderBody{{ $order->id }}" aria-expanded="false" aria-controls="orderBody{{ $order->id }}">
                  <div>
                    <span class="profile-order-item__num">{{ $order->order_number }}</span>
                    <span class="profile-order-item__name">{{ $order->customer_name }} {{ $order->customer_surname }}</span>
                  </div>
                  <div class="d-flex align-items-center gap-3">
                    <span class="order-status-pill s-{{ (int) $order->status }}"><i class="fas fa-circle" style="font-size:.55rem"></i> {{ $order->status_text }}</span>
                    <span class="profile-order-item__date">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                    <i class="fas fa-chevron-down profile-order-item__chev"></i>
                  </div>
                </button>
                <div id="orderBody{{ $order->id }}" class="collapse">
                  <div class="profile-order-item__body">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <div class="profile-card" style="margin:0;">
                          <div class="profile-card__head">
                            <h3 class="profile-card__title"><i class="fas fa-info-circle"></i> Sipariş Bilgileri</h3>
                          </div>
                          <p class="mb-1"><strong>Sipariş No:</strong> <span style="color:#ea580c;">{{ $order->order_number }}</span></p>
                          <p class="mb-1"><strong>Tarih:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
                          <p class="mb-1"><strong>Ödeme:</strong> {{ ucfirst($order->payment_method) }}</p>
                          @canSeePrices
                            <p class="mb-0"><strong>Toplam:</strong> <b style="color:#ea580c;">₺{{ number_format($order->total_price, 2, ',', '.') }}</b></p>
                          @endif
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="profile-card" style="margin:0;">
                          <div class="profile-card__head">
                            <h3 class="profile-card__title"><i class="fas fa-truck"></i> Teslimat</h3>
                            @if((int) $order->status === 0)
                              <button type="button" class="btn-orange-sm" data-bs-toggle="modal" data-bs-target="#editDeliveryModal{{ $order->id }}"><i class="fas fa-edit"></i> Düzenle</button>
                            @endif
                          </div>
                          <p class="mb-1"><strong>{{ $order->customer_name }} {{ $order->customer_surname }}</strong></p>
                          <p class="mb-1 text-muted"><i class="fas fa-phone" style="color:#ea580c; width:14px;"></i> {{ $order->customer_phone }}</p>
                          <p class="mb-1 text-muted"><i class="fas fa-location-dot" style="color:#ea580c; width:14px;"></i> {{ $order->city ?? '—' }} / {{ $order->district ?? '—' }}</p>
                          <p class="mb-0 text-muted">{{ $order->shipping_address }}</p>
                          @if((int) $order->status !== 0)
                            <small class="text-muted d-block mt-2"><i class="fas fa-lock"></i> Onaylanan siparişlerin teslimat bilgisi değiştirilemez.</small>
                          @endif
                        </div>
                      </div>
                    </div>

                    @if((int) $order->status === 0)
                      <div class="modal fade" id="editDeliveryModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <form method="POST" action="{{ route('profile.orders.delivery.update', $order->id) }}">
                              @csrf @method('PUT')
                              <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-truck me-2" style="color:#ea580c"></i> Teslimat Bilgilerini Düzenle — {{ $order->order_number }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>
                              <div class="modal-body">
                                <div class="row g-3">
                                  <div class="col-md-6"><label class="form-label">Ad <span class="text-danger">*</span></label><input type="text" class="form-control" name="customer_name" value="{{ $order->customer_name }}" required></div>
                                  <div class="col-md-6"><label class="form-label">Soyad <span class="text-danger">*</span></label><input type="text" class="form-control" name="customer_surname" value="{{ $order->customer_surname }}" required></div>
                                  <div class="col-md-6"><label class="form-label">Telefon <span class="text-danger">*</span></label><input type="text" class="form-control" name="customer_phone" value="{{ $order->customer_phone }}" required></div>
                                  <div class="col-md-3"><label class="form-label">İl <span class="text-danger">*</span></label><input type="text" class="form-control" name="city" value="{{ $order->city }}" required></div>
                                  <div class="col-md-3"><label class="form-label">İlçe <span class="text-danger">*</span></label><input type="text" class="form-control" name="district" value="{{ $order->district }}" required></div>
                                  <div class="col-12"><label class="form-label">Adres <span class="text-danger">*</span></label><textarea class="form-control" name="shipping_address" rows="3" required>{{ $order->shipping_address }}</textarea></div>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="cm-btn cm-btn--ghost" data-bs-dismiss="modal">İptal</button>
                                <button type="submit" class="cm-btn cm-btn--primary"><i class="fas fa-save"></i> Kaydet</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    @endif

                    <h6 class="fw-bold mt-4 mb-3" style="color:#0a0a0a;"><i class="fas fa-boxes-stacked me-2" style="color:#ea580c"></i> Ürün Detayları</h6>
                    <div class="row g-3">
                      @foreach($order->cartItems as $item)
                        <div class="col-md-6">
                          <div class="profile-card" style="margin:0;">
                            <div class="d-flex align-items-center gap-3 mb-2">
                              <img src="{{ explode(',', $item->product->images)[0] }}" alt="{{ $item->product->title }}" style="width:50px; height:50px; object-fit:cover; border-radius:10px;">
                              <div class="flex-grow-1">
                                <strong>{{ $item->product->title }}</strong>
                                <p class="mb-0"><small class="text-muted">{{ $item->quantity }} Adet</small>
                                  @canSeePrices <span class="ms-2 fw-bold" style="color:#ea580c"><x-price-display :item="$item" :showQuantity="true" :showDiscountBadge="true" /></span> @endif
                                </p>
                              </div>
                            </div>
                            @if($item->notes)
                              @php $notes = json_decode($item->notes, true); $customizations = $notes['customizations'] ?? []; $totalCustomizationPrice = $notes['total_customization_price'] ?? 0; @endphp
                              @if($customizations)
                                <x-customization-list :customizations="$customizations" :totalCustomizationPrice="$totalCustomizationPrice" />
                              @endif
                              @if(isset($notes['order_note']) && !empty($notes['order_note']))
                                <div class="mt-2 p-2" style="background:#fff7ed; border-left:3px solid #ea580c; border-radius:6px;">
                                  <small class="fw-bold" style="color:#9a3412;"><i class="fas fa-sticky-note"></i> Sipariş Notu</small>
                                  <p class="mb-0 mt-1"><small>{{ $notes['order_note'] }}</small></p>
                                </div>
                              @endif
                            @endif
                            @if($item->urgent_status == 1)
                              <small class="text-success-2 fw-bold d-block mt-2">🚨 Acil Üretim @canSeePrices +{{ number_format($item->product->urgent_price, 2) }} ₺ @endcanSeePrices</small>
                            @endif
                            @if(($notes['design_service'] ?? null) === 'with_design')
                              <small class="text-success-2 fw-bold d-block">✏️ Tasarımı Bize Yaptır @canSeePrices +{{ number_format($item->product->design_service_price ?? 0, 2) }} ₺ @endcanSeePrices</small>
                            @elseif(($notes['design_service'] ?? null) === 'self_design')
                              <small class="text-muted d-block">✏️ Tasarımı kendim yapacağım</small>
                            @endif
                            @php
                              $latestStatus = \App\Models\OrderStatusHistory::where('cart_id', $item->id)->with('orderStatus')->orderBy('created_at','desc')->first();
                            @endphp
                            @if($latestStatus && $latestStatus->orderStatus)
                              <div class="d-flex align-items-center gap-2 mt-3 pt-3" style="border-top:1px solid #f1f5f9;">
                                <span class="order-status-pill s-{{ $latestStatus->orderStatus->id - 1 }}" style="font-size:.78rem;"><i class="fas fa-circle" style="font-size:.5rem"></i> {{ $latestStatus->orderStatus->title }}</span>
                                <small class="text-muted ms-auto">{{ $latestStatus->created_at->format('d.m.Y H:i') }}</small>
                              </div>
                            @endif
                          </div>
                        </div>
                      @endforeach
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                      <a href="{{ route('orders.show', $order->id) }}" class="cm-btn cm-btn--ghost" style="font-size:.85rem; padding:8px 16px;"><i class="fas fa-eye"></i> Detay Sayfası</a>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          @if($orders->hasPages())
            <div class="d-flex justify-content-center mt-4">{{ $orders->appends(request()->query())->links() }}</div>
          @endif
        @else
          <div class="profile-empty">
            <i class="fas fa-bag-shopping"></i>
            <h4>Henüz siparişiniz yok</h4>
            <p>İlk siparişinizi vermek için ürünlere göz atın.</p>
            <a href="{{ route('products.index') }}" class="cm-btn cm-btn--primary"><i class="fas fa-cart-plus"></i> Alışverişe Başla</a>
          </div>
        @endif
      </div>
    </div>

  </section>
</main>
<br>
@endsection
