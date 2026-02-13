<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sipariş Durumu Güncellendi</title>
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
            background-color: #ffc107;
            color: #333;
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
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            background-color: #28a745;
            color: white;
            border-radius: 20px;
            font-weight: bold;
        }
        .order-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
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
        <h1>📦 Sipariş Durumu Güncellendi</h1>
        <p>Sipariş No: #{{ $order->order_number }}</p>
    </div>
    
    <div class="content">
        <h2>Merhaba {{ $order->user->name }},</h2>
        <p>Siparişinizin durumu güncellenmiştir.</p>
        
        <div class="order-details">
            <h3>📋 Sipariş Bilgileri</h3>
            <p><strong>Sipariş No:</strong> #{{ $order->order_number }}</p>
            <p><strong>Sipariş Tarihi:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
            <p><strong>Toplam Tutar:</strong> {{ number_format($order->total_price, 2) }} ₺</p>
            
            @if($previousStatus)
                <p><strong>Önceki Durum:</strong> <span class="status-badge">{{ $previousStatus->title }}</span></p>
            @endif
            
            <p><strong>Yeni Durum:</strong> <span class="status-badge">{{ $status->title }}</span></p>
            
            @if($status->desc)
                <p><strong>Açıklama:</strong> {{ $status->desc }}</p>
            @endif
        </div>
        
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