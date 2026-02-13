@extends('frontend.master')

@section('content')
<main>
    <div class="mb-4 pb-4"></div>
    <section class="login-register container">
      <h2 class="d-none">Giriş Yap ve Kayıt Ol</h2>
      <ul class="nav nav-tabs mb-5" id="login_register" role="tablist">
        <li class="nav-item" role="presentation">
          <a class="nav-link nav-link_underscore " href="{{ route('login') }}">Giriş Yap</a>
        </li>
        <li class="nav-item" role="presentation">
          <a class="nav-link nav-link_underscore active" href="{{ route('register') }}">Kayıt Ol</a>
        </li>
      </ul>
      <div class="tab-content pt-2" id="login_register_tab_content">
        <div class="tab-pane fade " id="tab-item-login" role="tabpanel" aria-labelledby="login-tab">
      
        </div>
        <div class="tab-pane fade show active" id="tab-item-register" role="tabpanel" aria-labelledby="register-tab">
          <div class="register-form">
            <form name="register-form" class="needs-validation" method="POST" action="{{ route('register') }}" novalidate>
                @csrf
           
              <div class="pb-3"></div>

              <div class="form-floating mb-3">
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <label for="name">Ad Soyad *</label>
              </div>

              <div class="form-floating mb-3">
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <label for="email">Email Adresi *</label>
              </div>

              <div class="form-floating mb-3">
                <input type="text" class="form-control @error('company_title') is-invalid @enderror" id="company_title" name="company_title" value="{{ old('company_title') }}" required>
                @error('company_title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <label for="company_title">Firma Ünvanı *</label>
              </div>

              <div class="form-floating mb-3">
                <input type="tel" class="form-control @error('company_phone') is-invalid @enderror" id="company_phone" name="company_phone" value="{{ old('company_phone') }}" required>
                @error('company_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <label for="company_phone">Firma Telefonu *</label>
              </div>

             
    
              <div class="pb-3"></div>
    
              <div class="form-floating mb-3">
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <label for="password">Şifre *</label>
              </div>

              <div class="form-floating mb-3">
                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <label for="password_confirmation">Şifre Tekrar *</label>
              </div>
    
              <div class="d-flex align-items-center mb-3 pb-2">
                <p class="m-0">Kişisel verileriniz bu web sitesinde deneyiminizi desteklemek, hesabınıza erişimi yönetmek ve gizlilik politikamızda açıklanan diğer amaçlar için kullanılacaktır.</p>
              </div>
    
              <button class="btn btn-primary w-100 text-uppercase" type="submit">Kayıt Ol</button>
            </form>
            <br>
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection 