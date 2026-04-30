@extends('frontend.master')

@section('content')

<main>
    <div class="mb-4 pb-4"></div>
    <section class="shop-checkout container">
      <h2 class="page-title">TESLİMAT VE ÖDEME</h2>
      <div class="checkout-steps">
        <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">01</span>
          <span class="checkout-steps__item-title">
            <span>SEPETİM</span>
            <em>Ürünlerinizi Yönetin</em>
          </span>
        </a>
        <a href="{{ route('cart.checkout') }}" class="checkout-steps__item active">
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
      
      <form name="checkout-form" action="{{ route('cart.complete') }}" method="POST" id="checkoutForm">
        @csrf
        <div class="checkout-form">
          <div class="billing-info__wrapper">
            <h4>Teslimat Bilgileri</h4>
            
            <!-- Adres Seçimi -->
            <div class="row mb-3">
              <div class="col-md-12">
                <label for="address_select" class="form-label">Adres Seçin</label>
                <select class="form-select" id="address_select" onchange="fillAddressForm()">
                  <option value="">Yeni Adres Seçin</option>
                  @foreach($addresses as $address)
                    <option value="{{ $address->id }}" 
                            data-ad="{{ $address->ad }}" 
                            data-soyad="{{ $address->soyad }}" 
                            data-telefon="{{ $address->telefon }}" 
                            data-city="{{ $address->city }}" 
                            data-district="{{ $address->district }}" 
                            data-adres="{{ $address->adres }}">
                      {{ $address->title }} - {{ $address->ad }} {{ $address->soyad }} ({{ $address->city ?? 'İl belirtilmemiş' }}/{{ $address->district ?? 'İlçe belirtilmemiş' }})
                    </option>
                  @endforeach
                </select>
                <small class="text-muted">Kayıtlı adresiniz varsa seçin, yoksa yeni adres bilgilerini aşağıya girin.</small>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-floating my-3">
                  <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Ad" required minlength="2" maxlength="255" pattern="[A-Za-zğüşıöçĞÜŞİÖÇ\s]+" title="Sadece harf ve boşluk karakterleri kullanabilirsiniz">
                  <label for="customer_name">Ad *</label>
                  <div class="invalid-feedback" id="customer_name_error"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating my-3">
                  <input type="text" class="form-control" id="customer_surname" name="customer_surname" placeholder="Soyad" required minlength="2" maxlength="255" pattern="[A-Za-zğüşıöçĞÜŞİÖÇ\s]+" title="Sadece harf ve boşluk karakterleri kullanabilirsiniz">
                  <label for="customer_surname">Soyad *</label>
                  <div class="invalid-feedback" id="customer_surname_error"></div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-floating my-3">
                  <input type="tel" class="form-control" id="customer_phone" name="customer_phone" placeholder="Telefon" required pattern="[0-9\s\-\+\(\)]+" minlength="10" maxlength="20" title="Geçerli bir telefon numarası giriniz">
                  <label for="customer_phone">Telefon *</label>
                  <div class="invalid-feedback" id="customer_phone_error"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating my-3">
                  <select class="form-select" id="city_select" name="city" required onchange="updateDistricts()">
                    <option value="">İl Seçiniz *</option>
                  </select>
                  <label for="city_select">İl *</label>
                  <div class="invalid-feedback" id="city_select_error"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating my-3">
                  <select class="form-select" id="district_select" name="district" required>
                    <option value="">İlçe Seçiniz *</option>
                  </select>
                  <label for="district_select">İlçe *</label>
                  <div class="invalid-feedback" id="district_select_error"></div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-floating my-3">
                  <textarea class="form-control" id="shipping_address" name="shipping_address" placeholder="Teslimat Adresi" style="height: 100px" required minlength="10" maxlength="1000"></textarea>
                  <label for="shipping_address">Teslimat Adresi *</label>
                  <div class="invalid-feedback" id="shipping_address_error"></div>
                </div>
              </div>
            </div>
            
            <!-- Adresi Kaydet Butonu -->
            <div class="row mt-3">
              <div class="col-md-12">
                <button type="button" class="btn btn-outline-primary" id="saveAddressBtn" onclick="saveAddress()">
                  <i class="fas fa-save"></i> Adresi Kaydet
                </button>
              </div>
            </div>
          </div>
          
          <div class="payment-info__wrapper mt-4">
            <h4>Sipariş Dosyaları</h4>
            <div class="card mb-3">
              <div class="card-body">
                <p class="mb-2">
                  <i class="fas fa-info-circle text-primary"></i>
                  <strong>Tüm sepetinizdeki ürünlere ait görselleri</strong> tek bir ZIP dosyasında yükleyin. Dosya boyutu maksimum <strong>500 MB</strong> olabilir.
                </p>
                <input type="file" id="orderUploadFile" class="form-control mb-3" accept=".zip,.rar,.7z">
                <div id="orderUploadStatus" class="d-none">
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
            </div>

            <h4>Ödeme Bilgileri</h4>
            <div class="row">
              <div class="col-md-12">
                <div class="payment-method">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="payment_bakiye" value="bakiye" checked>
                    <label class="form-check-label" for="payment_bakiye">
                      <strong>Bakiye Ödemesi</strong>
                      <p class="text-muted mb-0">Siparişiniz onaylandıktan sonra firma bakiyenizden otomatik olarak ödeme yapılacaktır.</p>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <button type="submit" id="completeOrderSubmitBtn" class="btn btn-primary" disabled style="
            width: 100%;
            margin-top: 20px;
        ">
                      <i class="fas fa-check"></i> SİPARİŞİ TAMAMLA
                    </button>
            <small id="completeOrderHint" class="text-danger d-block text-center mt-2">
              Önce dosyalarınızı yüklemelisiniz.
            </small>


          </div>
          

          
      
        </div>
      </form>
    </section>
    <br>

