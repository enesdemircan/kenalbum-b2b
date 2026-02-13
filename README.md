# KenAlbüm

Laravel tabanlı fotoğraf albümü ve sipariş yönetim sistemi.

## 🚀 Özellikler

- Kullanıcı yönetimi (Administrator, Müşteri, Firma Yöneticisi rolleri)
- Firma (Customer) yönetimi
- Ürün kataloğu
- Sipariş yönetimi
- Sepet sistemi
- S3/DigitalOcean Spaces entegrasyonu
- Mail gönderimi (Mailjet)
- Kargo entegrasyonu (Everest)

## 📋 Gereksinimler

- PHP 8.2+
- MySQL 5.7+ / MariaDB 10.3+
- Composer
- Node.js & npm (frontend için)

## 🛠️ Kurulum

### 1. Projeyi klonlayın

```bash
git clone https://github.com/YOUR-USERNAME/kenalbum.git
cd kenalbum
```

### 2. Bağımlılıkları yükleyin

```bash
# PHP bağımlılıkları
composer install

# Frontend bağımlılıkları (opsiyonel)
npm install
npm run build
```

### 3. Environment dosyasını ayarlayın

```bash
cp .env.example .env
php artisan key:generate
```

`.env` dosyasını düzenleyerek veritabanı ve diğer ayarları yapın.

### 4. Veritabanını hazırlayın

```bash
php artisan migrate
php artisan db:seed --class=RoleSeeder
```

### 5. Storage link oluşturun

```bash
php artisan storage:link
```

### 6. Geliştirme sunucusunu başlatın

```bash
php artisan serve
```

Tarayıcınızda `http://localhost:8000` adresine gidin.

## 📦 Production Deployment

cPanel'e deployment için [DEPLOYMENT.md](DEPLOYMENT.md) dosyasına bakın.

## 🔐 Güvenlik

Güvenlik açıkları için lütfen bir issue açmayın. Doğrudan bizimle iletişime geçin.

## 📝 Lisans

Bu proje özel bir projedir.

## 🤝 Katkıda Bulunma

Pull request'ler memnuniyetle karşılanır.

## 📞 İletişim

- Website: https://kenalbum.com.tr
- Email: info@kenalbum.com.tr
