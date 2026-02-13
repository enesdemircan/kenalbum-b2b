<div class="modal fade" id="extraSalesModal" tabindex="-1" aria-labelledby="extraSalesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="extraSalesModalLabel">Sepete Ekleme Başarılı!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-check-circle text-success-2" style="font-size: 3rem;"></i>
                    <h4 class="text-success-2 mt-2">Ürün Sepete Eklendi!</h4>
                    <p class="text-muted">Aşağıdaki ürünlerle birlikte almanızı öneririz:</p>
                </div>
                <div class="row">
                    @foreach($extraSales as $product)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    @if($product->images)
                                        <img src="{{ explode(',', $product->images)[0] }}" alt="{{ $product->title }}" class="img-fluid mb-3" style="max-height: 150px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center mb-3" style="height: 150px;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    <h6 class="card-title">{{ $product->title }}</h6>
                                    <p class="card-text text-primary fw-bold">{{ number_format($product->price, 2) }} ₺</p>
                                    <a href="{{ route('products.ordercreate', $product->id) }}" class="btn btn-primary btn-sm">
                                        Sepete Ekle
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('cart.index') }}" class="btn btn-success-2">Sepete Git</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Alışverişe Devam Et</button>
            </div>
        </div>
    </div>
</div> 