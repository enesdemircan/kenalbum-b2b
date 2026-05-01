@extends('frontend.master')

@section('content')
<main>
  <div class="mb-4 pb-4"></div>
  <section class="profile-shell container">

    <div class="profile-shell__head">
      <div>
        <h1 class="profile-shell__heading"><i class="fas fa-location-dot"></i> Adreslerim</h1>
        <p class="profile-shell__sub">Şirket ve müşteri adreslerinizi yönetin.</p>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-3">
        @include('frontend.profile._sidebar', ['active' => 'addresses'])
      </div>

      <div class="col-lg-9">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

        {{-- ŞİRKET ADRESLERİM --}}
        <div class="profile-card address-group">
          <div class="profile-card__head">
            <div>
              <h3 class="profile-card__title address-group-title address-group-title--company"><i class="fas fa-building"></i> Şirket Adreslerim</h3>
              <p class="profile-card__sub">Sipariş bana gelsin — bayi/atölye/depo adresleriniz.</p>
            </div>
            <button type="button" class="cm-btn cm-btn--primary" style="font-size:.85rem; padding:8px 16px;" data-bs-toggle="modal" data-bs-target="#addAddressModal" onclick="prepareAddModal('company')">
              <i class="fas fa-plus"></i> Yeni Şirket Adresi
            </button>
          </div>
          <div class="row g-3">
            @forelse($companyAddresses as $address)
              <div class="col-md-6 col-lg-4">
                <div class="address-card">
                  <div class="address-card__head">
                    <strong><i class="fas fa-building" style="color:#ea580c"></i> {{ $address->title }}</strong>
                    <span class="cm-badge cm-badge--company"><i class="fas fa-building"></i> Şirket</span>
                  </div>
                  <div class="address-card__lines">
                    <p><strong>{{ $address->ad }} {{ $address->soyad }}</strong></p>
                    <p><i class="fas fa-location-dot"></i> {{ $address->city ?? '—' }} / {{ $address->district ?? '—' }}</p>
                    <p>{{ $address->adres }}</p>
                    <p><i class="fas fa-phone"></i> {{ $address->telefon }}</p>
                  </div>
                  <div class="address-card__actions">
                    <button class="btn-orange-sm" onclick="editAddress({{ $address->id }})"><i class="fas fa-edit"></i> Düzenle</button>
                    <form method="POST" action="{{ route('profile.addresses.delete', $address->id) }}" style="display:inline;" onsubmit="return confirm('Bu adresi silmek istediğinizden emin misiniz?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="cm-btn cm-btn--ghost" style="padding:6px 12px; font-size:.78rem;"><i class="fas fa-trash"></i></button>
                    </form>
                  </div>
                </div>
              </div>
            @empty
              <div class="col-12">
                <div class="profile-empty" style="padding:24px 16px;">
                  <i class="fas fa-building" style="font-size:1.8rem;"></i>
                  <p class="mb-0">Henüz şirket adresi eklenmemiş.</p>
                </div>
              </div>
            @endforelse
          </div>
        </div>

        {{-- MÜŞTERİ ADRESLERİM --}}
        <div class="profile-card address-group">
          <div class="profile-card__head">
            <div>
              <h3 class="profile-card__title address-group-title address-group-title--customer"><i class="fas fa-users"></i> Müşteri Adreslerim</h3>
              <p class="profile-card__sub">Sipariş müşterime gitsin — son müşteri adresleri.</p>
            </div>
            <button type="button" class="cm-btn cm-btn--primary" style="font-size:.85rem; padding:8px 16px;" data-bs-toggle="modal" data-bs-target="#addAddressModal" onclick="prepareAddModal('customer')">
              <i class="fas fa-plus"></i> Yeni Müşteri Adresi
            </button>
          </div>
          <div class="row g-3">
            @forelse($customerAddresses as $address)
              <div class="col-md-6 col-lg-4">
                <div class="address-card">
                  <div class="address-card__head">
                    <strong><i class="fas fa-users" style="color:#2563eb"></i> {{ $address->title }}</strong>
                    <span class="cm-badge cm-badge--customer"><i class="fas fa-users"></i> Müşteri</span>
                  </div>
                  <div class="address-card__lines">
                    <p><strong>{{ $address->ad }} {{ $address->soyad }}</strong></p>
                    <p><i class="fas fa-location-dot"></i> {{ $address->city ?? '—' }} / {{ $address->district ?? '—' }}</p>
                    <p>{{ $address->adres }}</p>
                    <p><i class="fas fa-phone"></i> {{ $address->telefon }}</p>
                  </div>
                  <div class="address-card__actions">
                    <button class="btn-orange-sm" onclick="editAddress({{ $address->id }})"><i class="fas fa-edit"></i> Düzenle</button>
                    <form method="POST" action="{{ route('profile.addresses.delete', $address->id) }}" style="display:inline;" onsubmit="return confirm('Bu adresi silmek istediğinizden emin misiniz?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="cm-btn cm-btn--ghost" style="padding:6px 12px; font-size:.78rem;"><i class="fas fa-trash"></i></button>
                    </form>
                  </div>
                </div>
              </div>
            @empty
              <div class="col-12">
                <div class="profile-empty" style="padding:24px 16px;">
                  <i class="fas fa-users" style="font-size:1.8rem;"></i>
                  <p class="mb-0">Henüz müşteri adresi eklenmemiş.</p>
                </div>
              </div>
            @endforelse
          </div>
        </div>
      </div>
    </div>

  </section>
