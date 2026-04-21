@extends('frontend.master')

@section('content')

<main>
    <div class="mb-4 pb-4"></div>
    @if(session('error'))
        <div class="container">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    <section class="shop-checkout container">
      <h2 @if(count($cartItems) == 0) style="display: none;" @endif class="page-title">Sepetim</h2>
      <div class="checkout-steps" @if(count($cartItems) == 0) style="display: none;" @endif>
        <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">01</span>
          <span class="checkout-steps__item-title">
            <span>SEPETİM</span>
            <em>Ürünlerinizi Yönetin</em>
          </span>
        </a>
        <a href="{{ route('cart.checkout') }}" class="checkout-steps__item">
          <span class="checkout-steps__item-number">02</span>
          <span class="checkout-steps__item-title">
            <span>TESLİMAT VE ÖDEME</span>
            <em>Siparişinizi Tamamlayın</em>
          </span>
        </a>
        <a href="#" class="checkout-steps__item">
          <span class="checkout-steps__item-number">03</span>
          <span class="checkout-steps__item-title">
            <span>ONAY</span>
            <em>Siparişinizi Gözden Geçirin ve Gönderin</em>
          </span>
        </a>
      </div>
      <div class="shopping-cart">
        @if(count($cartItems) > 0)
        <div class="cart-table__wrapper">
          <table class="cart-table">
            <thead>
              <tr>
                <th>Ürün</th>
                <th></th>
                @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1)) <th>Fiyat</th> @endif
                <th style="text-align: center;"></th>
                @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1)) <th>Toplam Fiyat</th> @endif
                <th></th>
              </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $item)
              <tr>
                <td>
                  <div class="shopping-cart__product-item">
                    @if($item->product->images)
                                            <a href="{{ route('products.show', $item->product->slug) }}">
                        <img loading="lazy" src="{{explode(',', $item->product->images)[0] }}" width="120" height="120" alt="">
                      </a>
                    @endif
                  </div>
                </td>
                <td>
                  <div class="shopping-cart__product-item__detail">
                    <h4><a href="{{ route('products.show', $item->product->slug) }}">{{ $item->product->title }}</a></h4>
                    @if($item->notes)
                    @php
                        $notes = json_decode($item->notes, true);
                        $customizations = $notes['customizations'] ?? [];
                        $urgentProduction = $notes['urgent_production'] ?? false;
                        $urgentPrice = $notes['urgent_price'] ?? 0;
                    @endphp
                    @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1))
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
                        
                        @if($item->thumbnail_urls && count($item->thumbnail_urls) > 0)
                        <li>
                            <small class="text-primary fw-medium">Önizleme:</small>
                            <div class="d-flex gap-2 mt-1">
                                @foreach($item->thumbnail_urls as $thumbnailUrl)
                                    <img src="storage/{{ $thumbnailUrl }}" alt="Önizleme" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                @endforeach
                            </div>
                        </li>
                        @endif
                        
                        @if($customizations)
                        @foreach($customizations as $categoryId => $customization)
                            @php
                                $category = App\Models\CustomizationCategory::find($categoryId);
                            @endphp
                        
                            @if($category)
                                <li>
                                    <x-customization-display :customization="$customization" :category="$category" />
                                </li>
                            @endif
                        @endforeach
                        @endif
                        
                        <!-- Acil üretim bilgisi -->
                        @if($item->urgent_status == 1)
                        <li>
                            <small class="text-success-2 fw-bold">🚨 Acil Üretim @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1)) +{{ number_format($item->product->urgent_price, 2) }} ₺ @endif</small>
                        </li>
                    @endif
                    </ul>
                    @endif
                    @endif
                  </div>
                </td>
                @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1)) <td>
                  <x-price-display :item="$item" :showQuantity="false" :showDiscountBadge="true" />
                </td>
                @endif
                <td>
                  <div class="qty-control position-relative">
                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="qty-control__number text-center" data-cart-item-id="{{ $item->id }}">
                    <div class="qty-control__reduce">-</div>
                    <div class="qty-control__increase">+</div>
                  </div><!-- .qty-control -->
                </td>
                @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1)) <td>
                  <x-price-display :item="$item" :showQuantity="true" :showDiscountBadge="true" />
                </td>
                @endif
                <td>
                  <a href="#" class="remove-cart remove-item" data-cart-item-id="{{ $item->id }}">
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="#767676" xmlns="http://www.w3.org/2000/svg">
                      <path d="M0.259435 8.85506L9.11449 0L10 0.885506L1.14494 9.74056L0.259435 8.85506Z"/>
                      <path d="M0.885506 0.0889838L9.74057 8.94404L8.85506 9.82955L0 0.97449L0.885506 0.0889838Z"/>
                    </svg>                  
                  </a>
                </td>
              </tr>
              @endforeach
        
            </tbody>
          </table>
        
        </div>
        <div class="shopping-cart__totals-wrapper">
          <div class="sticky-content">
            <div class="shopping-cart__totals">
              <h3>Sepet Özeti</h3>
              @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1))
              
              <table class="cart-totals">
                <tbody>
                 
                  <tr class="border-top">
                    <th><strong>Genel Toplam</strong></th>
                    <td><strong>{{ number_format($finalTotal, 2) }} ₺</strong></td>
                  </tr>
                </tbody>
              </table>
              @else
              <span>Fiyat detaylarını görüntülemek için yetkiniz yok.</span><br><br>
              @endif
            </div>
            <div class="mobile_fixed-btn_wrapper">
              <div class="button-wrapper container">
                <a style="width: 100%;" href="{{ route('cart.checkout') }}" class="btn btn-primary"><i class="fas fa-check"></i> SİPARİŞİ TAMAMLA</a>
                
              </div>
            </div>
          </div>
        </div>
        @else
        <div class="empty-cart" style="padding-top: 50px; width: 100%;">
          <div class="empty-cart__content text-center">
            <div class="empty-cart__icon mb-4">
              <svg width="80" height="80" viewBox="0 0 80 80" fill="#ddd" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 10h40l-5 15H25L20 10z"/>
                <path d="M15 30h50v30H15z"/>
                <circle cx="25" cy="45" r="3"/>
                <circle cx="35" cy="45" r="3"/>
                <circle cx="45" cy="45" r="3"/>
              </svg>
            </div>
            <h3 class="empty-cart__title mb-3">Sepetiniz Boş</h3>
            <p class="empty-cart__description mb-4 text-muted">Sepetinizde henüz ürün bulunmuyor. Alışverişe başlamak için aşağıdaki butona tıklayabilirsiniz.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
              <i class="fas fa-shopping-cart"></i> Alışverişe Başla
            </a>
          </div>
        </div>
        @endif
      </div>
    </section>
    <br>
  </main>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if jQuery is available
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    var $ = jQuery;

    // Adet değişikliği
    $('.qty-control__increase').click(function() {
        var $input = $(this).siblings('.qty-control__number');
        var currentQty = parseInt($input.val());
        var cartItemId = $(this).closest('tr').find('.remove-item').data('cart-item-id');
        
        updateQuantity(cartItemId, currentQty + 1);
    });
    
    $('.qty-control__reduce').click(function() {
        var $input = $(this).siblings('.qty-control__number');
        var currentQty = parseInt($input.val());
        var cartItemId = $(this).closest('tr').find('.remove-item').data('cart-item-id');
        
        if (currentQty > 1) {
            updateQuantity(cartItemId, currentQty - 1);
        }
    });
    
    function updateQuantity(cartItemId, newQuantity) {
        $.ajax({
            url: '{{ route("cart.quantity", ":id") }}'.replace(':id', cartItemId),
            method: 'POST',
            data: {
                quantity: newQuantity,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Input değerini güncelle
                    $('input[data-cart-item-id="' + cartItemId + '"]').val(response.quantity);
                    
                    // Toplam fiyatı güncelle
                    $('tr').each(function() {
                        if ($(this).find('.remove-item').data('cart-item-id') == cartItemId) {
                            $(this).find('.shopping-cart__subtotal').text(response.total_price + ' ₺');
                        }
                    });
                    
                    // Sayfayı yenile
                    location.reload();
                }
            },
            error: function() {
                alert('Bir hata oluştu!');
            }
        });
    }

    // Ürün kaldırma
    $('.remove-item').click(function() {
        var cartItemId = $(this).data('cart-item-id');
        var row = $(this).closest('tr');
        
        if (confirm('Bu ürünü sepetten kaldırmak istediğinizden emin misiniz?')) {
            $.ajax({
                url: '/cart/remove/' + cartItemId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        row.fadeOut(function() {
                            $(this).remove();
                            
                            // Sepet özetini güncelle
                            if (response.cart_summary) {
                                updateCartSummary(response.cart_summary);
                            }
                            
                            // Eğer sepet boşsa sayfayı yenile
                            if (response.cart_summary.item_count === 0) {
                                location.reload();
                            }
                        });
                    }
                },
                error: function() {
                    alert('Bir hata oluştu!');
                }
            });
        }
    });

    // Sepeti temizleme
    $('#clear-cart').click(function() {
        if (confirm('Sepeti tamamen temizlemek istediğinizden emin misiniz?')) {
            $.ajax({
                url: '/cart/clear',
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function() {
                    alert('Bir hata oluştu!');
                }
            });
        }
    });

    // Siparişi tamamla
    $('#checkout-btn').click(function() {
        window.location.href = '/checkout';
    });

    // Sepet özetini güncelle (backend'den gelen verilerle)
    function updateCartSummary(summary) {
        // Genel toplam güncelle
        $('.cart-totals tbody tr.border-top td strong').text(summary.final_total + ' ₺');
        
        // Başlığı güncelle (opsiyonel)
        if (summary.item_count === 0) {
            $('.page-title').text('Sepetiniz Boş');
        }
        
        console.log('Cart summary updated:', summary);
    }

    // Extra Sales Modal
    function showExtraSalesModal(extraSales) {
        if (extraSales && extraSales.length > 0) {
            var modalHtml = `
                <div class="modal fade" id="extraSalesModal" tabindex="-1" aria-labelledby="extraSalesModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="extraSalesModalLabel">Önerilen Ürünler</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted mb-4">Bu ürünlerle birlikte almanızı öneririz:</p>
                                <div class="row">
            `;
            
            extraSales.forEach(function(product) {
                modalHtml += `
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <img src="${product.images ? product.images.split(',')[0] : ''}" alt="${product.title}" class="img-fluid mb-3" style="max-height: 150px; object-fit: cover;">
                                <h6 class="card-title">${product.title}</h6>
                                <p class="card-text text-primary fw-bold">${parseFloat(product.price).toFixed(2)} ₺</p>
                                <a href="/products/order/${product.id}" class="btn btn-primary btn-sm">
                                    Sepete Ekle
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            modalHtml += `
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Devam Et</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Eski modal'ı kaldır
            $('#extraSalesModal').remove();
            
            // Yeni modal'ı ekle
            $('body').append(modalHtml);
            
            // Modal'ı göster
            $('#extraSalesModal').modal('show');
        }
    }
});
</script>
@endsection 