<!-- Adres Başlığı Modal -->
<div class="modal fade" id="addressTitleModal" tabindex="-1" aria-labelledby="addressTitleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addressTitleModalLabel">Adres Başlığı</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="address_title" class="form-label">Adres Başlığı</label>
          <input type="text" class="form-control" id="address_title" placeholder="Ev, İş, Ofis vb." maxlength="255" required>
          <div class="invalid-feedback" id="address_title_error"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
        <button type="button" class="btn btn-primary" onclick="confirmSaveAddress()">Kaydet</button>
      </div>
    </div>
  </div>
</div>



  
  </main>

  @section('scripts')
  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Türkiye Şehirleri -->
  <script src="{{ asset('js/turkey-cities.js') }}"></script>
<script>

// ============ ORDER-LEVEL R2 DIRECT UPLOAD ============
// Tüm sepet için tek ZIP — Browser → R2 (Cloudflare bypass)
const ORDER_R2 = {
    PART_SIZE: 10 * 1024 * 1024,
    PARALLEL_PARTS: 3,
    MAX_RETRIES: 5,
    MAX_FILE_BYTES: 524_288_000,
};

function orderCsrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value
        || '';
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
    const submitBtn = document.getElementById('completeOrderSubmitBtn');
    const hint = document.getElementById('completeOrderHint');

    status.classList.remove('d-none');
    document.getElementById('orderUploadFilename').textContent = file.name;
    document.getElementById('orderUploadSize').textContent = orderFmt(file.size);
    bar.style.width = '0%';
    txt.textContent = '0%';
    doneBadge.classList.add('d-none');

    if (file.size > ORDER_R2.MAX_FILE_BYTES * 1.05) {
        Swal.fire({ icon: 'error', title: 'Dosya çok büyük', text: 'Maksimum 500 MB.' });
        return false;
    }

    // 1. initiate
    let initiate;
    try {
        const r = await fetch('{{ route("upload.r2.order.initiate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': orderCsrf(),
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                file_size: file.size,
                file_name: file.name,
                content_type: file.type || 'application/octet-stream',
            }),
        });
        initiate = await r.json();
        if (!r.ok || !initiate.success) {
            console.error('R2 initiate failed', initiate);
            Swal.fire({ icon: 'error', title: 'Yükleme Başlatılamadı', text: initiate?.message || 'Sunucu hatası' });
            return false;
        }
    } catch (e) {
        console.error('initiate exception', e);
        return false;
    }

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
                if (!etag) throw new Error('ETag missing (CORS?)');
                etag = etag.replace(/^"|"$/g, '');
                partResults[partInfo.partNumber - 1] = { PartNumber: partInfo.partNumber, ETag: etag };
                uploadedBytes += blob.size;
                const pct = Math.round((uploadedBytes / file.size) * 100);
                bar.style.width = pct + '%';
                txt.textContent = pct + '% (' + orderFmt(uploadedBytes) + ' / ' + orderFmt(file.size) + ')';
                return true;
            } catch (err) {
                console.warn('Part', partInfo.partNumber, 'attempt', attempt, err.message);
                if (attempt < ORDER_R2.MAX_RETRIES) {
                    await new Promise(r => setTimeout(r, Math.min(1000 * Math.pow(2, attempt - 1), 30000)));
                }
            }
        }
        return false;
    };

    // 2. parallel parts
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
        } catch (e) { /* best-effort */ }
        Swal.fire({ icon: 'error', title: 'Yükleme Hatası', text: 'Lütfen tekrar deneyin.' });
        return false;
    }

    // 3. complete
    try {
        const r = await fetch('{{ route("upload.r2.order.complete") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': orderCsrf(), 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ key, upload_id, parts: partResults }),
        });
        const d = await r.json();
        if (!r.ok || !d.success) {
            console.error('complete failed', d);
            Swal.fire({ icon: 'error', title: 'Yükleme Tamamlanamadı', text: d?.message || 'Sunucu hatası' });
            return false;
        }
    } catch (e) {
        console.error('complete exception', e);
        return false;
    }

    bar.style.width = '100%';
    txt.textContent = '100%';
    doneBadge.classList.remove('d-none');
    submitBtn.disabled = false;
    if (hint) hint.classList.add('d-none');
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('orderUploadFile');
    if (fileInput) {
        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files?.[0];
            if (!file) return;

            // Disable submit during upload
            const submitBtn = document.getElementById('completeOrderSubmitBtn');
            if (submitBtn) submitBtn.disabled = true;
            fileInput.disabled = true;

            const ok = await uploadOrderZipToR2(file);
            fileInput.disabled = false;
            if (!ok) {
                fileInput.value = '';
            }
        });
    }
});
// ============ /ORDER-LEVEL R2 DIRECT UPLOAD ============