</main>

{{-- Yeni Adres Modal --}}
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-plus me-2" style="color:#ea580c"></i> Yeni Adres Ekle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('profile.addresses.store') }}">
        @csrf
        <input type="hidden" name="type" id="addAddressType" value="company">
        <div class="modal-body">
          <div class="mb-3 address-type-indicator p-3 rounded" style="background:#fff7ed; border:1px solid #fed7aa;">
            <small class="d-block" style="font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; color:#94a3b8;">Adres Tipi</small>
            <strong id="addAddressTypeLabel" style="color:#9a3412;">🏢 Şirket Adresi (Bana Gelsin)</strong>
          </div>
          <div class="mb-3">
            <label class="form-label">Adres Başlığı</label>
            <input type="text" class="form-control" name="title" required placeholder="Atölye / Müşteri Ali vb.">
          </div>
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Ad</label><input type="text" class="form-control" name="ad" required></div>
            <div class="col-md-6"><label class="form-label">Soyad</label><input type="text" class="form-control" name="soyad" required></div>
            <div class="col-md-6"><label class="form-label">İl</label><select class="form-select" id="city" name="city" required onchange="updateDistricts()"><option value="">İl Seçiniz</option></select></div>
            <div class="col-md-6"><label class="form-label">İlçe</label><select class="form-select" id="district" name="district" required><option value="">İlçe Seçiniz</option></select></div>
            <div class="col-12"><label class="form-label">Adres</label><textarea class="form-control" name="adres" rows="3" required></textarea></div>
            <div class="col-12"><label class="form-label">Telefon</label><input type="text" class="form-control" name="telefon" required></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="cm-btn cm-btn--ghost" data-bs-dismiss="modal">İptal</button>
          <button type="submit" class="cm-btn cm-btn--primary"><i class="fas fa-save"></i> Kaydet</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Düzenleme Modal --}}
