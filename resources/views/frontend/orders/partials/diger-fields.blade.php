{{-- Diğer wizard tab'ında her sipariş için sabit hardcoded alanlar.
     Customization sistemine ait değil — admin yapılandırmadan tüm ürünlerde görünür.
     Dinamik fiyatlar (urgent_price, design_service_price) products tablosundan okunur. --}}

{{-- Tasarım Hizmeti — radio: Tasarımı bize yaptır (+design_service_price) / Tasarımı kendin yap.
     Ek ücret tüm kullanıcılara gösterilir; "ek ücret olduğu" satın alma kararını etkileyen kritik bilgi. --}}
<div class="wizard-step-section">
    <h5 class="wizard-section-title">Tasarım Hizmeti</h5>
    <p class="wizard-step-desc">Albüm tasarımını bizim yapmamızı mı, yoksa kendinizin yapmasını mı istiyorsunuz?</p>
    <div class="customization-section" data-category="design_service" data-type="radio" data-required="1">
        <div class="row g-3 option-card-grid">
            @php
                $designOptions = [
                    [
                        'id' => 'design_service_with',
                        'value' => 'with_design',
                        'title' => 'Tasarımı bize yaptır',
                        'price' => (float)($product->design_service_price ?? 0),
                        'icon' => 'fa-pen-fancy',
                        'hint' => null,
                    ],
                    [
                        'id' => 'design_service_self',
                        'value' => 'self_design',
                        'title' => 'Tasarımı kendin yap',
                        'price' => 0,
                        'icon' => 'fa-user-edit',
                        'hint' => 'Hazır tasarım yükleyebilirsin',
                    ],
                ];
            @endphp
            @foreach($designOptions as $opt)
                <div class="col-6 col-md-6 option-card-wrapper" data-parent-pivot-id="0">
                    <label class="option-card" for="{{ $opt['id'] }}">
                        <input class="option-card-input design-service-radio"
                               type="radio"
                               name="design_service"
                               value="{{ $opt['value'] }}"
                               id="{{ $opt['id'] }}"
                               data-price="{{ $opt['price'] }}"
                               data-title="{{ $opt['title'] }}">
                        <div class="option-card-image-wrap">
                            <div class="option-card-no-image">
                                <i class="fas {{ $opt['icon'] }}"></i>
                            </div>
                            <span class="option-card-checkmark"><i class="fas fa-check"></i></span>
                        </div>
                        <div class="option-card-body">
                            <div class="option-card-title">{{ $opt['title'] }}</div>
                            @if($opt['price'] > 0)
                                <div class="option-card-price">+{{ number_format($opt['price'], 2) }} ₺</div>
                            @elseif($opt['hint'])
                                <small class="option-card-hint d-block">{{ $opt['hint'] }}</small>
                            @endif
                        </div>
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Acil Üretim — sadece ürünün urgent_price'ı set edilmişse render edilir. --}}
@if($product->urgent_price)
    <div class="wizard-step-section">
        <div class="form-check diger-toggle-card">
            <input class="form-check-input" type="checkbox" name="urgent_production" id="urgent_production" value="1">
            <label class="form-check-label" for="urgent_production">
                <strong>🚨 Acil Üretim</strong>
                @canSeePrices
                    <span class="text-success-2 fw-bold">+ {{ number_format($product->urgent_price, 2) }} ₺</span>
                @endcanSeePrices
                <small class="text-muted d-block">Ek ücret karşılığında ürününüz acil olarak üretilecektir.</small>
            </label>
        </div>
    </div>
@endif

{{-- Sipariş Notu — opsiyonel free-text textarea. --}}
<div class="wizard-step-section">
    <h5 class="wizard-section-title">Sipariş Notu</h5>
    <textarea class="form-control"
              id="order_note"
              name="order_note"
              rows="2"
              placeholder="Siparişiniz ile ilgili özel notlarınızı buraya yazabilirsiniz... (opsiyonel)"
              style="resize: vertical;"></textarea>
</div>