// Sayfa yüklendiğinde illeri yükle
document.addEventListener('DOMContentLoaded', function() {
    loadCities();
});

// İlleri yükle
function loadCities() {
    const citySelect = document.getElementById('city_select');
    const cities = getCities();
    
    cities.forEach(city => {
        const option = document.createElement('option');
        option.value = city;
        option.textContent = city;
        citySelect.appendChild(option);
    });
}

// İl seçildiğinde ilçeleri güncelle
function updateDistricts() {
    const citySelect = document.getElementById('city_select');
    const districtSelect = document.getElementById('district_select');
    const selectedCity = citySelect.value;
    
    // İlçe select'i temizle
    districtSelect.innerHTML = '<option value="">İlçe Seçiniz *</option>';
    
    if (selectedCity) {
        const districts = getDistricts(selectedCity);
        districts.forEach(district => {
            const option = document.createElement('option');
            option.value = district;
            option.textContent = district;
            districtSelect.appendChild(option);
        });
    }
}

function showSuccessAlert(message) {
    Swal.fire({
        icon: 'success',
        title: 'Başarılı!',
        text: message,
        confirmButtonText: 'Tamam'
    });
}

function showErrorAlert(message) {
    Swal.fire({
        icon: 'error',
        title: 'Hata!',
        text: message,
        confirmButtonText: 'Tamam'
    });
}

function fillAddressForm() {
    const select = document.getElementById('address_select');
    const selectedOption = select.options[select.selectedIndex];
    
    if (select.value === '') {
        // Yeni adres seçildi - formu temizle
        document.getElementById('customer_name').value = '';
        document.getElementById('customer_surname').value = '';
        document.getElementById('customer_phone').value = '';
        document.getElementById('city_select').value = '';
        document.getElementById('district_select').innerHTML = '<option value="">İlçe Seçiniz *</option>';
        document.getElementById('shipping_address').value = '';
    } else {
        // Kayıtlı adres seçildi - formu doldur
        document.getElementById('customer_name').value = selectedOption.getAttribute('data-ad');
        document.getElementById('customer_surname').value = selectedOption.getAttribute('data-soyad');
        document.getElementById('customer_phone').value = selectedOption.getAttribute('data-telefon');
        
        // İl ve ilçe bilgilerini doldur
        const city = selectedOption.getAttribute('data-city');
        const district = selectedOption.getAttribute('data-district');
        
        if (city) {
            document.getElementById('city_select').value = city;
            updateDistricts(); // İlçeleri yükle
            
            // İlçe seçimini geciktir çünkü updateDistricts asenkron çalışıyor
            setTimeout(() => {
                if (district) {
                    document.getElementById('district_select').value = district;
                }
            }, 100);
        }
        
        document.getElementById('shipping_address').value = selectedOption.getAttribute('data-adres');
    }
}

