{{-- Ekstra ürün modal'ı için flat customization form'u.
     Ana wizard'a kıyasla step yok, tüm customization'lar üst üste.
     Submit /cart/add'e POST eder — ana wizard ile aynı endpoint, customization
     verisi notes'a json olarak yazılır. --}}
<form id="extraCustomizeForm"
      data-extra-product-id="{{ $product->id }}"
      data-extra-product-title="{{ $product->title }}">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="quantity" value="1">
    <input type="hidden" name="page_count" value="1">
    <input type="hidden" name="price" value="{{ (float) $product->price }}">

    <div class="extra-form-product-summary mb-3">
        <strong>{{ $product->title }}</strong>
        <span class="text-success fw-bold ms-2">{{ number_format($product->price, 2) }} ₺</span>
    </div>

    @if(empty($categories))
        <p class="text-muted">Bu ekstra ürün için özelleştirme bulunmuyor.</p>
    @else
        @foreach($categories as $cat)
            <div class="extra-form-section mb-3">
                <h6 class="mb-1">{{ $cat['category']->title }}</h6>
                @if(!empty($cat['category']->description))
                    <p class="text-muted small mb-2">{{ $cat['category']->description }}</p>
                @endif
                @include('frontend.products.customization-section', [
                    'category' => $cat['category'],
                    'categoryParams' => $cat['params'],
                    'product' => $product,
                ])
            </div>
        @endforeach
    @endif

    <div class="d-grid mt-3">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-cart-plus"></i> Sepete Ekle
        </button>
    </div>
</form>
