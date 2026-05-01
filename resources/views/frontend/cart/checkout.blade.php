@extends('frontend.master')

@section('content')

@php
  $totalProducts = 0; // ürünler toplamı (indirimli)
  $totalUrgent = 0;   // acil üretim toplamı
  foreach ($cartItems as $item) {
      $totalProducts += $item->price * $item->quantity;
      if ($item->notes) {
          $n = json_decode($item->notes, true);
          if (isset($n['urgent_price'])) { $totalUrgent += $n['urgent_price']; }
      }
  }
  $userBalance = optional(optional(auth()->user())->customer)->balance ?? 0;
@endphp

<main>
  <div class="mb-4 pb-4"></div>
  <section class="checkout-multi container">
    <h2 class="checkout-multi__title">Siparişi Tamamla</h2>

    <div class="checkout-multi__steps" id="checkoutStepsBar">
      <a href="{{ route('cart.index') }}" class="cm-step is-done" style="text-decoration:none;"><span class="cm-step__num">1</span><span class="cm-step__label">Sepetim</span></a>
      <div class="cm-step is-active" data-step="1"><span class="cm-step__num">2</span><span class="cm-step__label">Teslimat</span></div>
      <div class="cm-step" data-step="2"><span class="cm-step__num">3</span><span class="cm-step__label">Fatura</span></div>
      <div class="cm-step" data-step="3"><span class="cm-step__num">4</span><span class="cm-step__label">Kargo</span></div>
      <div class="cm-step" data-step="4"><span class="cm-step__num">5</span><span class="cm-step__label">Dosya</span></div>
      <div class="cm-step" data-step="5"><span class="cm-step__num">6</span><span class="cm-step__label">Onay</span></div>
    </div>

    <form action="{{ route('cart.complete') }}" method="POST" id="checkoutForm" class="row g-4">
      @csrf

      <div class="col-lg-8">
        {{-- ============ STEP 1: TESLİMAT ADRESİ ============ --}}
        <section class="cm-pane is-active" data-pane="1">
          <div class="cm-card">
            <div class="cm-card__head">
              <h3 class="cm-card__title">Teslimat Adresi</h3>
              <p class="cm-card__sub">Siparişin nereye gideceğini seçin. Müşterinize göndermek için "Müşteri Adresleri" sekmesini kullanın.</p>
            </div>

            <ul class="nav nav-tabs cm-tabs" role="tablist">
              <li class="nav-item">
                <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#shipCompany" role="tab">
                  <i class="fas fa-building"></i> Şirket Adresleri (Bana Gelsin)
                  <span class="cm-tabs__count">{{ $companyAddresses->count() }}</span>
                </button>
              </li>
              <li class="nav-item">
                <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#shipCustomer" role="tab">
                  <i class="fas fa-users"></i> Müşteri Adresleri (Müşterime Gitsin)
                  <span class="cm-tabs__count">{{ $customerAddresses->count() }}</span>
                </button>
              </li>
            </ul>

            <div class="tab-content cm-tabcontent">
              <div class="tab-pane fade show active" id="shipCompany" role="tabpanel">
                @if($companyAddresses->isEmpty())
                  <div class="cm-empty">
                    <i class="fas fa-building"></i>
                    <p>Henüz şirket adresi yok.</p>
                    <button type="button" class="btn btn-orange-outline" data-cm-add-address="company">
                      <i class="fas fa-plus"></i> Şirket Adresi Ekle
                    </button>
                  </div>
                @else
                  <div class="cm-address-grid">
                    @foreach($companyAddresses as $i => $addr)
                      <label class="cm-address-card">
                        <input type="radio" name="ship_addr" value="{{ $addr->id }}" {{ $i === 0 ? 'checked' : '' }}
                          data-ad="{{ $addr->ad }}" data-soyad="{{ $addr->soyad }}"
                          data-telefon="{{ $addr->telefon }}" data-city="{{ $addr->city }}"
                          data-district="{{ $addr->district }}" data-adres="{{ $addr->adres }}"
                          data-title="{{ $addr->title }}">
                        <div class="cm-address-card__inner">
                          <div class="cm-address-card__head">
                            <strong>{{ $addr->title }}</strong>
                            <span class="cm-badge cm-badge--company"><i class="fas fa-building"></i> Şirket</span>
                          </div>
                          <p class="cm-address-card__name">{{ $addr->ad }} {{ $addr->soyad }}</p>
                          <p class="cm-address-card__line">{{ $addr->city }} / {{ $addr->district }}</p>
                          <p class="cm-address-card__line">{{ $addr->adres }}</p>
                          <p class="cm-address-card__phone"><i class="fas fa-phone"></i> {{ $addr->telefon }}</p>
                        </div>
                      </label>
                    @endforeach
                  </div>
                  <button type="button" class="btn btn-orange-outline mt-3" data-cm-add-address="company">
                    <i class="fas fa-plus"></i> Yeni Şirket Adresi Ekle
                  </button>
                @endif
              </div>

              <div class="tab-pane fade" id="shipCustomer" role="tabpanel">
                @if($customerAddresses->isEmpty())
                  <div class="cm-empty">
                    <i class="fas fa-users"></i>
                    <p>Henüz müşteri adresi yok. Müşterinize göndermek için bir müşteri adresi ekleyin.</p>
                    <button type="button" class="btn btn-orange-outline" data-cm-add-address="customer">
                      <i class="fas fa-plus"></i> Müşteri Adresi Ekle
                    </button>
                  </div>
                @else
                  <div class="cm-address-grid">
                    @foreach($customerAddresses as $addr)
                      <label class="cm-address-card">
                        <input type="radio" name="ship_addr" value="{{ $addr->id }}"
                          data-ad="{{ $addr->ad }}" data-soyad="{{ $addr->soyad }}"
                          data-telefon="{{ $addr->telefon }}" data-city="{{ $addr->city }}"
                          data-district="{{ $addr->district }}" data-adres="{{ $addr->adres }}"
                          data-title="{{ $addr->title }}">
                        <div class="cm-address-card__inner">
                          <div class="cm-address-card__head">
                            <strong>{{ $addr->title }}</strong>
                            <span class="cm-badge cm-badge--customer"><i class="fas fa-users"></i> Müşteri</span>
                          </div>
                          <p class="cm-address-card__name">{{ $addr->ad }} {{ $addr->soyad }}</p>
                          <p class="cm-address-card__line">{{ $addr->city }} / {{ $addr->district }}</p>
                          <p class="cm-address-card__line">{{ $addr->adres }}</p>
                          <p class="cm-address-card__phone"><i class="fas fa-phone"></i> {{ $addr->telefon }}</p>
                        </div>
                      </label>
                    @endforeach
                  </div>
                  <button type="button" class="btn btn-orange-outline mt-3" data-cm-add-address="customer">
                    <i class="fas fa-plus"></i> Yeni Müşteri Adresi Ekle
                  </button>
                @endif
              </div>
            </div>

            {{-- Hidden inputs — radio'dan beslenir, complete() validate eder --}}
            <input type="hidden" name="customer_name" id="customer_name">
            <input type="hidden" name="customer_surname" id="customer_surname">
            <input type="hidden" name="customer_phone" id="customer_phone">
            <input type="hidden" name="city" id="city">
            <input type="hidden" name="district" id="district">
            <input type="hidden" name="shipping_address" id="shipping_address">
          </div>

          <div class="cm-actions">
            <a href="{{ route('cart.index') }}" class="cm-btn cm-btn--ghost"><i class="fas fa-arrow-left"></i> Sepete Dön</a>
            <button type="button" class="cm-btn cm-btn--primary" data-cm-next>İleri <i class="fas fa-arrow-right"></i></button>
          </div>
        </section>

        {{-- ============ STEP 2: FATURA ADRESİ ============ --}}
        <section class="cm-pane" data-pane="2">
          <div class="cm-card">
            <div class="cm-card__head">
              <h3 class="cm-card__title">Fatura Adresi</h3>
              <p class="cm-card__sub">Fatura adresi teslimat adresinizden farklıysa düzenleyebilirsiniz.</p>
            </div>

            <input type="hidden" name="billing_same_as_shipping" value="0">
            <label class="cm-checkbox cm-checkbox--lg">
              <input type="checkbox" name="billing_same_as_shipping" value="1" id="billingSameToggle" checked>
              <span class="cm-checkbox__box"><i class="fas fa-check"></i></span>
              <span class="cm-checkbox__label">
                <strong>Fatura adresim teslimat adresimle aynı</strong>
                <em>Teslimat adresine fatura kesilecektir.</em>
              </span>
            </label>

            <div class="cm-billing-fields mt-4" id="billingFields" hidden>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Ad *</label>
                  <input type="text" class="form-control" name="billing_name" id="billing_name">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Soyad *</label>
                  <input type="text" class="form-control" name="billing_surname" id="billing_surname">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Telefon *</label>
                  <input type="tel" class="form-control" name="billing_phone" id="billing_phone">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Firma Adı (opsiyonel)</label>
                  <input type="text" class="form-control" name="billing_company" id="billing_company">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Vergi No / TC (opsiyonel)</label>
                  <input type="text" class="form-control" name="billing_tax_no" id="billing_tax_no">
                </div>
                <div class="col-md-6">
                  <label class="form-label">İl *</label>
                  <select class="form-select" name="billing_city" id="billing_city">
                    <option value="">İl Seçin</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">İlçe *</label>
                  <select class="form-select" name="billing_district" id="billing_district">
                    <option value="">İlçe Seçin</option>
                  </select>
                </div>
                <div class="col-md-12">
                  <label class="form-label">Açık Adres *</label>
                  <textarea class="form-control" name="billing_address" id="billing_address" rows="3"></textarea>
                </div>
              </div>
            </div>
          </div>

          <div class="cm-actions">
            <button type="button" class="cm-btn cm-btn--ghost" data-cm-prev><i class="fas fa-arrow-left"></i> Geri</button>
            <button type="button" class="cm-btn cm-btn--primary" data-cm-next>İleri <i class="fas fa-arrow-right"></i></button>
          </div>
        </section>

        {{-- ============ STEP 3: KARGO ============ --}}
        <section class="cm-pane" data-pane="3">
          <div class="cm-card">
            <div class="cm-card__head">
              <h3 class="cm-card__title">Kargo Yöntemi</h3>
              <p class="cm-card__sub">Siparişinizin nasıl ulaşmasını istediğinizi seçin.</p>
            </div>

            @if($shippingMethods->isEmpty())
              <div class="cm-empty">
                <i class="fas fa-truck"></i>
                <p>Şu anda aktif kargo yöntemi tanımlı değil. Lütfen yöneticiyle iletişime geçin.</p>
              </div>
            @else
              <div class="cm-shipping-list">
                @foreach($shippingMethods as $i => $method)
                  <label class="cm-shipping-card">
                    <input type="radio" name="shipping_method_id" value="{{ $method->id }}"
                      data-price="{{ (float) $method->price }}"
                      data-title="{{ $method->title }}"
                      {{ $i === 0 ? 'checked' : '' }}>
                    <div class="cm-shipping-card__inner">
                      <div class="cm-shipping-card__icon"><i class="fas fa-truck"></i></div>
                      <div class="cm-shipping-card__body">
                        <strong>{{ $method->title }}</strong>
                        @if($method->description)
                          <span>{{ $method->description }}</span>
                        @endif
                      </div>
                      <div class="cm-shipping-card__price">
                        @if($method->price > 0)
                          ₺{{ number_format($method->price, 2, ',', '.') }}
                        @else
                          <span class="cm-shipping-card__free">ÜCRETSİZ</span>
                        @endif
                      </div>
                    </div>
                  </label>
                @endforeach
              </div>
            @endif
          </div>

          <div class="cm-actions">
            <button type="button" class="cm-btn cm-btn--ghost" data-cm-prev><i class="fas fa-arrow-left"></i> Geri</button>
            <button type="button" class="cm-btn cm-btn--primary" data-cm-next>İleri <i class="fas fa-arrow-right"></i></button>
          </div>
        </section>

        {{-- ============ STEP 4: SİPARİŞ DOSYASI ============ --}}
        <section class="cm-pane" data-pane="4">
          <div class="cm-card">
            <div class="cm-card__head">
              <h3 class="cm-card__title">Sipariş Dosyası</h3>
              <p class="cm-card__sub">Tüm sepetinizdeki ürünlere ait görselleri tek bir ZIP dosyasında yükleyin. Maksimum 500 MB.</p>
            </div>

            <input type="file" id="orderUploadFile" class="form-control" accept=".zip,.rar,.7z">
            <div id="orderUploadStatus" class="d-none mt-3">
              <div class="d-flex align-items-center mb-2">
                <strong id="orderUploadFilename" class="me-2"></strong>
                <span id="orderUploadSize" class="text-muted small"></span>
                <span id="orderUploadDoneBadge" class="badge bg-success ms-auto d-none">✓ Yüklendi</span>
              </div>
              <div class="progress" style="height:8px;">
                <div id="orderUploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%"></div>
              </div>
              <small id="orderUploadProgressText" class="text-muted d-block mt-1">0%</small>
            </div>
            <small class="text-muted d-block mt-2">
              <i class="fas fa-folder-open"></i> İpucu: ZIP içine sepetteki her ürün için ayrı klasör koymanız işimizi kolaylaştırır.
            </small>
          </div>

          <div class="cm-actions">
            <button type="button" class="cm-btn cm-btn--ghost" data-cm-prev><i class="fas fa-arrow-left"></i> Geri</button>
            <button type="button" class="cm-btn cm-btn--primary" data-cm-next id="cmStep4Next" disabled>
              <span id="cmStep4Hint">Önce dosyanızı yükleyin</span>
            </button>
          </div>
        </section>

        {{-- ============ STEP 5: ONAY ============ --}}
        <section class="cm-pane" data-pane="5">
          <div class="cm-card">
            <div class="cm-card__head">
              <h3 class="cm-card__title">Sipariş Onayı</h3>
              <p class="cm-card__sub">Siparişinizi göndermeden önce bilgileri kontrol edin.</p>
            </div>

            <div class="cm-review-grid">
              <div class="cm-review">
                <div class="cm-review__head">
                  <strong>Teslimat Adresi</strong>
                  <button type="button" class="cm-review__edit" data-cm-go="1">Düzenle</button>
                </div>
                <div class="cm-review__body" id="reviewShipping">—</div>
              </div>

              <div class="cm-review">
                <div class="cm-review__head">
                  <strong>Fatura Adresi</strong>
                  <button type="button" class="cm-review__edit" data-cm-go="2">Düzenle</button>
                </div>
                <div class="cm-review__body" id="reviewBilling">—</div>
              </div>

              <div class="cm-review">
                <div class="cm-review__head">
                  <strong>Kargo Yöntemi</strong>
                  <button type="button" class="cm-review__edit" data-cm-go="3">Düzenle</button>
                </div>
                <div class="cm-review__body" id="reviewShippingMethod">—</div>
              </div>

              <div class="cm-review">
                <div class="cm-review__head">
                  <strong>Sipariş Dosyası</strong>
                  <button type="button" class="cm-review__edit" data-cm-go="4">Düzenle</button>
                </div>
                <div class="cm-review__body" id="reviewFile">—</div>
              </div>
            </div>

            <div class="cm-payment mt-4">
              <strong>Ödeme Yöntemi</strong>
              <label class="cm-radio mt-2">
                <input type="radio" name="payment_method" value="bakiye" checked>
                <span>
                  <strong>Firma Bakiyesi</strong>
                  <em>Mevcut bakiye: <b>₺{{ number_format($userBalance, 2, ',', '.') }}</b></em>
                </span>
              </label>
            </div>
          </div>

          <div class="cm-actions">
            <button type="button" class="cm-btn cm-btn--ghost" data-cm-prev><i class="fas fa-arrow-left"></i> Geri</button>
            <button type="submit" class="cm-btn cm-btn--success" id="finalSubmitBtn">
              <i class="fas fa-check"></i> SİPARİŞİ ONAYLA
            </button>
          </div>
        </section>
      </div>

      {{-- ============ STICKY SUMMARY ============ --}}
      <aside class="col-lg-4">
        <div class="cm-summary">
          <h4 class="cm-summary__title">Sipariş Özeti</h4>

          <div class="cm-summary__items">
            @foreach($cartItems as $item)
              <div class="cm-summary__item">
                <span class="cm-summary__name">{{ $item->product->title ?? '—' }} <em>×{{ $item->quantity }}</em></span>
                <span class="cm-summary__price">₺{{ number_format($item->price * $item->quantity, 2, ',', '.') }}</span>
              </div>
            @endforeach
          </div>

          <div class="cm-summary__divider"></div>

          <div class="cm-summary__row">
            <span>Ürünler</span>
            <span>₺{{ number_format($totalProducts, 2, ',', '.') }}</span>
          </div>
          @if($totalUrgent > 0)
            <div class="cm-summary__row">
              <span>Acil Üretim</span>
              <span>₺{{ number_format($totalUrgent, 2, ',', '.') }}</span>
            </div>
          @endif
          @if($totalDiscount > 0)
            <div class="cm-summary__row cm-summary__row--success">
              <span>İndirim</span>
              <span>-₺{{ number_format($totalDiscount, 2, ',', '.') }}</span>
            </div>
          @endif
          <div class="cm-summary__row" id="summaryShipping">
            <span>Kargo</span>
            <span id="summaryShippingPrice">—</span>
          </div>

          <div class="cm-summary__divider"></div>

          <div class="cm-summary__total">
            <span>Genel Toplam</span>
            <span id="summaryTotal">₺{{ number_format($total, 2, ',', '.') }}</span>
          </div>

          <div class="cm-summary__balance">
            <i class="fas fa-wallet"></i>
            <div>
              <strong>Firma Bakiyesi</strong>
              <em>₺{{ number_format($userBalance, 2, ',', '.') }}</em>
            </div>
          </div>
        </div>
      </aside>
    </form>
  </section>
  <br>

  {{-- Yeni Adres Ekleme Modal --}}
  <div class="modal fade" id="cmAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="cmAddressModalTitle">Yeni Adres Ekle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="cmModalType" value="company">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Adres Başlığı *</label>
              <input type="text" class="form-control" id="cmModalTitle" placeholder="Ev / Ofis / Depo / Müşteri X" maxlength="255">
            </div>
            <div class="col-md-6">
              <label class="form-label">Ad *</label>
              <input type="text" class="form-control" id="cmModalAd">
            </div>
            <div class="col-md-6">
              <label class="form-label">Soyad *</label>
              <input type="text" class="form-control" id="cmModalSoyad">
            </div>
            <div class="col-md-6">
              <label class="form-label">Telefon *</label>
              <input type="tel" class="form-control" id="cmModalTelefon">
            </div>
            <div class="col-md-6">
              <label class="form-label">İl *</label>
              <select class="form-select" id="cmModalCity"><option value="">İl Seçin</option></select>
            </div>
            <div class="col-md-6">
              <label class="form-label">İlçe *</label>
              <select class="form-select" id="cmModalDistrict"><option value="">İlçe Seçin</option></select>
            </div>
            <div class="col-12">
              <label class="form-label">Açık Adres *</label>
              <textarea class="form-control" id="cmModalAdres" rows="3"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
          <button type="button" class="btn btn-orange-solid" id="cmModalSaveBtn"><i class="fas fa-save"></i> Adresi Kaydet</button>
        </div>
      </div>
    </div>
  </div>

</main>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/turkey-cities.js') }}"></script>
<script>
// ============ STEP NAVIGATION ============
const cmPanes = document.querySelectorAll('.cm-pane');
const cmSteps = document.querySelectorAll('.cm-step');
let cmCurrentStep = 1;

function cmGoTo(step) {
    if (step < 1 || step > 5) return;
    cmPanes.forEach(p => p.classList.toggle('is-active', parseInt(p.dataset.pane) === step));
    cmSteps.forEach(s => {
        const n = parseInt(s.dataset.step);
        s.classList.toggle('is-active', n === step);
        s.classList.toggle('is-done', n < step);
    });
    cmCurrentStep = step;
    if (step === 5) cmRenderReview();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function cmValidateCurrent() {
    if (cmCurrentStep === 1) {
        const sel = document.querySelector('input[name="ship_addr"]:checked');
        if (!sel) {
            Swal.fire({ icon: 'warning', title: 'Adres seçin', text: 'Lütfen bir teslimat adresi seçin veya yeni adres ekleyin.' });
            return false;
        }
        // hidden inputs doldur
        document.getElementById('customer_name').value = sel.dataset.ad || '';
        document.getElementById('customer_surname').value = sel.dataset.soyad || '';
        document.getElementById('customer_phone').value = sel.dataset.telefon || '';
        document.getElementById('city').value = sel.dataset.city || '';
        document.getElementById('district').value = sel.dataset.district || '';
        document.getElementById('shipping_address').value = sel.dataset.adres || '';
        return true;
    }
    if (cmCurrentStep === 2) {
        const same = document.getElementById('billingSameToggle').checked;
        if (!same) {
            const fields = ['billing_name','billing_surname','billing_phone','billing_city','billing_district','billing_address'];
            for (const f of fields) {
                const v = document.getElementById(f).value.trim();
                if (!v) {
                    Swal.fire({ icon: 'warning', title: 'Eksik alan', text: 'Lütfen tüm fatura alanlarını doldurun.' });
                    return false;
                }
            }
        }
        return true;
    }
    if (cmCurrentStep === 3) {
        const sel = document.querySelector('input[name="shipping_method_id"]:checked');
        if (!sel) {
            Swal.fire({ icon: 'warning', title: 'Kargo seçin', text: 'Lütfen bir kargo yöntemi seçin.' });
            return false;
        }
        return true;
    }
    if (cmCurrentStep === 4) {
        if (document.getElementById('cmStep4Next').disabled) {
            Swal.fire({ icon: 'warning', title: 'Dosya gerekli', text: 'Lütfen önce sipariş dosyanızı yükleyin.' });
            return false;
        }
        return true;
    }
    return true;
}

document.querySelectorAll('[data-cm-next]').forEach(btn => {
    btn.addEventListener('click', () => {
        if (cmValidateCurrent()) cmGoTo(cmCurrentStep + 1);
    });
});
document.querySelectorAll('[data-cm-prev]').forEach(btn => {
    btn.addEventListener('click', () => cmGoTo(cmCurrentStep - 1));
});
document.querySelectorAll('[data-cm-go]').forEach(btn => {
    btn.addEventListener('click', () => cmGoTo(parseInt(btn.dataset.cmGo)));
});

// ============ BILLING TOGGLE ============
const billingToggle = document.getElementById('billingSameToggle');
const billingFields = document.getElementById('billingFields');
billingToggle.addEventListener('change', () => {
    billingFields.hidden = billingToggle.checked;
});

// ============ ŞEHİR / İLÇE ============
function cmFillCities(selectEl) {
    const cities = (typeof getCities === 'function') ? getCities() : [];
    selectEl.innerHTML = '<option value="">İl Seçin</option>';
    cities.forEach(c => {
        const o = document.createElement('option');
        o.value = c; o.textContent = c;
        selectEl.appendChild(o);
    });
}
function cmFillDistricts(citySelect, districtSelect) {
    const districts = (typeof getDistricts === 'function') ? getDistricts(citySelect.value) : [];
    districtSelect.innerHTML = '<option value="">İlçe Seçin</option>';
    districts.forEach(d => {
        const o = document.createElement('option');
        o.value = d; o.textContent = d;
        districtSelect.appendChild(o);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    cmFillCities(document.getElementById('billing_city'));
    cmFillCities(document.getElementById('cmModalCity'));

    document.getElementById('billing_city').addEventListener('change', function() {
        cmFillDistricts(this, document.getElementById('billing_district'));
    });
    document.getElementById('cmModalCity').addEventListener('change', function() {
        cmFillDistricts(this, document.getElementById('cmModalDistrict'));
    });
});

// ============ KARGO FİYAT GÜNCELLEME ============
const baseTotal = {{ (float) $total }};
function cmRefreshTotal() {
    const sel = document.querySelector('input[name="shipping_method_id"]:checked');
    const shipPrice = sel ? parseFloat(sel.dataset.price || 0) : 0;
    const shipEl = document.getElementById('summaryShippingPrice');
    if (sel) {
        shipEl.textContent = shipPrice > 0
            ? '₺' + shipPrice.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
            : 'Ücretsiz';
    } else {
        shipEl.textContent = '—';
    }
    const total = baseTotal + shipPrice;
    document.getElementById('summaryTotal').textContent = '₺' + total.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
document.querySelectorAll('input[name="shipping_method_id"]').forEach(r => {
    r.addEventListener('change', cmRefreshTotal);
});
document.addEventListener('DOMContentLoaded', cmRefreshTotal);

// ============ REVIEW RENDER ============
function cmEsc(s) { return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]); }
function cmRenderReview() {
    const ad = document.getElementById('customer_name').value;
    const soyad = document.getElementById('customer_surname').value;
    const tel = document.getElementById('customer_phone').value;
    const city = document.getElementById('city').value;
    const dist = document.getElementById('district').value;
    const adres = document.getElementById('shipping_address').value;
    document.getElementById('reviewShipping').innerHTML =
        '<strong>' + cmEsc(ad + ' ' + soyad) + '</strong><br>' +
        cmEsc(adres) + '<br>' + cmEsc(city + ' / ' + dist) + '<br><i class="fas fa-phone"></i> ' + cmEsc(tel);

    const same = document.getElementById('billingSameToggle').checked;
    if (same) {
        document.getElementById('reviewBilling').innerHTML = '<em>Teslimat adresiyle aynı</em>';
    } else {
        const bAd = document.getElementById('billing_name').value;
        const bSoy = document.getElementById('billing_surname').value;
        const bTel = document.getElementById('billing_phone').value;
        const bCity = document.getElementById('billing_city').value;
        const bDist = document.getElementById('billing_district').value;
        const bAdres = document.getElementById('billing_address').value;
        const bComp = document.getElementById('billing_company').value;
        const bTax = document.getElementById('billing_tax_no').value;
        let html = '<strong>' + cmEsc(bAd + ' ' + bSoy) + '</strong><br>' +
                   cmEsc(bAdres) + '<br>' + cmEsc(bCity + ' / ' + bDist) + '<br><i class="fas fa-phone"></i> ' + cmEsc(bTel);
        if (bComp) html += '<br><i class="fas fa-building"></i> ' + cmEsc(bComp);
        if (bTax) html += '<br><i class="fas fa-id-card"></i> ' + cmEsc(bTax);
        document.getElementById('reviewBilling').innerHTML = html;
    }

    const ship = document.querySelector('input[name="shipping_method_id"]:checked');
    if (ship) {
        const p = parseFloat(ship.dataset.price || 0);
        const priceTxt = p > 0
            ? '₺' + p.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
            : 'Ücretsiz';
        document.getElementById('reviewShippingMethod').innerHTML =
            '<strong>' + cmEsc(ship.dataset.title) + '</strong> · ' + priceTxt;
    }

    const fileBadge = document.getElementById('orderUploadDoneBadge');
    const filename = document.getElementById('orderUploadFilename').textContent;
    if (fileBadge && !fileBadge.classList.contains('d-none') && filename) {
        document.getElementById('reviewFile').innerHTML = '<i class="fas fa-check-circle text-success"></i> ' + cmEsc(filename) + ' yüklendi';
    } else {
        document.getElementById('reviewFile').innerHTML = '<em>Henüz yüklenmedi</em>';
    }
}

// ============ R2 UPLOAD ============
const ORDER_R2 = { PART_SIZE: 10*1024*1024, PARALLEL_PARTS: 3, MAX_RETRIES: 5, MAX_FILE_BYTES: 524_288_000 };
function orderCsrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value || '';
}
function orderFmt(b) {
    if (!b) return '0 B';
    const k = 1024, sizes = ['B','KB','MB','GB'];
    const i = Math.floor(Math.log(b) / Math.log(k));
    return parseFloat((b / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}
async function uploadOrderZipToR2(file) {
    const status = document.getElementById('orderUploadStatus');
    const bar = document.getElementById('orderUploadProgressBar');
    const txt = document.getElementById('orderUploadProgressText');
    const doneBadge = document.getElementById('orderUploadDoneBadge');
    const nextBtn = document.getElementById('cmStep4Next');
    const hint = document.getElementById('cmStep4Hint');

    status.classList.remove('d-none');
    document.getElementById('orderUploadFilename').textContent = file.name;
    document.getElementById('orderUploadSize').textContent = orderFmt(file.size);
    bar.style.width = '0%'; txt.textContent = '0%';
    doneBadge.classList.add('d-none');

    if (file.size > ORDER_R2.MAX_FILE_BYTES * 1.05) {
        Swal.fire({ icon: 'error', title: 'Dosya çok büyük', text: 'Maksimum 500 MB.' });
        return false;
    }

    let initiate;
    try {
        const r = await fetch('{{ route("upload.r2.order.initiate") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': orderCsrf(), 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ file_size: file.size, file_name: file.name, content_type: file.type || 'application/octet-stream' }),
        });
        initiate = await r.json();
        if (!r.ok || !initiate.success) {
            Swal.fire({ icon: 'error', title: 'Yükleme Başlatılamadı', text: initiate?.message || 'Sunucu hatası' });
            return false;
        }
    } catch (e) { console.error(e); return false; }

    const { upload_id, key, part_size, part_count, part_urls } = initiate;
    const partResults = new Array(part_count);
    let uploadedBytes = 0;

    const uploadPart = async (partInfo) => {
        const start = (partInfo.partNumber - 1) * part_size;
        const end = Math.min(start + part_size, file.size);
        const blob = file.slice(start, end);
        for (let attempt = 1; attempt <= ORDER_R2.MAX_RETRIES; attempt++) {
            try {
                const r = await fetch(partInfo.url, { method: 'PUT', body: blob });
                if (!r.ok) throw new Error('PUT ' + r.status);
                let etag = r.headers.get('ETag') || r.headers.get('etag');
                if (!etag) throw new Error('ETag missing');
                etag = etag.replace(/^"|"$/g, '');
                partResults[partInfo.partNumber - 1] = { PartNumber: partInfo.partNumber, ETag: etag };
                uploadedBytes += blob.size;
                const pct = Math.round((uploadedBytes / file.size) * 100);
                bar.style.width = pct + '%';
                txt.textContent = pct + '% (' + orderFmt(uploadedBytes) + ' / ' + orderFmt(file.size) + ')';
                return true;
            } catch (err) {
                if (attempt < ORDER_R2.MAX_RETRIES) {
                    await new Promise(r => setTimeout(r, Math.min(1000 * Math.pow(2, attempt - 1), 30000)));
                }
            }
        }
        return false;
    };

    const queue = [...part_urls];
    let aborted = false;
    const workers = [];
    for (let w = 0; w < Math.min(ORDER_R2.PARALLEL_PARTS, queue.length); w++) {
        workers.push((async () => {
            while (!aborted && queue.length > 0) {
                const pi = queue.shift();
                if (!pi) break;
                const ok = await uploadPart(pi);
                if (!ok) { aborted = true; break; }
            }
        })());
    }
    await Promise.all(workers);

    if (aborted) {
        try {
            await fetch('{{ route("upload.r2.order.abort") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': orderCsrf() },
                credentials: 'same-origin',
                body: JSON.stringify({ key, upload_id }),
            });
        } catch (e) {}
        Swal.fire({ icon: 'error', title: 'Yükleme Hatası', text: 'Lütfen tekrar deneyin.' });
        return false;
    }

    try {
        const r = await fetch('{{ route("upload.r2.order.complete") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': orderCsrf(), 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ key, upload_id, parts: partResults }),
        });
        const d = await r.json();
        if (!r.ok || !d.success) {
            Swal.fire({ icon: 'error', title: 'Yükleme Tamamlanamadı', text: d?.message || 'Sunucu hatası' });
            return false;
        }
    } catch (e) { console.error(e); return false; }

    bar.style.width = '100%';
    txt.textContent = '100%';
    doneBadge.classList.remove('d-none');
    nextBtn.disabled = false;
    if (hint) hint.textContent = 'İleri';
    nextBtn.innerHTML = 'İleri <i class="fas fa-arrow-right"></i>';
    return true;
}
document.addEventListener('DOMContentLoaded', () => {
    const fi = document.getElementById('orderUploadFile');
    if (fi) {
        fi.addEventListener('change', async (e) => {
            const f = e.target.files?.[0];
            if (!f) return;
            const next = document.getElementById('cmStep4Next');
            if (next) next.disabled = true;
            fi.disabled = true;
            const ok = await uploadOrderZipToR2(f);
            fi.disabled = false;
            if (!ok) fi.value = '';
        });
    }
});