function saveAddress() {
    // Form validasyonu
    const customerName = document.getElementById('customer_name').value.trim();
    const customerSurname = document.getElementById('customer_surname').value.trim();
    const customerPhone = document.getElementById('customer_phone').value.trim();
    const city = document.getElementById('city_select').value.trim();
    const district = document.getElementById('district_select').value.trim();
    const shippingAddress = document.getElementById('shipping_address').value.trim();
    
    let isValid = true;
    
    // Ad validasyonu
    if (customerName.length < 2) {
        showFieldError('customer_name', 'Ad en az 2 karakter olmalıdır.');
        isValid = false;
    } else if (!/^[A-Za-zğüşıöçĞÜŞİÖÇ\s]+$/.test(customerName)) {
        showFieldError('customer_name', 'Ad sadece harf ve boşluk karakterleri içerebilir.');
        isValid = false;
    } else {
        clearFieldError('customer_name');
    }
    
    // Soyad validasyonu
    if (customerSurname.length < 2) {
        showFieldError('customer_surname', 'Soyad en az 2 karakter olmalıdır.');
        isValid = false;
    } else if (!/^[A-Za-zğüşıöçĞÜŞİÖÇ\s]+$/.test(customerSurname)) {
        showFieldError('customer_surname', 'Soyad sadece harf ve boşluk karakterleri içerebilir.');
        isValid = false;
    } else {
        clearFieldError('customer_surname');
    }
    
    // Telefon validasyonu
    if (customerPhone.length < 10) {
        showFieldError('customer_phone', 'Telefon numarası en az 10 karakter olmalıdır.');
        isValid = false;
    } else if (!/^[0-9\s\-\+\(\)]+$/.test(customerPhone)) {
        showFieldError('customer_phone', 'Geçerli bir telefon numarası giriniz.');
        isValid = false;
    } else {
        clearFieldError('customer_phone');
    }
    
    // İl validasyonu
    if (!city) {
        showFieldError('city_select', 'Lütfen il seçiniz.');
        isValid = false;
    } else {
        clearFieldError('city_select');
    }
    
    // İlçe validasyonu
    if (!district) {
        showFieldError('district_select', 'Lütfen ilçe seçiniz.');
        isValid = false;
    } else {
        clearFieldError('district_select');
    }
    
    // Adres validasyonu
    if (shippingAddress.length < 10) {
        showFieldError('shipping_address', 'Adres en az 10 karakter olmalıdır.');
        isValid = false;
    } else {
        clearFieldError('shipping_address');
    }
    
    if (!isValid) {
        showErrorAlert('Lütfen form alanlarını kontrol ediniz.');
        return;
    }
    
    // Modal'ı aç
    document.getElementById('address_title').value = '';
    const modal = new bootstrap.Modal(document.getElementById('addressTitleModal'));
    modal.show();
}

function confirmSaveAddress() {
    const addressTitle = document.getElementById('address_title').value.trim();
    
    if (addressTitle.length < 2) {
        showFieldError('address_title', 'Adres başlığı en az 2 karakter olmalıdır.');
        return;
    }
    
    clearFieldError('address_title');
    
    // Form verilerini al
    const formData = {
        title: addressTitle,
        ad: document.getElementById('customer_name').value.trim(),
        soyad: document.getElementById('customer_surname').value.trim(),
        telefon: document.getElementById('customer_phone').value.trim(),
        city: document.getElementById('city_select').value.trim(),
        district: document.getElementById('district_select').value.trim(),
        adres: document.getElementById('shipping_address').value.trim(),
        _token: document.querySelector('input[name="_token"]').value
    };
    
    // AJAX ile adres kaydet
    console.log('Sending form data:', formData);
    
    fetch('{{ route("profile.addresses.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        // Modal'ı kapat
        const modal = bootstrap.Modal.getInstance(document.getElementById('addressTitleModal'));
        modal.hide();
        
        if (data.success) {
            showSuccessAlert(data.message || 'Adres başarıyla kaydedildi.');
        } else {
            // Validation hatalarını göster
            if (data.errors) {
                let errorMessage = 'Lütfen aşağıdaki hataları düzeltin:\n';
                Object.keys(data.errors).forEach(field => {
                    data.errors[field].forEach(error => {
                        errorMessage += `• ${error}\n`;
                    });
                });
                
                showErrorAlert(errorMessage);
            } else {
                showErrorAlert(data.message || 'Adres kaydedilirken bir hata oluştu.');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const modal = bootstrap.Modal.getInstance(document.getElementById('addressTitleModal'));
        modal.hide();
        
        showErrorAlert('Bir hata oluştu. Lütfen tekrar deneyiniz.');
    });
}

function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + '_error');
    
    field.classList.add('is-invalid');
    if (errorDiv) {
        errorDiv.textContent = message;
    }
}

