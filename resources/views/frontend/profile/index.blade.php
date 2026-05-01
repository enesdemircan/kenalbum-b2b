@extends('frontend.master')

@section('content')
<main>
  <div class="mb-4 pb-4"></div>
  <section class="profile-shell container">

    <div class="profile-shell__head">
      <div>
        <h1 class="profile-shell__heading"><i class="fas fa-gauge"></i> Hesabım</h1>
        <p class="profile-shell__sub">Hesap özetiniz, hızlı erişim ve bakiye bilgileriniz.</p>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-3">
        @include('frontend.profile._sidebar', ['active' => 'dashboard'])
      </div>

      <div class="col-lg-9">
        @if(Auth::user()->roles->contains('id', 3) && Auth::user()->customer)
          <div class="profile-balance">
            <div class="profile-balance__icon"><i class="fas fa-wallet"></i></div>
            <div class="profile-balance__body">
              <strong>Firma Bakiyesi</strong>
              <em>₺{{ number_format(auth()->user()->customer->balance, 2, ',', '.') }}</em>
            </div>
          </div>
        @elseif(Auth::user()->roles->contains('id', 3) && !Auth::user()->customer)
          <div class="profile-empty">
            <i class="fas fa-triangle-exclamation"></i>
            <h4>Firma Atamanız Bulunmuyor</h4>
            <p>Sipariş verebilmek için lütfen yöneticiyle iletişime geçin.</p>
          </div>
        @endif

        <div class="profile-card">
          <div class="profile-card__head">
            <div>
              <h3 class="profile-card__title"><i class="fas fa-hand-wave"></i> Merhaba, {{ auth()->user()->name }}</h3>
              <p class="profile-card__sub">Hesap panelinizden son siparişlerinizi, adreslerinizi ve hesap detaylarınızı yönetebilirsiniz.</p>
            </div>
          </div>

          <div class="profile-quicklinks">
            <a href="{{ route('profile.orders') }}" class="profile-quicklink">
              <span class="profile-quicklink__icon"><i class="fas fa-bag-shopping"></i></span>
              <h4 class="profile-quicklink__title">Siparişlerim</h4>
              <p class="profile-quicklink__desc">Tüm siparişlerinizi görüntüleyin ve takip edin.</p>
            </a>
            <a href="{{ route('profile.addresses') }}" class="profile-quicklink">
              <span class="profile-quicklink__icon"><i class="fas fa-location-dot"></i></span>
              <h4 class="profile-quicklink__title">Adreslerim</h4>
              <p class="profile-quicklink__desc">Şirket ve müşteri adreslerinizi yönetin.</p>
            </a>
            <a href="{{ route('profile.detail') }}" class="profile-quicklink">
              <span class="profile-quicklink__icon"><i class="fas fa-user-pen"></i></span>
              <h4 class="profile-quicklink__title">Hesap Detayları</h4>
              <p class="profile-quicklink__desc">Bilgilerinizi ve şifrenizi güncelleyin.</p>
            </a>
            @if(auth()->user()->hasRole(3))
              <a href="{{ route('profile.personels') }}" class="profile-quicklink">
                <span class="profile-quicklink__icon"><i class="fas fa-id-badge"></i></span>
                <h4 class="profile-quicklink__title">Personellerim</h4>
                <p class="profile-quicklink__desc">Firma personellerinizi yönetin.</p>
              </a>
            @endif
          </div>
        </div>
      </div>
    </div>

  </section>
</main>
<br>
@endsection
