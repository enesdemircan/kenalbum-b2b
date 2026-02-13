@extends('emails.layout')

@section('title', 'Sipariş Durumu Güncellendi')

@section('header_color', '#f57c00')

@section('header')
    <h1>Sipariş Durumu Güncellendi</h1>
    <p>Sipariş No: #{{ $order->order_number }}</p>
@endsection

@section('content')
    <h2>Merhaba {{ $order->user->name }},</h2>
    <p>Siparişinizin durumu güncellenmiştir.</p>
    
    <div class="email-card email-info">
        <h3 style="margin-top: 0;">Sipariş Bilgileri</h3>
        <p><strong>Sipariş No:</strong> #{{ $order->order_number }}</p>
        <p><strong>Sipariş Tarihi:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
        <p><strong>Toplam Tutar:</strong> {{ number_format($order->total_price, 2) }} ₺</p>
        
        @if($previousStatus)
            <p><strong>Önceki Durum:</strong> <span style="display: inline-block; padding: 4px 12px; background-color: #757575; color: white; border-radius: 16px; font-size: 12px;">{{ $previousStatus->title }}</span></p>
        @endif
        
        <p><strong>Yeni Durum:</strong> <span style="display: inline-block; padding: 4px 12px; background-color: #388e3c; color: white; border-radius: 16px; font-size: 12px;">{{ $status->title }}</span></p>
        
        @if($status->desc)
            <p><strong>Açıklama:</strong> {{ $status->desc }}</p>
        @endif
    </div>
    
    <h3>İletişim</h3>
    <p>Siparişinizle ilgili herhangi bir sorunuz varsa, lütfen bizimle iletişime geçin.</p>
    
    <a href="{{ url(route('profile.orders')) }}" class="btn-material btn-warning" style="background-color: #f57c00 !important;">Siparişlerimi Görüntüle</a>
@endsection
