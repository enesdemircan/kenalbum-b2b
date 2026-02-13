@extends('emails.layout')

@section('title')
Sipariş Onayı - #{{ $order->order_number }}
@endsection

@section('header_color', '#388e3c')

@section('header')
    <h1>Siparişiniz Alındı!</h1>
    <p>Sipariş No: #{{ $order->order_number }}</p>
@endsection

@section('content')
    <h2>Merhaba {{ $order->user->name }},</h2>
    <p>Siparişiniz başarıyla alındı ve işleme alındı. Siparişinizin detayları aşağıda yer almaktadır.</p>
    
    <div class="email-card email-success">
        <h3 style="margin-top: 0;">Sipariş Detayları</h3>
        <p><strong>Sipariş Tarihi:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
        <p><strong>Toplam Tutar:</strong> {{ number_format($order->total_price, 2) }} ₺</p>
        <p><strong>Ödeme Yöntemi:</strong> {{ ucfirst($order->payment_method) }}</p>
        
        <h4 style="margin: 16px 0 8px 0;">Sipariş Edilen Ürünler</h4>
        @foreach($order->cartItems as $item)
            <div style="border-bottom: 1px solid #e0e0e0; padding: 10px 0;">
                <strong>{{ $item->product->title }}</strong><br>
                <small style="color: #757575;">Adet: {{ $item->quantity }} | Fiyat: {{ number_format($item->price, 2) }} ₺</small>
            </div>
        @endforeach
        
        <p style="font-weight: bold; font-size: 18px; color: #388e3c; margin: 16px 0 0 0;">Toplam: {{ number_format($order->total_price, 2) }} ₺</p>
    </div>
    
    <h3>Teslimat Bilgileri</h3>
    <p><strong>Ad Soyad:</strong> {{ $order->user->name }}</p>
    <p><strong>Telefon:</strong> {{ $order->customer_phone }}</p>
    <p><strong>İl/İlçe:</strong> {{ $order->city ?? 'Belirtilmemiş' }} / {{ $order->district ?? 'Belirtilmemiş' }}</p>
    <p><strong>Adres:</strong> {{ $order->shipping_address }}</p>
    
    <h3>İletişim</h3>
    <p>Siparişinizle ilgili herhangi bir sorunuz varsa, lütfen bizimle iletişime geçin.</p>
    
    <a href="{{ url(route('profile.orders')) }}" class="btn-material btn-success" style="background-color: #388e3c !important;">Siparişlerimi Görüntüle</a>
@endsection
