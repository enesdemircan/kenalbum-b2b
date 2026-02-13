# KenAlbüm - Deployment Guide

Bu döküman, KenAlbüm projesinin cPanel'e deploy edilmesi için gerekli adımları içerir.

## 📋 Gereksinimler

- cPanel erişimi (SSH erişimi önerilir)
- MySQL veritabanı
- PHP 8.2 veya üzeri
- Composer
- Git
- Node.js & npm (frontend build için)

## 🚀 cPanel'e Deployment Adımları

### 1. cPanel Hazırlığı

#### a) Veritabanı Oluşturma
```
MySQL Databases bölümünden:
- Yeni veritabanı oluştur
- Yeni kullanıcı oluştur
- Kullanıcıya tüm yetkiler ver
- Veritabanı bilgilerini not al
```

#### b) PHP Versiyonunu Ayarla
```
Select PHP Version veya MultiPHP Manager:
- PHP 8.2+ seç
- Gerekli extension'ları aktif et:
  ✓ php-xml
  ✓ php-mbstring
  ✓ php-curl
  ✓ php-zip
  ✓ php-gd
  ✓ php-mysql
  ✓ php-bcmath
```

### 2. Git ile Projeyi Clone Etme

#### SSH ile (Önerilen):
```bash
cd ~/public_html/your-subdomain
git clone https://github.com/YOUR-USERNAME/kenalbum.git .
```

#### veya GitHub Actions ile otomatik deployment kurulumu için bu dosyayı kullanın.

### 3. Bağımlılıkları Yükleme

```bash
# Composer bağımlılıkları
composer install --optimize-autoloader --no-dev

# Frontend bağımlılıkları (eğer gerekiyorsa)
npm install
npm run build
```

### 4. Environment Ayarları

```bash
# .env dosyasını kopyala
cp .env.example .env

# APP_KEY oluştur
php artisan key:generate

# .env dosyasını düzenle (veritabanı, mail, S3 bilgileri)
nano .env
```

#### .env Düzenleme:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_HOST=localhost
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_pass

# Diğer ayarlar...
```

### 5. Laravel Kurulum Komutları

```bash
# Storage ve cache klasörlerine izin ver
chmod -R 755 storage bootstrap/cache

# Storage link oluştur
php artisan storage:link

# Veritabanı migration'larını çalıştır
php artisan migrate --force

# Cache'leri temizle ve oluştur
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Public Klasör Yönlendirmesi

#### a) Alt Domain için:
```
cPanel > Subdomains > Create Subdomain
- Document Root: /home/username/public_html/kenalbum/public
```

#### b) .htaccess Kontrolü:
`/public_html/your-subdomain/.htaccess` dosyası olmalı:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### 7. Cron Jobs (Opsiyonel)

Laravel Scheduler için:
```
* * * * * cd /home/username/public_html/kenalbum && php artisan schedule:run >> /dev/null 2>&1
```

### 8. Queue Worker (Opsiyonel)

Eğer queue kullanıyorsanız:
```bash
php artisan queue:work --daemon
```

## 🔄 Güncelleme Adımları

```bash
# Git'ten son değişiklikleri çek
git pull origin main

# Bağımlılıkları güncelle
composer install --optimize-autoloader --no-dev

# Migration'ları çalıştır
php artisan migrate --force

# Cache'leri temizle
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache'leri yeniden oluştur
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔒 Güvenlik

1. `.env` dosyasını asla commit etmeyin
2. `APP_DEBUG=false` production'da
3. `APP_ENV=production` ayarlayın
4. Güvenli şifreler kullanın
5. HTTPS kullanın (SSL sertifikası)

## 📝 Notlar

- **Storage izinleri**: `storage/` ve `bootstrap/cache/` klasörleri yazılabilir olmalı
- **Composer**: Eğer cPanel'de composer yoksa, local'de vendor klasörünü de yükleyebilirsiniz
- **Node modules**: production'da `node_modules` klasörüne gerek yok, sadece build'lenmiş dosyalar yeterli
- **Database backup**: Her deployment öncesi veritabanı yedeği alın

## 🆘 Sorun Giderme

### 500 Internal Server Error:
```bash
php artisan config:clear
chmod -R 755 storage bootstrap/cache
```

### Permission Denied:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Class not found:
```bash
composer dump-autoload
php artisan clear-compiled
```

## 📞 Destek

Sorun yaşarsanız:
1. Laravel log dosyalarını kontrol edin: `storage/logs/laravel.log`
2. cPanel error logs'u kontrol edin
3. `.env` dosyasının doğru olduğundan emin olun
