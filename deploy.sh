#!/bin/bash

echo "🚀 Deployment başlıyor..."

# Ana dizine git
cd /home/kentr/b2b.kenalbum.com.tr

# Composer bağımlılıklarını güncelle
echo "📦 Composer install..."
/usr/local/bin/ea-php82 /opt/cpanel/composer/bin/composer install --no-dev --optimize-autoloader --no-interaction

# Storage link kontrolü
echo "🔗 Storage link kontrol ediliyor..."
if [ ! -L public/storage ]; then
    echo "Storage link oluşturuluyor..."
    /usr/local/bin/ea-php82 artisan storage:link
else
    echo "Storage link zaten mevcut."
fi

# Site klasörü kontrolü
echo "📁 Site klasörü kontrol ediliyor..."
mkdir -p storage/app/public/site
chmod -R 755 storage/app/public/site

# Migration'ları çalıştır
echo "🗄️ Migration..."
/usr/local/bin/ea-php82 artisan migrate --force

# Cache'leri temizle
echo "🧹 Cache temizleniyor..."
/usr/local/bin/ea-php82 artisan config:clear
/usr/local/bin/ea-php82 artisan route:clear
/usr/local/bin/ea-php82 artisan view:clear
/usr/local/bin/ea-php82 artisan cache:clear

# Cache'leri yeniden oluştur
echo "✨ Cache oluşturuluyor..."
/usr/local/bin/ea-php82 artisan config:cache
/usr/local/bin/ea-php82 artisan route:cache
/usr/local/bin/ea-php82 artisan view:cache

# Optimize
echo "⚡ Optimize..."
/usr/local/bin/ea-php82 artisan optimize

# Storage izinleri
echo "🔐 İzinler ayarlanıyor..."
chmod -R 755 storage bootstrap/cache

echo "✅ Deployment tamamlandı!"
