@extends('emails.layout')

@section('title', 'Şifre Sıfırlama')

@section('header_color', '#d32f2f')

@section('header')
    <h1>Şifre Sıfırlama</h1>
    <p>Hesabınız için şifre sıfırlama talebi</p>
@endsection

@section('content')
    <h2>Merhaba {{ $user->name }},</h2>
    <p>Hesabınız için şifre sıfırlama talebinde bulundunuz. Eğer bu talebi siz yapmadıysanız, bu e-postayı görmezden gelebilirsiniz.</p>
    
    <div class="email-card email-warning">
        <strong>Güvenlik Uyarısı:</strong>
        <p style="margin: 8px 0 0 0;">Bu link sadece 60 dakika geçerlidir. Güvenliğiniz için şifrenizi hemen değiştirmenizi öneririz.</p>
    </div>
    
    <h3>Şifre Sıfırlama Linki</h3>
    <p>Aşağıdaki butona tıklayarak şifrenizi sıfırlayabilirsiniz:</p>
    
    <a href="{{ $resetUrl }}" class="btn-material btn-danger" style="background-color: #d32f2f !important;">Şifremi Sıfırla</a>
    
    <p>Eğer buton çalışmıyorsa, aşağıdaki linki tarayıcınıza kopyalayabilirsiniz:</p>
    <p style="word-break: break-all; background-color: #f5f5f5; padding: 12px; border-radius: 8px; font-size: 12px; color: #757575;">
        {{ $resetUrl }}
    </p>
    
    <h3>Yardım</h3>
    <p>Herhangi bir sorun yaşarsanız, lütfen bizimle iletişime geçin.</p>
@endsection
