# 🚀 KenAlbüm - Quick Start Guide

Bu dosya, projenizi hızlıca GitHub'a yüklemek ve cPanel'e deploy etmek için hızlı başlangıç rehberidir.

## 📁 Oluşturulan Dosyalar

✅ `.env.example` - Production için örnek environment dosyası
✅ `.gitignore` - Güncellendi (güvenlik için)
✅ `README.md` - Proje dokümantasyonu
✅ `DEPLOYMENT.md` - cPanel deployment rehberi
✅ `GITHUB-SETUP.md` - Detaylı GitHub ve deployment rehberi
✅ `.github/workflows/deploy.yml` - Otomatik deployment için GitHub Actions
✅ `public/.htaccess` - Güncellendi (güvenlik ve yönlendirme)

## 🎯 Hızlı Başlangıç (3 Adım)

### 1️⃣ GitHub Desktop ile Repository Oluşturma

1. **GitHub Desktop**'ı açın
2. **File > Add Local Repository** veya `Ctrl+O`
3. Bu klasörü seçin: `D:\Projeler-2025\KenAlbüm`
4. **"Create a Repository"** seçeneğine tıklayın
5. Repository bilgilerini doldurun:
   - Name: `kenalbum`
   - Description: "KenAlbüm - Fotoğraf Albümü Yönetim Sistemi"
   - **Private** olarak işaretleyin
6. **Create Repository** butonuna tıklayın

### 2️⃣ İlk Commit ve Push

1. **Summary** (commit message) yazın:
   ```
   Initial commit: KenAlbüm project
   ```

2. **Commit to main** butonuna tıklayın

3. **Publish repository** butonuna tıklayın
   - ⚠️ **Keep this code private** kutucuğunu işaretleyin

4. GitHub'da açın ve kontrol edin:
   - `.env` dosyasının **OLMAD

IĞINI** doğrulayın
   - `vendor/` ve `node_modules/` klasörlerinin **OLMAD

IĞINI** doğrulayın

### 3️⃣ cPanel Hazırlığı

#### A) Veritabanı Oluşturma (cPanel > MySQL Databases)
```
Database Name: kenalbum_prod
User: kenalbum_user
Password: [güçlü bir şifre]
Privileges: ALL
```

#### B) Subdomain Oluşturma (cPanel > Subdomains)
```
Subdomain: app (veya istediğiniz isim)
Document Root: /public_html/app/public (ÖNEMLİ: /public eki)
```

#### C) PHP Ayarları (cPanel > Select PHP Version)
```
Version: PHP 8.2 veya 8.3
Extensions: xml, mbstring, curl, zip, gd, mysql, bcmath
```

## 🔐 GitHub Actions Secrets (Otomatik Deploy için)

GitHub repository'nizde: **Settings > Secrets and variables > Actions**

Eklenecek secrets:
```
FTP_SERVER: ftp.yourdomain.com
FTP_USERNAME: your-ftp-username
FTP_PASSWORD: your-ftp-password
FTP_SERVER_DIR: /public_html/app/

SSH_HOST: yourdomain.com
SSH_USERNAME: cpanel-username
SSH_PASSWORD: cpanel-password
SSH_PORT: 22
DEPLOY_PATH: /home/username/public_html/app
```

## 📝 cPanel'de İlk Kurulum (SSH/Terminal ile)

```bash
# Proje klasörüne git
cd ~/public_html/app

# Bağımlılıkları yükle
composer install --optimize-autoloader --no-dev

# .env dosyasını oluştur ve düzenle
cp .env.example .env
nano .env  # veya File Manager'dan düzenle

# APP_KEY oluştur
php artisan key:generate

# Storage link
php artisan storage:link

# Veritabanı migration
php artisan migrate --force
php artisan db:seed --class=RoleSeeder

# İzinler
chmod -R 755 storage bootstrap/cache

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## ⚙️ .env Ayarları (cPanel'de)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://app.yourdomain.com

DB_HOST=localhost
DB_DATABASE=username_kenalbum_prod
DB_USERNAME=username_kenalbum_user
DB_PASSWORD=your-database-password

# Mevcut .env'den kopyalanacaklar:
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
MAILJET_APIKEY=...
MAILJET_APISECRET=...
EVEREST_CARGO_AUTH=...
```

## ✅ Kontrol Listesi

### GitHub
- [ ] Repository private olarak oluşturuldu
- [ ] .env dosyası commit edilmedi
- [ ] vendor/ klasörü commit edilmedi
- [ ] İlk push başarılı

### cPanel
- [ ] Subdomain oluşturuldu
- [ ] Document root `/public` ile biter
- [ ] PHP 8.2+ seçildi
- [ ] MySQL veritabanı hazır
- [ ] .env dosyası yapılandırıldı
- [ ] Composer install yapıldı
- [ ] Migration'lar çalıştırıldı
- [ ] Site açılıyor

### GitHub Actions (Opsiyonel)
- [ ] Secrets eklendi
- [ ] Workflow testi yapıldı
- [ ] Otomatik deployment çalışıyor

## 🆘 Sorun mu Yaşıyorsunuz?

### GitHub Desktop'ta "Create Repository" görünmüyor
➡️ **Çözüm**: Klasör zaten bir Git repo'su. **File > New Repository** yerine doğrudan **Publish Repository** kullanın.

### .env dosyası GitHub'a yüklendi
➡️ **Çözüm**: 
```bash
git rm --cached .env
git commit -m "Remove .env from repository"
git push
```
Sonra GitHub'da dosyayı manuel silin.

### 500 Error (cPanel'de)
➡️ **Çözüm**:
```bash
php artisan config:clear
chmod -R 755 storage bootstrap/cache
```
Loglara bakın: `storage/logs/laravel.log`

### GitHub Actions çalışmıyor
➡️ **Kontrol edin**:
- Secrets doğru girilmiş mi?
- FTP/SSH bilgileri doğru mu?
- cPanel'de erişim açık mı?

## 📚 Detaylı Dokümantasyon

- **GITHUB-SETUP.md** - Adım adım GitHub ve deployment rehberi
- **DEPLOYMENT.md** - cPanel deployment detayları
- **README.md** - Proje genel bilgisi

## 🎉 Başarılar!

Her şey hazır! GitHub Desktop ile commit/push yapın, her push otomatik olarak cPanel'e deploy edilecek.

---

**Not**: Bu dosya sadece hızlı başlangıç içindir. Detaylı bilgi için diğer MD dosyalarını okuyun.