function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + '_error');
    
    field.classList.remove('is-invalid');
    if (errorDiv) {
        errorDiv.textContent = '';
    }
}

// Dosya zorunluluğu kontrolü (backend tarafından hesaplanan eksik dosyalı ürünler)
const missingFileItems = @json($missingFileItems ?? []);

// Form submit validasyonu
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    // Önce dosya zorunluluğu kontrolü
    if (missingFileItems.length > 0) {
        e.preventDefault();
        const productListText = missingFileItems.map(function(p){ return '• ' + p; }).join('\n');
        const escapeHtml = function(s){
            return String(s).replace(/[&<>"']/g, function(c){
                return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c];
            });
        };
        if (window.Swal) {
            Swal.fire({
                icon: 'warning',
                title: 'Dosya Yüklemesi Zorunlu',
                html: 'Aşağıdaki ürünler için dosya yüklemesi zorunludur:<br><br><strong>' +
                      missingFileItems.map(function(p){ return '• ' + escapeHtml(p); }).join('<br>') +
                      '</strong><br><br>Lütfen sepete dönüp dosyayı yükleyin.',
                confirmButtonText: 'Sepete Dön',
                showCancelButton: true,
                cancelButtonText: 'Kapat'
            }).then(function(result){
                if (result.isConfirmed) {
                    window.location.href = "{{ route('cart.index') }}";
                }
            });
        } else {
            if (confirm('Aşağıdaki ürünler için dosya yüklemesi zorunludur:\n\n' + productListText + '\n\nSepete dönmek ister misiniz?')) {
                window.location.href = "{{ route('cart.index') }}";
            }
        }
        return;
    }

    const customerName = document.getElementById('customer_name').value.trim();
    const customerSurname = document.getElementById('customer_surname').value.trim();
    const customerPhone = document.getElementById('customer_phone').value.trim();
    const city = document.getElementById('city_select').value.trim();
    const district = document.getElementById('district_select').value.trim();
    const shippingAddress = document.getElementById('shipping_address').value.trim();
    
    let isValid = true;
    
    // Ad validasyonu
    if (customerName.length < 2) {
        showFieldError('customer_name', 'Ad en az 2 karakter olmalıdır.');
        isValid = false;
    } else if (!/^[A-Za-zğüşıöçĞÜŞİÖÇ\s]+$/.test(customerName)) {
        showFieldError('customer_name', 'Ad sadece harf ve boşluk karakterleri içerebilir.');
        isValid = false;
    } else {
        clearFieldError('customer_name');
    }
    
    // Soyad validasyonu
    if (customerSurname.length < 2) {
        showFieldError('customer_surname', 'Soyad en az 2 karakter olmalıdır.');
        isValid = false;
    } else if (!/^[A-Za-zğüşıöçĞÜŞİÖÇ\s]+$/.test(customerSurname)) {
        showFieldError('customer_surname', 'Soyad sadece harf ve boşluk karakterleri içerebilir.');
        isValid = false;
    } else {
        clearFieldError('customer_surname');
    }
    
    // Telefon validasyonu
    if (customerPhone.length < 10) {
        showFieldError('customer_phone', 'Telefon numarası en az 10 karakter olmalıdır.');
        isValid = false;
    } else if (!/^[0-9\s\-\+\(\)]+$/.test(customerPhone)) {
        showFieldError('customer_phone', 'Geçerli bir telefon numarası giriniz.');
        isValid = false;
    } else {
        clearFieldError('customer_phone');
    }
    
    // İl validasyonu
    if (!city) {
        showFieldError('city_select', 'Lütfen il seçiniz.');
        isValid = false;
    } else {
        clearFieldError('city_select');
    }
    
    // İlçe validasyonu
    if (!district) {
        showFieldError('district_select', 'Lütfen ilçe seçiniz.');
        isValid = false;
    } else {
        clearFieldError('district_select');
    }
    
    // Adres validasyonu
    if (shippingAddress.length < 10) {
        showFieldError('shipping_address', 'Adres en az 10 karakter olmalıdır.');
        isValid = false;
    } else {
        clearFieldError('shipping_address');
    }
    
    if (!isValid) {
        e.preventDefault();
        showErrorAlert('Lütfen form alanlarını kontrol ediniz.');
    }
});
</script>
  @endsection
@endsection