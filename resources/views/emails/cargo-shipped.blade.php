@extends('emails.layout')

@section('title')
Sipariş Kargoya Verildi - #{{ $order->order_number }}
@endsection

@section('header_color', '#0288d1')

@section('header')
    <h1>Siparişiniz Kargoya Verildi!</h1>
    <p>Sipariş No: #{{ $order->order_number }}</p>
@endsection

@section('content')
    <h2>Merhaba {{ $order->customer_name }},</h2>
    <p>Siparişiniz başarıyla kargoya verildi ve yola çıktı!</p>
    
    <div class="email-card email-info">
        <h3 style="margin-top: 0;">Kargo Bilgileri</h3>
        <p><strong>Kargo Firması:</strong> <span style="font-weight: bold; color: #0288d1;">{{ $cargoName }}</span></p>
        <p><strong>Tarih:</strong> {{ now()->format('d.m.Y H:i') }}</p>
    </div>
    
    <div class="email-card">
        <h3 style="margin-top: 0;">Sipariş Bilgileri</h3>
        <p><strong>Sipariş No:</strong> {{ $order->order_number }}</p>
        <p><strong>Sipariş Tarihi:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
        <p><strong>Toplam Tutar:</strong> {{ number_format($order->total_price, 2) }} ₺</p>
    </div>
    
    <div style="background-color: #ffffff; padding: 16px; border: 2px dashed #0288d1; border-radius: 8px; margin: 16px 0; text-align: center;">
        <h4 style="margin: 0 0 8px 0;">Kargo Takip Barkodu</h4>
        <div style="font-family: monospace; font-size: 18px; font-weight: bold; color: #0288d1;">{{ $cargoBarcode }}</div>
        <small style="color: #757575;">Bu barkodu kargo firmasının web sitesinde kullanarak siparişinizi takip edebilirsiniz.</small>
    </div>
    
    <h3>Teslimat Adresi</h3>
    <p><strong>Ad Soyad:</strong> {{ $order->customer_name }}</p>
    <p><strong>Telefon:</strong> {{ $order->customer_phone }}</p>
    @if($order->city || $order->district)
        <p><strong>İl/İlçe:</strong> {{ $order->city ?? 'Belirtilmemiş' }} / {{ $order->district ?? 'Belirtilmemiş' }}</p>
    @endif
    @if($order->shipping_address)
        <p><strong>Adres:</strong> {{ $order->shipping_address }}</p>
    @endif
    
    <h3>Kargo Takibi</h3>
    <p>Kargo takibi için yukarıdaki barkodu kullanabilir veya aşağıdaki butona tıklayarak siparişinizi görüntüleyebilirsiniz.</p>
    
    <a href="{{ url(route('orders.show', $order->id)) }}" class="btn-material btn-info" style="background-color: #0288d1 !important;">Siparişi Görüntüle</a>
    
    <h3>İletişim</h3>
    <p>Siparişinizle ilgili herhangi bir sorunuz varsa, lütfen bizimle iletişime geçin.</p>
@endsection
