@extends('frontend.master')

@section('content')
<main>
  <div class="mb-4 pb-4"></div>
  <section class="profile-shell container">

    <div class="profile-shell__head">
      <div>
        <h1 class="profile-shell__heading"><i class="fas fa-user-pen"></i> Hesap Detayları</h1>
        <p class="profile-shell__sub">Kişisel bilgilerinizi ve şifrenizi güncelleyin.</p>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-3">
        @include('frontend.profile._sidebar', ['active' => 'detail'])
      </div>

      <div class="col-lg-9">
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="profile-card profile-form">
          <form name="account_edit_form" class="needs-validation" method="POST" action="{{ route('profile.detail.update') }}" novalidate>
            @csrf
            @method('PUT')

            <div class="profile-card__head">
              <h3 class="profile-card__title"><i class="fas fa-id-card"></i> Kişisel Bilgiler</h3>
            </div>

            <div class="row g-3">
              <div class="col-md-12">
                <div class="form-floating">
                  <input type="text" class="form-control @error('name') is-invalid @enderror" id="account_name" name="name" placeholder="Ad Soyad" value="{{ old('name', $user->name) }}" required>
                  <label for="account_name">Ad Soyad</label>
                  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-floating">
                  <input type="email" class="form-control @error('email') is-invalid @enderror" id="account_email" name="email" placeholder="E-posta" value="{{ old('email', $user->email) }}" required>
                  <label for="account_email">E-posta Adresi</label>
                  @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>

            <div class="profile-form-section">
              <h5><i class="fas fa-key"></i> Şifre Değiştir</h5>
              <small>Şifrenizi değiştirmek istemiyorsanız bu alanları boş bırakın.</small>
            </div>

            <div class="row g-3">
              <div class="col-md-12">
                <div class="form-floating">
                  <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="account_current_password" name="current_password" placeholder="Mevcut şifre">
                  <label for="account_current_password">Mevcut şifre</label>
                  @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="account_new_password" name="new_password" placeholder="Yeni şifre">
                  <label for="account_new_password">Yeni şifre</label>
                  @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" id="account_confirm_password" name="new_password_confirmation" placeholder="Yeni şifre tekrar">
                  <label for="account_confirm_password">Yeni şifre tekrar</label>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
              <button type="submit" class="cm-btn cm-btn--primary"><i class="fas fa-save"></i> Değişiklikleri Kaydet</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </section>
</main>
<br>
@endsection
