<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hoş Geldiniz!</title>
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
            background-color: #007bff;
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
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
      <img src="{{ $siteSettings->logo }}" alt="KenAlbüm Logo" style="width: 100px; height: auto;">
    </div>
    
    <div class="content">
        <h2>Merhaba {{ $user->name }},</h2>
        <p>Albüm satış sistemimize hoş geldiniz! Hesabınız başarıyla oluşturuldu hesabınız onaylandıktan sonra artık sistemimizi kullanmaya başlayabilirsiniz.</p>
        
        <h3>📋 Hesap Bilgileriniz:</h3>
        <ul>
            <li><strong>E-posta:</strong> {{ $user->email }}</li>
            <li><strong>Ad Soyad:</strong> {{ $user->name }}</li>
            <li><strong>Kayıt Tarihi:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</li>
        </ul>
        
            
        <p>Herhangi bir sorunuz olduğunda bizimle iletişime geçebilirsiniz.</p>
        
       
    </div>
    
    <div class="footer">
        <p>Bu e-posta Laravel + Mailjet entegrasyonu ile gönderilmiştir.</p>
        <p>© {{ date('Y') }} KenAlbüm</p>
    </div>
</body>
</html> 