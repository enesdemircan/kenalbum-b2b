{{-- Ekstralar tab — son aşama. Ana ürün Sipariş Özeti adımında zaten sepete
     eklendi; burada sadece sepete git veya doğrudan ödemeye geç. --}}
<div class="d-grid mt-3">
    <a href="{{ route('cart.index') }}" class="btn btn-primary btn-lg" id="extrasGoToCartBtn">
        <i class="fas fa-shopping-cart"></i> SEPETE GİT
    </a>
</div>
<div class="d-grid mt-2">
    <a href="{{ route('cart.checkout') }}" class="btn btn-success btn-lg" id="extrasCheckoutBtn">
        <i class="fas fa-bolt"></i> DOĞRUDAN ÖDEMEYE GİT
    </a>
</div>