<div class="modal fade" id="editAddressModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-edit me-2" style="color:#ea580c"></i> Adres Düzenle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="editAddressForm">
        @csrf @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Adres Tipi</label>
            <select class="form-select" id="edit_type" name="type" required>
              <option value="company">🏢 Şirket Adresi (Bana Gelsin)</option>
              <option value="customer">👥 Müşteri Adresi (Müşterime Gitsin)</option>
            </select>
          </div>
          <div class="mb-3"><label class="form-label">Adres Başlığı</label><input type="text" class="form-control" id="edit_title" name="title" required></div>
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Ad</label><input type="text" class="form-control" id="edit_ad" name="ad" required></div>
            <div class="col-md-6"><label class="form-label">Soyad</label><input type="text" class="form-control" id="edit_soyad" name="soyad" required></div>
            <div class="col-md-6"><label class="form-label">İl</label><select class="form-select" id="edit_city" name="city" required onchange="updateEditDistricts()"><option value="">İl Seçiniz</option></select></div>
            <div class="col-md-6"><label class="form-label">İlçe</label><select class="form-select" id="edit_district" name="district" required><option value="">İlçe Seçiniz</option></select></div>
            <div class="col-12"><label class="form-label">Adres</label><textarea class="form-control" id="edit_adres" name="adres" rows="3" required></textarea></div>
            <div class="col-12"><label class="form-label">Telefon</label><input type="text" class="form-control" id="edit_telefon" name="telefon" required></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="cm-btn cm-btn--ghost" data-bs-dismiss="modal">İptal</button>
          <button type="submit" class="cm-btn cm-btn--primary"><i class="fas fa-save"></i> Güncelle</button>
        </div>
      </form>
    </div>
  </div>
</div>

<br><br>

<script src="{{ asset('js/turkey-cities.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCities();
    loadEditCities();
});

function loadCities() {
    const sel = document.getElementById('city');
    getCities().forEach(c => { const o = document.createElement('option'); o.value = c; o.textContent = c; sel.appendChild(o); });
}
function loadEditCities() {
    const sel = document.getElementById('edit_city');
    getCities().forEach(c => { const o = document.createElement('option'); o.value = c; o.textContent = c; sel.appendChild(o); });
}
function updateDistricts() {
    const cs = document.getElementById('city');
    const ds = document.getElementById('district');
    ds.innerHTML = '<option value="">İlçe Seçiniz</option>';
    if (cs.value) getDistricts(cs.value).forEach(d => { const o = document.createElement('option'); o.value = d; o.textContent = d; ds.appendChild(o); });
}
function updateEditDistricts() {
    const cs = document.getElementById('edit_city');
    const ds = document.getElementById('edit_district');
    ds.innerHTML = '<option value="">İlçe Seçiniz</option>';
    if (cs.value) getDistricts(cs.value).forEach(d => { const o = document.createElement('option'); o.value = d; o.textContent = d; ds.appendChild(o); });
}

function prepareAddModal(type) {
    const hidden = document.getElementById('addAddressType');
    const label = document.getElementById('addAddressTypeLabel');
    const indicator = document.querySelector('.address-type-indicator');
    hidden.value = type;
    if (type === 'customer') {
        label.textContent = '👥 Müşteri Adresi (Müşterime Gitsin)';
        label.style.color = '#1e3a8a';
        indicator.style.background = '#dbeafe';
        indicator.style.borderColor = '#93c5fd';
    } else {
        label.textContent = '🏢 Şirket Adresi (Bana Gelsin)';
        label.style.color = '#9a3412';
        indicator.style.background = '#fff7ed';
        indicator.style.borderColor = '#fed7aa';
    }
}

function editAddress(addressId) {
    const addresses = [...@json($companyAddresses), ...@json($customerAddresses)];
    const a = addresses.find(addr => addr.id === addressId);
    if (!a) return;
    document.getElementById('edit_type').value = a.type || 'company';
    document.getElementById('edit_title').value = a.title;
    document.getElementById('edit_ad').value = a.ad;
    document.getElementById('edit_soyad').value = a.soyad;
    if (a.city) {
        document.getElementById('edit_city').value = a.city;
        updateEditDistricts();
        setTimeout(() => { if (a.district) document.getElementById('edit_district').value = a.district; }, 100);
    }
    document.getElementById('edit_adres').value = a.adres;
    document.getElementById('edit_telefon').value = a.telefon;
    const updateUrl = '{{ route("profile.addresses.update", ":id") }}'.replace(':id', addressId);
    document.getElementById('editAddressForm').action = updateUrl;
    new bootstrap.Modal(document.getElementById('editAddressModal')).show();
}
</script>
@endsection
