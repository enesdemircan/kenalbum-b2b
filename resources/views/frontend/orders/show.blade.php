@extends('frontend.master')

@section('content')

<main>
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
      <h2 class="page-title">Siparişiniz Alındı!</h2>
      <div class="checkout-steps">
        <div class="checkout-steps__item active">
          <span class="checkout-steps__item-number">01</span>
          <span class="checkout-steps__item-title">
            <span>Sepet</span>
            <em>Ürünlerinizi Yönetin</em>
          </span>
        </div>
        <div class="checkout-steps__item active">
          <span class="checkout-steps__item-number">02</span>
          <span class="checkout-steps__item-title">
            <span>Ödeme ve Teslimat</span>
            <em>Siparişinizi Tamamlayın</em>
          </span>
        </div>
        <div class="checkout-steps__item active">
          <span class="checkout-steps__item-number">03</span>
          <span class="checkout-steps__item-title">
            <span>Onay</span>
            <em>Siparişiniz Alındı</em>
          </span>
        </div>
      </div>
      <div class="order-complete">
        <div class="order-complete__message">
          <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="40" cy="40" r="40" fill="#B9A16B"/>
            <path d="M52.9743 35.7612C52.9743 35.3426 52.8069 34.9241 52.5056 34.6228L50.2288 32.346C49.9275 32.0446 49.5089 31.8772 49.0904 31.8772C48.6719 31.8772 48.2533 32.0446 47.952 32.346L36.9699 43.3449L32.048 38.4062C31.7467 38.1049 31.3281 37.9375 30.9096 37.9375C30.4911 37.9375 30.0725 38.1049 29.7712 38.4062L27.4944 40.683C27.1931 40.9844 27.0257 41.4029 27.0257 41.8214C27.0257 42.24 27.1931 42.6585 27.4944 42.9598L33.5547 49.0201L35.8315 51.2969C36.1328 51.5982 36.5513 51.7656 36.9699 51.7656C37.3884 51.7656 37.8069 51.5982 38.1083 51.2969L40.385 49.0201L52.5056 36.8996C52.8069 36.5982 52.9743 36.1797 52.9743 35.7612Z" fill="white"/>
          </svg>
          <h3>Siparişiniz tamamlandı!</h3>
          <p>Teşekkürler. Siparişiniz başarıyla alındı.</p>
        </div>
        <div class="order-info">
          <div class="order-info__item">
            <label>Sipariş No</label>
            <span>{{ $order->order_number }}</span>
          </div>
          <div class="order-info__item">
            <label>Tarih</label>
            <span>{{ $order->created_at->format('d.m.Y H:i') }}</span>
          </div>
          @canSeePrices
          <div class="order-info__item">
            <label>Toplam</label>
            <span>{{ number_format($order->total_price, 2) }} ₺</span>
          </div>
          @endif
          <div class="order-info__item">
            <label>Ödeme Yöntemi</label>
            <span>Bakiye</span>
          </div>
        </div>
        <div class="checkout__totals-wrapper">
          <div class="checkout__totals">
            <h3>Sipariş Detayları</h3>
            <table class="checkout-cart-items">
              <thead>
                <tr>
                  <th>ÜRÜN</th>
                  <th style="text-align: left;">ADET</th>
                  @canSeePrices <th>FİYAT</th> @endif
                  @canSeePrices <th>TOPLAM</th> @endif
                </tr>
              </thead>
              <tbody>
                @foreach($order->cartItems as $item)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <img src="{{ explode(',', $item->product->images)[0] }}" alt="{{ $item->product->title }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;" class="me-3">
                      <div>
                        <strong>{{ $item->product->title }}</strong>
                        @if($item->parsed_notes && isset($item->parsed_notes['customizations']))
                          <br><small class="text-muted">
                            <ul style="overflow: scroll;" class="shopping-cart__product-item__options">
                              @if($item->page_count > 1)
                              @if($item->page_count)
                              <li>
                                  <small class="text-primary fw-medium">Yaprak Adeti:</small>
                                  <small class="text-muted">{{ $item->page_count }} yaprak ({{ $item->page_count * 2 }} sayfa)</small>
                              </li>
                              @endif
                              @endif
                              @if($item->barcode)
                              <li>
                                  <small class="text-primary fw-medium">Barkod:</small>
                                  <small class="text-muted">{{ $item->barcode }}</small>
                              </li>
                              @endif

                              
                              @php
                                  $totalCustomizationPrice = 0;
                                  $customizations = $item->parsed_notes['customizations'];
                              @endphp
                              @foreach($customizations as $categoryId => $customization)
                                  @php
                                      $category = App\Models\CustomizationCategory::find($categoryId);
                                  @endphp
                              
                                  @if($category)
                                      <li>
                                          <x-customization-display :customization="$customization" :category="$category" />
                                      </li>
                                  @endif
                                  
                                  @php
                                      // Özelleştirme fiyatını hesapla
                                      if($customization['type'] == 'radio' || $customization['type'] == 'hidden' || $customization['type'] == 'select') {
                                          if(isset($customization['value']) && $customization['value']) {
                                              $pivotParam = App\Models\CustomizationPivotParam::find($customization['value']);
                                              if($pivotParam && $pivotParam->price) {
                                                  $totalCustomizationPrice += $pivotParam->price;
                                              }
                                          }
                                      } elseif($customization['type'] == 'checkbox') {
                                          if(isset($customization['values']) && is_array($customization['values'])) {
                                              foreach($customization['values'] as $pivotId) {
                                                  $pivotParam = App\Models\CustomizationPivotParam::find($pivotId);
                                                  if($pivotParam && $pivotParam->price) {
                                                      $totalCustomizationPrice += $pivotParam->price;
                                                  }
                                              }
                                          }
                                      }
                                  @endphp
                              @endforeach
                             
                       
                                           <!-- Acil üretim bilgisi -->
                                           @if($item->urgent_status == 1)
                                           <li>
                                               <small class="text-success-2 fw-bold">🚨 Acil Üretim @canSeePrices +{{ number_format($item->product->urgent_price, 2) }} ₺ @endcanSeePrices</small>
                                           </li>
                                           @endif
                                           @php $itemNotes = $item->notes ? json_decode($item->notes, true) : []; @endphp
                                           @if(($itemNotes['design_service'] ?? null) === 'with_design')
                                           <li>
                                               <small class="text-success-2 fw-bold">✏️ Tasarımı Bize Yaptır @canSeePrices +{{ number_format($item->product->design_service_price ?? 0, 2) }} ₺ @endcanSeePrices</small>
                                           </li>
                                           @elseif(($itemNotes['design_service'] ?? null) === 'self_design')
                                           <li>
                                               <small class="text-muted">✏️ Tasarımı kendim yapacağım</small>
                                           </li>
                                           @endif
                             
                          </ul>
                          </small>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td>{{ $item->quantity }} Adet</td>
                  @canSeePrices <td><x-price-display :item="$item" :showQuantity="false" :showDiscountBadge="true" /></td> @endif
                  @canSeePrices <td><strong><x-price-display :item="$item" :showQuantity="true" :showDiscountBadge="true" /></strong></td> @endif
                </tr>
                @endforeach
              </tbody>
            </table>
    
          </div>
        </div>
        
        <!-- Teslimat Bilgileri -->
        <div class="mt-4">
          <h4>Teslimat Bilgileri</h4>
          <div class="card">
            <div class="card-body">
              <p><strong>Ad Soyad:</strong> {{ $order->customer_name }} {{ $order->customer_surname }}</p>
              <p><strong>Telefon:</strong> {{ $order->customer_phone }}</p>
              <p><strong>İl/İlçe:</strong> {{ $order->city ?? 'Belirtilmemiş' }} / {{ $order->district ?? 'Belirtilmemiş' }}</p>
              <p><strong>Adres:</strong> {{ $order->shipping_address }}</p>
            </div>
          </div>
        </div>
        
        <!-- Sipariş Durumu -->
        <div class="mt-4">
          <h4>Sipariş Durumu</h4>
          <div class="card">
            <div class="card-body">
              <span class="  fs-6">
                {{ $order->status_text }}
              </span>
              <p class="mt-2 mb-0">
                <small class="text-muted">
                  Siparişinizin durumunu takip etmek için 
                  <a href="{{ route('profile.orders') }}" class="text-primary">Siparişlerim</a> 
                  sayfasını ziyaret edebilirsiniz.
                </small>
              </p>
            </div>
          </div>
        </div>
        
        <!-- Sipariş Notları -->
        @if($order->notes)
        <div class="mt-4">
          <h4>Sipariş Notları</h4>
          <div class="card">
            <div class="card-body">
              <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                {{ $order->notes }}
              </div>
            </div>
          </div>
        </div>
        @endif
      </div>
    </section>
  </main>

@endsection 