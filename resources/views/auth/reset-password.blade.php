@extends('frontend.master')

@section('content')
<main>
    <div class="mb-4 pb-4"></div>
    <section class="login-register container">
        <h2 class="d-none">Şifre Sıfırla</h2>
        <ul class="nav nav-tabs mb-5" id="login_register" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link nav-link_underscore" href="{{ route('login') }}">Giriş Yap</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link nav-link_underscore" href="{{ route('register') }}">Kayıt Ol</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link nav-link_underscore active" href="#">Şifre Sıfırla</a>
            </li>
        </ul>
        <div class="tab-content pt-2" id="login_register_tab_content">
            <div class="tab-pane fade show active" id="tab-item-reset" role="tabpanel">
                <div class="login-form">
                    <form name="reset-password-form" class="needs-validation" method="POST" action="{{ route('password.update') }}" novalidate>
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div class="form-floating mb-3">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $request->email) }}" required autofocus placeholder="E-posta Adresi">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <label for="email">E-posta Adresi *</label>
                        </div>

                        <div class="pb-3"></div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Yeni Şifre">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <label for="password">Yeni Şifre *</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Yeni Şifre (Tekrar)">
                            <label for="password_confirmation">Yeni Şifre (Tekrar) *</label>
                        </div>

                        <div class="pb-3"></div>

                        <button class="btn btn-primary w-100 text-uppercase" type="submit">Şifremi Sıfırla</button>

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
