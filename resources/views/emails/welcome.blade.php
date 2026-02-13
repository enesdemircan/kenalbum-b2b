@extends('emails.layout')

@section('title', 'Hoş Geldiniz!')

@section('header_color', '#1976d2')

@section('header')
    <h1>Hoş Geldiniz!</h1>
    <p>Hesabınız başarıyla oluşturuldu</p>
@endsection

@section('content')
    <h2>Merhaba {{ $user->name }},</h2>
    <p>Albüm satış sistemimize hoş geldiniz! Hesabınız başarıyla oluşturuldu. Hesabınız onaylandıktan sonra sistemimizi kullanmaya başlayabilirsiniz.</p>
    
    <div class="email-card email-success">
        <h3 style="margin-top: 0;">Hesap Bilgileriniz</h3>
        <ul style="margin: 0; padding-left: 20px;">
            <li><strong>E-posta:</strong> {{ $user->email }}</li>
            <li><strong>Ad Soyad:</strong> {{ $user->name }}</li>
            <li><strong>Kayıt Tarihi:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</li>
        </ul>
    </div>
    
    <p>Herhangi bir sorunuz olduğunda bizimle iletişime geçebilirsiniz.</p>
@endsection
