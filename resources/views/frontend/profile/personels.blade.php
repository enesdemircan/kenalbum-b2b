@extends('frontend.master')

@section('content')
<main>
  <div class="mb-4 pb-4"></div>
  <section class="profile-shell container">

    <div class="profile-shell__head">
      <div>
        <h1 class="profile-shell__heading"><i class="fas fa-id-badge"></i> Personellerim</h1>
        <p class="profile-shell__sub">Firmanıza atanmış kullanıcıları yönetin.</p>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-3">
        @include('frontend.profile._sidebar', ['active' => 'personels'])
      </div>

      <div class="col-lg-9">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

        <div class="profile-card">
          <div class="profile-card__head">
            <h3 class="profile-card__title"><i class="fas fa-users"></i> Personel Listesi</h3>
            <button type="button" class="cm-btn cm-btn--primary" style="font-size:.85rem; padding:8px 16px;" data-bs-toggle="modal" data-bs-target="#addPersonelModal">
              <i class="fas fa-plus"></i> Yeni Personel
            </button>
          </div>

          @if($personels->count() > 0)
            <div class="table-responsive">
              <table class="order-detail-table">
                <thead>
                  <tr>
                    <th>Ad Soyad</th>
                    <th>E-posta</th>
                    <th>Roller</th>
                    <th>Kayıt</th>
                    <th style="text-align:right;">İşlemler</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($personels as $personel)
                    <tr>
                      <td><strong>{{ $personel->name }}</strong></td>
                      <td class="text-muted">{{ $personel->email }}</td>
                      <td>
                        @foreach($personel->roles as $role)
                          <span class="cm-badge cm-badge--company" style="margin-right:4px;">{{ $role->name }}</span>
                        @endforeach
                      </td>
                      <td class="text-muted">{{ $personel->created_at->format('d.m.Y') }}</td>
                      <td style="text-align:right;">
                        <a href="{{ route('profile.personels.orders', $personel->id) }}" class="btn-orange-sm" title="Siparişleri Görüntüle"><i class="fas fa-bag-shopping"></i></a>
                        <form action="{{ route('profile.personels.delete', $personel->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('{{ $personel->name }} adlı personeli silmek istediğinizden emin misiniz?')">
                          @csrf @method('DELETE')
                          <button type="submit" class="cm-btn cm-btn--ghost" style="padding:6px 12px; font-size:.78rem;" title="Sil"><i class="fas fa-trash"></i></button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <p class="text-muted small mt-3 mb-0">Toplam {{ $personels->count() }} personel.</p>
          @else
            <div class="profile-empty">
              <i class="fas fa-id-badge"></i>
              <h4>Henüz personel atanmamış</h4>
              <p>"Yeni Personel" butonu ile firmanıza kullanıcı ekleyin.</p>
            </div>
          @endif
        </div>
      </div>
    </div>

  </section>
</main>

{{-- Personel Ekleme Modal --}}
<div class="modal fade" id="addPersonelModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-user-plus me-2" style="color:#ea580c"></i> Yeni Personel Ekle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('profile.personels.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12"><label class="form-label">Ad Soyad *</label><input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-12"><label class="form-label">E-posta *</label><input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label">Şifre *</label><input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label">Şifre Tekrar *</label><input type="password" class="form-control" name="password_confirmation" required></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="cm-btn cm-btn--ghost" data-bs-dismiss="modal">İptal</button>
          <button type="submit" class="cm-btn cm-btn--primary"><i class="fas fa-save"></i> Personel Ekle</button>
        </div>
      </form>
    </div>
  </div>
</div>

<br><br>
@endsection
