# KenAlbüm - GitHub ve cPanel Deployment Yapılandırması

## ✅ Tamamlanan İşlemler

### 1. GitHub Hazırlığı
- ✅ `.gitignore` güncellendi (güvenlik için)
- ✅ `.env.example` oluşturuldu (production template)
- ✅ `README.md` oluşturuldu
- ✅ `DEPLOYMENT.md` oluşturuldu
- ✅ `GITHUB-SETUP.md` oluşturuldu (detaylı rehber)
- ✅ `QUICKSTART.md` oluşturuldu (hızlı başlangıç)
- ✅ Git repository başlatıldı (`git init`)

### 2. GitHub Actions (Otomatik Deployment)
- ✅ `.github/workflows/deploy.yml` oluşturuldu
- ✅ FTP-based deployment yapılandırıldı
- ✅ SSH post-deployment komutları hazırlandı

### 3. cPanel Hazırlığı
- ✅ `public/.htaccess` güncellendi
- ✅ Güvenlik ayarları eklendi
- ✅ URL rewriting yapılandırıldı

### 4. Güvenlik
- ✅ `.env` dosyası gitignore'a eklendi
- ✅ `musteri.sql` dosyası gitignore'a eklendi
- ✅ Hassas dosyalar gitignore'a eklendi
- ✅ vendor/ ve node_modules/ hariç tutuldu

## 📋 Sıradaki Adımlar

### ADIM 1: GitHub Desktop ile Repository Oluşturma

1. GitHub Desktop'ı aç
2. File > Add Local Repository
3. Bu klasörü seç: `D:\Projeler-2025\KenAlbüm`
4. "Create a repository" seç
5. **Private** olarak işaretle
6. Create Repository

### ADIM 2: İlk Commit ve Push

1. Commit message yaz: "Initial commit: KenAlbüm project"
2. Commit to main
3. Publish repository (PRIVATE olarak)

### ADIM 3: cPanel Hazırlık

#### A) MySQL Veritabanı (cPanel)
```
Database: kenalbum_prod
User: kenalbum_user
Password: [güçlü şifre]
```

#### B) Subdomain (cPanel)
```
Subdomain: app.yourdomain.com
Document Root: /public_html/app/public
```

#### C) PHP 8.2+ Seç

#### D) .env Dosyası
```bash
cd ~/public_html/app
cp .env.example .env
nano .env  # düzenle
```

### ADIM 4: GitHub Actions Secrets (Opsiyonel)

GitHub > Settings > Secrets > Actions:
```
FTP_SERVER
FTP_USERNAME
FTP_PASSWORD
FTP_SERVER_DIR
SSH_HOST
SSH_USERNAME
SSH_PASSWORD
DEPLOY_PATH
```

### ADIM 5: İlk Deployment (SSH)

```bash
cd ~/public_html/app
composer install --no-dev
php artisan key:generate
php artisan storage:link
php artisan migrate --force
chmod -R 755 storage bootstrap/cache
php artisan config:cache
```

## 📚 Dokümantasyon

- **QUICKSTART.md** - Hızlı başlangıç (3 adım)
- **GITHUB-SETUP.md** - Detaylı GitHub ve deployment rehberi
- **DEPLOYMENT.md** - cPanel deployment detayları
- **README.md** - Proje dokümantasyonu

## ⚠️ ÖNEMLİ NOTLAR

1. **ASLA .env dosyasını GitHub'a yüklemeyin**
2. **Repository'yi PRIVATE yapın**
3. **cPanel'de Document Root'un sonuna /public ekleyin**
4. **Production'da APP_DEBUG=false olmalı**
5. **Her deployment öncesi veritabanı yedeği alın**

## 🎯 Hazırsınız!

GitHub Desktop ile commit ve push yapın. GitHub Actions otomatik olarak cPanel'e deploy edecek.

---

Tarih: 2026-02-13
Versiyon: 1.0
