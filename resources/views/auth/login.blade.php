@extends('frontend.master')

@section('content')


<main>
    <div class="mb-4 pb-4"></div>
    <section class="login-register container">
      <h2 class="d-none">Giriş Yap ve Kayıt Ol</h2>
      <ul class="nav nav-tabs mb-5" id="login_register" role="tablist">
        <li class="nav-item" role="presentation">
          <a class="nav-link nav-link_underscore active" href="{{ route('login') }}">Giriş Yap</a>
        </li>
        <li class="nav-item" role="presentation">
          <a class="nav-link nav-link_underscore" href="{{ route('register') }}">Kayıt Ol</a>
        </li>
      </ul>
      <div class="tab-content pt-2" id="login_register_tab_content">
        <div class="tab-pane fade show active" id="tab-item-login" role="tabpanel" aria-labelledby="login-tab">
          <div class="login-form">
            <form name="login-form" class="needs-validation"  method="POST" action="{{ route('login') }}" novalidate>
                @csrf
              <div class="form-floating mb-3">
               
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <label for="customerNameEmailInput1">Email Adresi *</label>
              </div>
    
              <div class="pb-3"></div>
    
              <div class="form-floating mb-3">
               
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <label for="password">Şifre *</label>
              </div>
    
              <div class="d-flex align-items-center mb-3 pb-2">
                <div class="form-check mb-0">
                  <input name="remember" class="form-check-input form-check-input_fill" type="checkbox" value="" id="flexCheckDefault1">
                  <label class="form-check-label text-secondary" for="flexCheckDefault1">Beni Hatırla</label>
                </div>
                <a href="{{ route('password.request') }}" class="btn-text ms-auto">Şifremi Unuttum?</a>
              </div>
    
              <button class="btn btn-primary w-100 text-uppercase" type="submit">Giriş Yap</button>
    
              <div class="customer-option mt-4 text-center">
                <span class="text-secondary">Hesabınız yok mu?</span>
                <a href="{{ route('register') }}" class="btn-text js-show-register">Hesap Oluştur</a>
              </div>
            </form>
          </div>
        </div>
        <div class="tab-pane fade" id="tab-item-register" role="tabpanel" aria-labelledby="register-tab">
          
        </div>
      </div>
    </section>
  </main>
  <br>
  <br>
@endsection 