@extends('frontend.master')

@section('content')
<main>
    <div class="mb-4 pb-4"></div>
    <section class="login-register container">
        <h2 class="d-none">Şifremi Unuttum</h2>
        <ul class="nav nav-tabs mb-5" id="login_register" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link nav-link_underscore" href="{{ route('login') }}">Giriş Yap</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link nav-link_underscore" href="{{ route('register') }}">Kayıt Ol</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link nav-link_underscore active" href="{{ route('password.request') }}">Şifremi Unuttum</a>
            </li>
        </ul>
        <div class="tab-content pt-2" id="login_register_tab_content">
            <div class="tab-pane fade show active" id="tab-item-forgot" role="tabpanel">
                <div class="login-form">
                    <form name="forgot-password-form" class="needs-validation" method="POST" action="{{ route('password.email') }}" novalidate>
                        @csrf
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="E-posta Adresi">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <label for="email">E-posta Adresi *</label>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="pb-3"></div>

                        <button class="btn btn-primary w-100 text-uppercase" type="submit">Şifre Sıfırlama Linki Gönder</button>

                        <div class="customer-option mt-4 text-center">
                            <span class="text-secondary">Hesabınızı hatırladınız mı?</span>
                            <a href="{{ route('login') }}" class="btn-text">Giriş Yap</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>
<br>
<br>
@endsection
