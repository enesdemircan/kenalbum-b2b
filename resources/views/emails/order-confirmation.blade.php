<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sipariş Onayı - #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .order-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .product-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .total {
            font-weight: bold;
            font-size: 18px;
            color: #28a745;
        }
        .footer {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 12px;
            color: #666;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Siparişiniz Alındı!</h1>
        <p>Sipariş No: #{{ $order->order_number }}</p>
    </div>
    
    <div class="content">
        <h2>Merhaba {{ $order->user->name }},</h2>
        <p>Siparişiniz başarıyla alındı ve işleme alındı. Siparişinizin detayları aşağıda yer almaktadır.</p>
        
        <div class="order-details">
            <h3>📋 Sipariş Detayları</h3>
            <p><strong>Sipariş Tarihi:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
            <p><strong>Toplam Tutar:</strong> {{ number_format($order->total_price, 2) }} ₺</p>
            <p><strong>Ödeme Yöntemi:</strong> {{ ucfirst($order->payment_method) }}</p>
            
            <h4>📦 Sipariş Edilen Ürünler:</h4>
            @foreach($order->cartItems as $item)
                <div class="product-item">
                    <strong>{{ $item->product->title }}</strong><br>
                    <small>Adet: {{ $item->quantity }} | Fiyat: {{ number_format($item->price, 2) }} ₺</small>
                </div>
            @endforeach
            
            <div class="total">
                <strong>Toplam: {{ number_format($order->total_price, 2) }} ₺</strong>
            </div>
        </div>
        
        <h3>🚚 Teslimat Bilgileri</h3>
        <p><strong>Ad Soyad:</strong> {{ $order->user->name }}</p>
        <p><strong>Telefon:</strong> {{ $order->customer_phone }}</p>
        <p><strong>İl/İlçe:</strong> {{ $order->city ?? 'Belirtilmemiş' }} / {{ $order->district ?? 'Belirtilmemiş' }}</p>
        <p><strong>Adres:</strong> {{ $order->shipping_address }}</p>
        
        <h3>📞 İletişim</h3>
        <p>Siparişinizle ilgili herhangi bir sorunuz varsa, lütfen bizimle iletişime geçin.</p>
        
        <a href="{{ route('profile.orders') }}" class="btn" style="color: white;">Siparişlerimi Görüntüle</a>
    </div>
    
    <div class="footer">
        <p>Bu e-posta Laravel + Mailjet entegrasyonu ile gönderilmiştir.</p>
        <p>© {{ date('Y') }} Your Company Name</p>
    </div>
</body>
</html> 