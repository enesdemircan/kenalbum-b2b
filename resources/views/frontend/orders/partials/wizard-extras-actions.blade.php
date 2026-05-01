{{-- Ekstralar tab — son aşama. Ana ürün Sipariş Özeti adımında zaten sepete
     eklendi; +/- ile ekstralar canlı sepete giriyor, kullanıcı sepete gidip
     bitirebilir veya doğrudan siparişi tamamlayabilir. --}}
<div class="d-grid mt-3">
    <a href="{{ route('cart.index') }}" class="btn btn-primary btn-lg" id="extrasGoToCartBtn">
        <i class="fas fa-shopping-cart"></i> SEPETE GİT
    </a>
</div>
<div class="d-grid mt-2">
    <button type="button" class="btn btn-success btn-lg" id="extrasFinishOrderBtn">
        <i class="fas fa-bolt"></i> SİPARİŞİ TAMAMLA
    </button>
    <small class="text-muted text-center mt-1">
        Sepetteki diğer ürünler silinir ve doğrudan teslimat adımına ilerler
    </small>
</div>
