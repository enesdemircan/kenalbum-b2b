@extends('frontend.master')

@section('content')
<main>
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">Hesap Detayları</h2>
      <div class="row">
        <div class="col-lg-3">
          <ul class="account-nav">
            <li><a href="{{ route('profile.index') }}" class="menu-link menu-link_us-s">DASHBOARD</a></li>
            <li><a href="{{ route('profile.orders') }}" class="menu-link menu-link_us-s">SİPARİŞLERİM</a></li>
            <li><a href="{{ route('profile.addresses') }}" class="menu-link menu-link_us-s">ADRESLERİM</a></li>
            <li><a href="{{ route('profile.detail') }}" class="menu-link menu-link_us-s menu-link_active">HESAP DETAYLARI</a></li>
            @if(auth()->user()->hasRole(1))
            <li><a href="{{ route('admin.dashboard') }}" class="menu-link menu-link_us-s">YÖNETİM PANELİ</a></li>
            @endif
            @if(auth()->user()->hasRole('Satış Müdürü'))
            <li><a href="{{ route('admin.customers.index') }}" class="menu-link menu-link_us-s">MÜŞTERİ LİSTESİ</a></li>
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
          <div class="page-content my-account__edit">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="my-account__edit-form">
              <form name="account_edit_form" class="needs-validation" method="POST" action="{{ route('profile.detail.update') }}" novalidate>
                @csrf
                @method('PUT')
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-floating my-3">
                      <input type="text" class="form-control @error('name') is-invalid @enderror" id="account_name" name="name" placeholder="Ad Soyad" value="{{ old('name', $user->name) }}" required>
                      @error('name')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                      <label for="account_name">Ad Soyad</label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-floating my-3">
                      <input type="email" class="form-control @error('email') is-invalid @enderror" id="account_email" name="email" placeholder="E-posta Adresi" value="{{ old('email', $user->email) }}" required>
                      @error('email')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                      <label for="account_email">E-posta Adresi</label>
                    </div>
                  </div>
               
                  <div class="col-md-12">
                    <div class="my-3">
                      <h5 class="text-uppercase mb-0">Şifre Değiştir</h5>
                      <small class="text-muted">Şifrenizi değiştirmek istemiyorsanız bu alanları boş bırakın.</small>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-floating my-3">
                      <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="account_current_password" name="current_password" placeholder="Mevcut şifre">
                      @error('current_password')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                      <label for="account_current_password">Mevcut şifre</label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-floating my-3">
                      <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="account_new_password" name="new_password" placeholder="Yeni şifre">
                      @error('new_password')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                      <label for="account_new_password">Yeni şifre</label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-floating my-3">
                      <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" id="account_confirm_password" name="new_password_confirmation" placeholder="Yeni şifre tekrar">
                      @error('new_password_confirmation')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                      <label for="account_confirm_password">Yeni şifre tekrar</label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="my-3">
                      <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <br>
<br>
@endsection