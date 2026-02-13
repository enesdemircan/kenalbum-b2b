<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Şifre Sıfırlama</title>
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
            background-color: #dc3545;
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
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
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
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
      
        <h1>🔐 Şifre Sıfırlama</h1>
        <p>Hesabınız için şifre sıfırlama talebi</p>
    </div>
    
    <div class="content">
        
        <h2>Merhaba {{ $user->name }},</h2>
        <p>Hesabınız için şifre sıfırlama talebinde bulundunuz. Eğer bu talebi siz yapmadıysanız, bu e-postayı görmezden gelebilirsiniz.</p>
        
        <div class="warning">
            <strong>⚠️ Güvenlik Uyarısı:</strong>
            <p>Bu link sadece 60 dakika geçerlidir. Güvenliğiniz için şifrenizi hemen değiştirmenizi öneririz.</p>
        </div>
        
        <h3>🔗 Şifre Sıfırlama Linki:</h3>
        <p>Aşağıdaki butona tıklayarak şifrenizi sıfırlayabilirsiniz:</p>
        
        <a href="{{ $resetUrl }}" class="btn" style="color: white;">Şifremi Sıfırla</a>
        
        <p>Eğer buton çalışmıyorsa, aşağıdaki linki tarayıcınıza kopyalayabilirsiniz:</p>
        <p style="word-break: break-all; background-color: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;">
            {{ $resetUrl }}
        </p>
        
        <h3>📞 Yardım</h3>
        <p>Herhangi bir sorun yaşarsanız, lütfen bizimle iletişime geçin.</p>
    </div>
    
    <div class="footer">
        <p>Bu e-posta Laravel + Mailjet entegrasyonu ile gönderilmiştir.</p>
        <p>© {{ date('Y') }} Your Company Name</p>
    </div>
</body>
</html> 