// ============ NEW ADDRESS MODAL ============
let cmModalCurrentTabBtn = null;
document.querySelectorAll('[data-cm-add-address]').forEach(btn => {
    btn.addEventListener('click', () => {
        const type = btn.dataset.cmAddAddress;
        document.getElementById('cmModalType').value = type;
        document.getElementById('cmAddressModalTitle').textContent =
            type === 'company' ? 'Yeni Şirket Adresi Ekle' : 'Yeni Müşteri Adresi Ekle';
        // Clear form
        ['cmModalTitle','cmModalAd','cmModalSoyad','cmModalTelefon','cmModalAdres'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('cmModalCity').value = '';
        document.getElementById('cmModalDistrict').innerHTML = '<option value="">İlçe Seçin</option>';
        cmModalCurrentTabBtn = btn;
        new bootstrap.Modal(document.getElementById('cmAddressModal')).show();
    });
});

document.getElementById('cmModalSaveBtn').addEventListener('click', async () => {
    const type = document.getElementById('cmModalType').value;
    const data = {
        type: type,
        title: document.getElementById('cmModalTitle').value.trim(),
        ad: document.getElementById('cmModalAd').value.trim(),
        soyad: document.getElementById('cmModalSoyad').value.trim(),
        telefon: document.getElementById('cmModalTelefon').value.trim(),
        city: document.getElementById('cmModalCity').value,
        district: document.getElementById('cmModalDistrict').value,
        adres: document.getElementById('cmModalAdres').value.trim(),
        _token: orderCsrf()
    };
    const required = ['title','ad','soyad','telefon','city','district','adres'];
    for (const k of required) {
        if (!data[k]) {
            Swal.fire({ icon: 'warning', title: 'Eksik alan', text: 'Lütfen tüm zorunlu alanları doldurun.' });
            return;
        }
    }
    try {
        const r = await fetch('{{ route("profile.addresses.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': orderCsrf(), 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify(data)
        });
        const res = await r.json();
        if (res.success) {
            Swal.fire({ icon: 'success', title: 'Kaydedildi', text: 'Adres listesini yenilemek için sayfa yeniden yüklenecek.' })
                .then(() => window.location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Hata', text: res.message || 'Kaydedilemedi.' });
        }
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Hata', text: 'Sunucu hatası.' });
    }
});

</script>
@endsection
@endsection
