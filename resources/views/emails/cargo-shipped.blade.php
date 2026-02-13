<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sipariş Kargoya Verildi - #{{ $order->order_number }}</title>
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
            background-color: #17a2b8;
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
        .cargo-info {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #17a2b8;
        }
        .order-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .barcode {
            background-color: #fff;
            padding: 15px;
            border: 2px dashed #17a2b8;
            border-radius: 5px;
            margin: 15px 0;
            text-align: center;
            font-family: monospace;
            font-size: 18px;
            font-weight: bold;
            color: #17a2b8;
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
            background-color: #17a2b8;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .cargo-company {
            font-weight: bold;
            color: #17a2b8;
        }
        .icon {
            font-size: 20px;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚚 Siparişiniz Kargoya Verildi!</h1>
        <p>Sipariş No: #{{ $order->order_number }}</p>
    </div>
    
    <div class="content">
        <h2>Merhaba {{ $order->customer_name }},</h2>
        <p>Siparişiniz başarıyla kargoya verildi ve yola çıktı! 🎉</p>
        
        <div class="cargo-info">
            <h3>📦 Kargo Bilgileri</h3>
            <p><span class="icon">🏢</span><strong>Kargo Firması:</strong> <span class="cargo-company">{{ $cargoName }}</span></p>
            <p><span class="icon">📅</span><strong>Tarih:</span> {{ now()->format('d.m.Y H:i') }}</p>
        </div>
        
        <div class="order-details">
            <h3>📋 Sipariş Bilgileri</h3>
            <p><strong>Sipariş No:</strong> {{ $order->order_number }}</p>
            <p><strong>Sipariş Tarihi:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
            <p><strong>Toplam Tutar:</strong> {{ number_format($order->total_price, 2) }} ₺</p>
        </div>
        
        <div class="barcode">
            <h4>🔍 Kargo Takip Barkodu</h4>
            <div>{{ $cargoBarcode }}</div>
            <small>Bu barkodu kargo firmasının web sitesinde kullanarak siparişinizi takip edebilirsiniz.</small>
        </div>
        
        <h3>📍 Teslimat Adresi</h3>
        <p><strong>Ad Soyad:</strong> {{ $order->customer_name }}</p>
        <p><strong>Telefon:</strong> {{ $order->customer_phone }}</p>
        @if($order->city || $order->district)
            <p><strong>İl/İlçe:</strong> {{ $order->city ?? 'Belirtilmemiş' }} / {{ $order->district ?? 'Belirtilmemiş' }}</p>
        @endif
        @if($order->shipping_address)
            <p><strong>Adres:</strong> {{ $order->shipping_address }}</p>
        @endif
        
        <h3>📱 Kargo Takibi</h3>
        <p>Kargo takibi için yukarıdaki barkodu kullanabilir veya aşağıdaki butona tıklayarak siparişinizi görüntüleyebilirsiniz.</p>
        
        <a href="{{ route('orders.show', $order->id) }}" class="btn" style="color: white;">Siparişi Görüntüle</a>
        
        <h3>📞 İletişim</h3>
        <p>Siparişinizle ilgili herhangi bir sorunuz varsa, lütfen bizimle iletişime geçin.</p>
    </div>
    
    <div class="footer">
        <p>Bu e-posta Laravel + Mailjet entegrasyonu ile gönderilmiştir.</p>
        <p>© {{ date('Y') }} {{ config('app.name') }}</p>
    </div>
</body>
</html> 