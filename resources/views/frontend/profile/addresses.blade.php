@extends('frontend.master')

@section('content')
<main>
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">ADRESLERİM</h2>
      <div class="row">
        <div class="col-lg-3">
          <ul class="account-nav">
            <li><a href="{{ route('profile.index') }}" class="menu-link menu-link_us-s">DASHBOARD</a></li>
            <li><a href="{{ route('profile.orders') }}" class="menu-link menu-link_us-s">SİPARİŞLERİM</a></li>
            <li><a href="{{ route('profile.addresses') }}" class="menu-link menu-link_us-s menu-link_active">ADRESLERİM</a></li>
            <li><a href="{{ route('profile.detail') }}" class="menu-link menu-link_us-s">HESAP DETAYLARI</a></li>
            @if(auth()->user()->hasRole(1))
            <li><a href="{{ route('admin.dashboard') }}" class="menu-link menu-link_us-s">YÖNETİM PANELİ</a></li>
            @endif
            @if(auth()->user()->hasRole(3))
            <li><a href="{{ route('profile.personels') }}" class="menu-link menu-link_us-s">PERSONELLERİM</a></li>
            @endif
            <li>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="menu-link menu-link_us-s border-0 bg-transparent" style="width: 100%; text-align: left; padding: 0; font-size: 14px;font-weight: 500;text-transform: uppercase;">
                        Çıkış Yap
                    </button>
                </form>
            </li>
          </ul>
        </div>
        <div class="col-lg-9">
          <div class="page-content my-account__address">
            <p class="notice">Aşağıdaki adresler ödeme sayfasında varsayılan olarak kullanılacaktır.</p>
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Yeni Adres Ekleme Butonu -->
            <div class="mb-4">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                    <i class="fas fa-plus"></i> Yeni Adres Ekle
                </button>
            </div>

            <div class="my-account__address-list">
              @forelse($addresses as $address)
                <div class="my-account__address-item">
                  <div class="my-account__address-item__title">
                    <h5>{{ $address->title }}</h5>
                    <div>
                      <a href="#" class="btn btn-sm btn-outline-primary" onclick="editAddress({{ $address->id }})">Düzenle</a>
                      <form method="POST" action="{{ route('profile.addresses.delete', $address->id) }}" style="display: inline;" onsubmit="return confirm('Bu adresi silmek istediğinizden emin misiniz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Sil</button>
                      </form>
                    </div>
                  </div>
                  <div class="my-account__address-item__detail">
                    <p>{{ $address->ad }} {{ $address->soyad }}</p>
                    <p>{{ $address->city ?? 'İl belirtilmemiş' }} / {{ $address->district ?? 'İlçe belirtilmemiş' }}</p>
                    <p>{{ $address->adres }}</p>
                    <p>{{ $address->telefon }}</p>
                  </div>
                </div>
              @empty
                <div class="text-center py-4">
                  <p class="text-muted">Henüz adres eklenmemiş.</p>
                </div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Yeni Adres Ekleme Modal -->
  <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addAddressModalLabel">Yeni Adres Ekle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('profile.addresses.store') }}">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label for="title" class="form-label">Adres Başlığı</label>
              <input type="text" class="form-control" id="title" name="title" required placeholder="Ev, İş vb.">
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="ad" class="form-label">Ad</label>
                  <input type="text" class="form-control" id="ad" name="ad" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="soyad" class="form-label">Soyad</label>
                  <input type="text" class="form-control" id="soyad" name="soyad" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="city" class="form-label">İl</label>
                  <select class="form-select" id="city" name="city" required onchange="updateDistricts()">
                    <option value="">İl Seçiniz *</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="district" class="form-label">İlçe</label>
                  <select class="form-select" id="district" name="district" required>
                    <option value="">İlçe Seçiniz *</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="adres" class="form-label">Adres</label>
              <textarea class="form-control" id="adres" name="adres" rows="3" required></textarea>
            </div>
            <div class="mb-3">
              <label for="telefon" class="form-label">Telefon</label>
              <input type="text" class="form-control" id="telefon" name="telefon" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
            <button type="submit" class="btn btn-primary">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Adres Düzenleme Modal -->
  <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editAddressModalLabel">Adres Düzenle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" id="editAddressForm">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="mb-3">
              <label for="edit_title" class="form-label">Adres Başlığı</label>
              <input type="text" class="form-control" id="edit_title" name="title" required>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="edit_ad" class="form-label">Ad</label>
                  <input type="text" class="form-control" id="edit_ad" name="ad" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="edit_soyad" class="form-label">Soyad</label>
                  <input type="text" class="form-control" id="edit_soyad" name="soyad" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="edit_city" class="form-label">İl</label>
                  <select class="form-select" id="edit_city" name="city" required onchange="updateEditDistricts()">
                    <option value="">İl Seçiniz *</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="edit_district" class="form-label">İlçe</label>
                  <select class="form-select" id="edit_district" name="district" required>
                    <option value="">İlçe Seçiniz *</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="edit_adres" class="form-label">Adres</label>
              <textarea class="form-control" id="edit_adres" name="adres" rows="3" required></textarea>
            </div>
            <div class="mb-3">
              <label for="edit_telefon" class="form-label">Telefon</label>
              <input type="text" class="form-control" id="edit_telefon" name="telefon" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
            <button type="submit" class="btn btn-primary">Güncelle</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<br><br>
  <!-- Türkiye Şehirleri -->
  <script src="{{ asset('js/turkey-cities.js') }}"></script>
  <script>
    // Sayfa yüklendiğinde illeri yükle
    document.addEventListener('DOMContentLoaded', function() {
        loadCities();
        loadEditCities();
    });

    // İlleri yükle (yeni adres için)
    function loadCities() {
        const citySelect = document.getElementById('city');
        const cities = getCities();
        
        cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
    }

    // İlleri yükle (düzenleme için)
    function loadEditCities() {
        const citySelect = document.getElementById('edit_city');
        const cities = getCities();
        
        cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
    }

    // İl seçildiğinde ilçeleri güncelle (yeni adres için)
    function updateDistricts() {
        const citySelect = document.getElementById('city');
        const districtSelect = document.getElementById('district');
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

    // İl seçildiğinde ilçeleri güncelle (düzenleme için)
    function updateEditDistricts() {
        const citySelect = document.getElementById('edit_city');
        const districtSelect = document.getElementById('edit_district');
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

    function editAddress(addressId) {
      // Burada AJAX ile adres bilgilerini çekip modal'a doldurabilirsiniz
      // Şimdilik basit bir örnek:
      const address = @json($addresses);
      const selectedAddress = address.find(addr => addr.id === addressId);
      
      if (selectedAddress) {
        document.getElementById('edit_title').value = selectedAddress.title;
        document.getElementById('edit_ad').value = selectedAddress.ad;
        document.getElementById('edit_soyad').value = selectedAddress.soyad;
        
        // İl ve ilçe bilgilerini doldur
        if (selectedAddress.city) {
            document.getElementById('edit_city').value = selectedAddress.city;
            updateEditDistricts(); // İlçeleri yükle
            
            // İlçe seçimini geciktir
            setTimeout(() => {
                if (selectedAddress.district) {
                    document.getElementById('edit_district').value = selectedAddress.district;
                }
            }, 100);
        }
        
        document.getElementById('edit_adres').value = selectedAddress.adres;
        document.getElementById('edit_telefon').value = selectedAddress.telefon;
        
        // Route URL'sini doğru şekilde oluştur
        const updateUrl = '{{ route("profile.addresses.update", ":id") }}'.replace(':id', addressId);
        document.getElementById('editAddressForm').action = updateUrl;
        
        new bootstrap.Modal(document.getElementById('editAddressModal')).show();
      }
    }
  </script>
@endsection