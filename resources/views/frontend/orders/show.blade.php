@extends('frontend.master')

@section('content')

<main>
  <div class="mb-4 pb-4"></div>
  <section class="profile-shell container">

    <div class="checkout-multi__steps cart-step-bar">
      <div class="cm-step is-done"><span class="cm-step__num">1</span><span class="cm-step__label">Sepetim</span></div>
      <div class="cm-step is-done"><span class="cm-step__num">2</span><span class="cm-step__label">Teslimat</span></div>
      <div class="cm-step is-done"><span class="cm-step__num">3</span><span class="cm-step__label">Fatura</span></div>
      <div class="cm-step is-done"><span class="cm-step__num">4</span><span class="cm-step__label">Kargo</span></div>
      <div class="cm-step is-done"><span class="cm-step__num">5</span><span class="cm-step__label">Dosya</span></div>
      <div class="cm-step is-active"><span class="cm-step__num">6</span><span class="cm-step__label">Onay</span></div>
    </div>

    <div class="order-success">
      <div class="order-success__icon"><i class="fas fa-check"></i></div>
      <h1 class="order-success__title">Siparişiniz alındı!</h1>
      <p class="order-success__sub">Teşekkürler. Sipariş numaranızı aşağıda bulabilir, durumunu hesabınızdan takip edebilirsiniz.</p>
      <div class="order-success__meta"><i class="fas fa-receipt"></i> Sipariş No: <strong>{{ $order->order_number }}</strong></div>
    </div>

    <div class="order-summary-grid">
      <div class="order-summary-card">
        <p class="order-summary-card__label">Tarih</p>
        <p class="order-summary-card__value">{{ $order->created_at->format('d.m.Y') }} <small class="text-muted">{{ $order->created_at->format('H:i') }}</small></p>
      </div>
      @canSeePrices
        <div class="order-summary-card">
          <p class="order-summary-card__label">Toplam</p>
          <p class="order-summary-card__value is-orange">₺{{ number_format($order->total_price, 2, ',', '.') }}</p>
        </div>
      @endif
      <div class="order-summary-card">
        <p class="order-summary-card__label">Ödeme</p>
        <p class="order-summary-card__value"><i class="fas fa-wallet" style="color:#ea580c"></i> Bakiye</p>
      </div>
      <div class="order-summary-card">
        <p class="order-summary-card__label">Durum</p>
        <p class="order-summary-card__value">
          <span class="order-status-pill s-{{ (int) $order->status }}">
            <i class="fas fa-circle" style="font-size:.55rem"></i> {{ $order->status_text }}
          </span>
        </p>
      </div>
    </div>

    <div class="profile-card">
      <div class="profile-card__head">
        <h3 class="profile-card__title"><i class="fas fa-box-open"></i> Sipariş Detayları</h3>
      </div>
      <div class="table-responsive">
        <table class="order-detail-table">
          <thead>
            <tr>
              <th>Ürün</th>
              <th style="text-align:center;">Adet</th>
              @canSeePrices <th style="text-align:right;">Fiyat</th> @endif
              @canSeePrices <th style="text-align:right;">Toplam</th> @endif
            </tr>
          </thead>
          <tbody>
            @foreach($order->cartItems as $item)
              <tr>
                <td>
                  <div class="d-flex align-items-start gap-3">
                    @if($item->product->images)
                      <img src="{{ explode(',', $item->product->images)[0] }}" alt="{{ $item->product->title }}" style="width:54px; height:54px; object-fit:cover; border-radius:10px; flex-shrink:0;">
                    @endif
                    <div>
                      <strong>{{ $item->product->title }}</strong>
                      @php $itemNotes = $item->notes ? json_decode($item->notes, true) : []; @endphp
                      @if($item->parsed_notes && isset($item->parsed_notes['customizations']))
                        <ul class="shopping-cart__product-item__options mb-0 mt-1" style="overflow:auto;">
                          @if($item->page_count > 1)
                            <li><small class="text-primary fw-medium">Yaprak Adeti:</small> <small class="text-muted">{{ $item->page_count }} yaprak ({{ $item->page_count * 2 }} sayfa)</small></li>
                          @endif
                          @if($item->barcode)
                            <li><small class="text-primary fw-medium">Barkod:</small> <small class="text-muted">{{ $item->barcode }}</small></li>
                          @endif
                          @foreach($item->parsed_notes['customizations'] as $categoryId => $customization)
                            @php $category = App\Models\CustomizationCategory::find($categoryId); @endphp
                            @if($category)
                              <li><x-customization-display :customization="$customization" :category="$category" /></li>
                            @endif
                          @endforeach
                          @if($item->urgent_status == 1)
                            <li><small class="text-success-2 fw-bold">🚨 Acil Üretim @canSeePrices +{{ number_format($item->product->urgent_price, 2) }} ₺ @endcanSeePrices</small></li>
                          @endif
                          @if(($itemNotes['design_service'] ?? null) === 'with_design')
                            <li><small class="text-success-2 fw-bold">✏️ Tasarımı Bize Yaptır @canSeePrices +{{ number_format($item->product->design_service_price ?? 0, 2) }} ₺ @endcanSeePrices</small></li>
                          @elseif(($itemNotes['design_service'] ?? null) === 'self_design')
                            <li><small class="text-muted">✏️ Tasarımı kendim yapacağım</small></li>
                          @endif
                        </ul>
                      @endif
                    </div>
                  </div>
                </td>
                <td style="text-align:center;">{{ $item->quantity }}</td>
                @canSeePrices <td style="text-align:right;"><x-price-display :item="$item" :showQuantity="false" :showDiscountBadge="true" /></td> @endif
                @canSeePrices <td style="text-align:right;"><strong><x-price-display :item="$item" :showQuantity="true" :showDiscountBadge="true" /></strong></td> @endif
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-md-6">
        <div class="profile-card h-100">
          <div class="profile-card__head">
            <h3 class="profile-card__title"><i class="fas fa-truck"></i> Teslimat Bilgileri</h3>
          </div>
          <p class="mb-2"><strong>{{ $order->customer_name }} {{ $order->customer_surname }}</strong></p>
          <p class="mb-1 text-muted"><i class="fas fa-phone" style="color:#ea580c; width:14px;"></i> {{ $order->customer_phone }}</p>
          <p class="mb-1 text-muted"><i class="fas fa-location-dot" style="color:#ea580c; width:14px;"></i> {{ $order->city ?? '—' }} / {{ $order->district ?? '—' }}</p>
          <p class="mb-0 text-muted">{{ $order->shipping_address }}</p>
          @if($order->shippingMethod)
            <hr style="border-color:#f1f5f9;">
            <p class="mb-0 text-muted"><i class="fas fa-truck-fast" style="color:#ea580c; width:14px;"></i> {{ $order->shippingMethod->title }}
              @if($order->shipping_cost > 0)
                · ₺{{ number_format($order->shipping_cost, 2, ',', '.') }}
              @else
                · <span style="color:#16a34a; font-weight:700;">Ücretsiz</span>
              @endif
            </p>
          @endif
        </div>
      </div>

      <div class="col-md-6">
        <div class="profile-card h-100">
          <div class="profile-card__head">
            <h3 class="profile-card__title"><i class="fas fa-file-invoice"></i> Fatura Bilgileri</h3>
          </div>
          @if($order->billing_same_as_shipping)
            <p class="text-muted mb-0"><em>Teslimat adresi ile aynı</em></p>
          @elseif($order->billing_name)
            <p class="mb-2"><strong>{{ $order->billing_name }} {{ $order->billing_surname }}</strong></p>
            @if($order->billing_company)
              <p class="mb-1 text-muted"><i class="fas fa-building" style="color:#ea580c; width:14px;"></i> {{ $order->billing_company }}</p>
            @endif
            @if($order->billing_tax_no)
              <p class="mb-1 text-muted"><i class="fas fa-id-card" style="color:#ea580c; width:14px;"></i> {{ $order->billing_tax_no }}</p>
            @endif
            <p class="mb-1 text-muted"><i class="fas fa-phone" style="color:#ea580c; width:14px;"></i> {{ $order->billing_phone }}</p>
            <p class="mb-1 text-muted"><i class="fas fa-location-dot" style="color:#ea580c; width:14px;"></i> {{ $order->billing_city }} / {{ $order->billing_district }}</p>
            <p class="mb-0 text-muted">{{ $order->billing_address }}</p>
          @else
            <p class="text-muted mb-0"><em>—</em></p>
          @endif
        </div>
      </div>
    </div>

    @if($order->notes)
      <div class="profile-card mt-3">
        <div class="profile-card__head">
          <h3 class="profile-card__title"><i class="fas fa-sticky-note"></i> Sipariş Notu</h3>
        </div>
        <p class="mb-0 text-muted">{{ $order->notes }}</p>
      </div>
    @endif

    <div class="d-flex justify-content-center gap-2 mt-4">
      <a href="{{ route('profile.orders') }}" class="cm-btn cm-btn--primary"><i class="fas fa-bag-shopping"></i> Siparişlerim</a>
      <a href="{{ route('products.index') }}" class="cm-btn cm-btn--ghost"><i class="fas fa-arrow-left"></i> Alışverişe Devam Et</a>
    </div>

  </section>
</main>

@endsection
