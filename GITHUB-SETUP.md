# GitHub Setup ve Deployment Rehberi

## 📚 İçindekiler
1. [GitHub Desktop ile Başlangıç](#github-desktop-ile-baslangiç)
2. [GitHub Repository Oluşturma](#github-repository-olusturma)
3. [İlk Commit ve Push](#ilk-commit-ve-push)
4. [GitHub Actions Secrets Ayarlama](#github-actions-secrets-ayarlama)
5. [cPanel Hazırlığı](#cpanel-hazirligi)
6. [Deployment](#deployment)
7. [Güncelleme İşlemleri](#guncelleme-islemleri)

---

## 🚀 GitHub Desktop ile Başlangıç

### 1. Repository Oluşturma

1. **GitHub Desktop'ı açın**
2. **File > Add Local Repository** veya **Ctrl+O**
3. Projenizin klasörünü seçin: `D:\Projeler-2025\KenAlbüm`
4. Eğer "This directory does not appear to be a Git repository" uyarısı alırsanız:
   - **"Create a repository"** butonuna tıklayın
   - Repository Name: `kenalbum`
   - Description: "KenAlbüm - Fotoğraf Albümü Yönetim Sistemi"
   - **Local path** doğru olduğundan emin olun
   - **Git ignore:** Laravel seçin (varsa)
   - **Initialize this repository with a README** - İŞARETLEMEYİN (README.md zaten var)
   - **Create Repository** butonuna tıklayın

### 2. Dosyaları Commit Etme

1. **Changes** sekmesinde tüm değişiklikleri göreceksiniz
2. Hassas dosyaların commit edilmediğinden emin olun:
   - ✅ `.env` (gitignore'da olmalı)
   - ✅ `vendor/` klasörü (gitignore'da olmalı)
   - ✅ `node_modules/` (gitignore'da olmalı)
   - ✅ `musteri.sql` (gitignore'da olmalı)

3. **Commit Message** yazın:
   ```
   Initial commit: KenAlbüm project setup
   
   - Laravel project structure
   - User and customer management
   - Product and order system
   - S3 integration
   - Mailjet integration
   - Cargo integration
   ```

4. **Commit to main** butonuna tıklayın

---

## 🌐 GitHub Repository Oluşturma

### 1. GitHub'da Yeni Repository

1. **GitHub Desktop'ta** üst menüden **Repository > Open on GitHub** seçin
2. Tarayıcıda GitHub açılacak, **"Publish repository"** sayfası gelecek
3. Repository ayarları:
   - **Name:** `kenalbum`
   - **Description:** "KenAlbüm - Fotoğraf Albümü ve Sipariş Yönetim Sistemi"
   - **Keep this code private:** ✅ İŞARETLEYİN (özel proje için)
   - **Publish Repository** butonuna tıklayın

### 2. Repository Doğrulama

1. Tarayıcınızda `https://github.com/YOUR-USERNAME/kenalbum` adresini açın
2. Dosyaların yüklendiğini kontrol edin
3. **⚠️ ÖNEMLİ:** `.env` dosyasının OLMAD

ĞINI kontrol edin

---

## 🔐 GitHub Actions Secrets Ayarlama

GitHub Actions'ın cPanel'e deploy edebilmesi için secrets (gizli bilgiler) eklemeniz gerekiyor:

### 1. GitHub Repository Settings

1. GitHub repository sayfanızda **Settings** sekmesine gidin
2. Sol menüden **Secrets and variables > Actions** seçin
3. **New repository secret** butonuna tıklayın

### 2. Eklenecek Secrets

Aşağıdaki secret'ları tek tek ekleyin:

#### FTP Bilgileri:
```
Name: FTP_SERVER
Value: ftp.yourdomain.com (cPanel'den alın)

Name: FTP_USERNAME
Value: your-ftp-username@yourdomain.com

Name: FTP_PASSWORD
Value: your-ftp-password

Name: FTP_SERVER_DIR
Value: /public_html/subdomain-klasoru/ (veya / ana domain için)
```

#### SSH Bilgileri (Opsiyonel ama önerilen):
```
Name: SSH_HOST
Value: yourdomain.com veya IP adresi

Name: SSH_USERNAME
Value: cpanel-kullanici-adiniz

Name: SSH_PASSWORD
Value: cpanel-sifreniz

Name: SSH_PORT
Value: 22 (genellikle)

Name: DEPLOY_PATH
Value: /home/username/public_html/subdomain-klasoru
```

### 3. cPanel FTP/SSH Bilgilerini Alma

#### FTP Bilgileri:
1. cPanel'e giriş yapın
2. **Files > FTP Accounts** bölümüne gidin
3. Bir FTP hesabı oluşturun veya mevcut olanı kullanın
4. **FTP Configuration** linkine tıklayarak bilgileri alın

#### SSH Bilgileri:
1. cPanel'de **Security > SSH Access** bölümüne gidin
2. SSH erişiminin aktif olduğundan emin olun
3. Host: cPanel domain'iniz veya sunucu IP'si
4. Username: cPanel kullanıcı adınız
5. Password: cPanel şifreniz
6. Port: Genellikle 22

---

## 🖥️ cPanel Hazırlığı

### 1. Subdomain Oluşturma

1. cPanel'e giriş yapın
2. **Domains > Subdomains** bölümüne gidin
3. **Create a Subdomain**:
   - **Subdomain:** `app` (veya istediğiniz isim)
   - **Domain:** yourdomain.com seçin
   - **Document Root:** `/public_html/app` (otomatik gelecek)
   - **Create** butonuna tıklayın

### 2. PHP Version Ayarlama

1. **Software > Select PHP Version** (veya MultiPHP Manager)
2. Subdomain'inizi seçin
3. **PHP 8.2** veya **8.3** seçin
4. Gerekli extension'ları aktif edin:
   - ✅ php82-php-xml
   - ✅ php82-php-mbstring
   - ✅ php82-php-curl
   - ✅ php82-php-zip
   - ✅ php82-php-gd
   - ✅ php82-php-mysql (veya mysqli)
   - ✅ php82-php-bcmath

### 3. MySQL Veritabanı Oluşturma

1. **Databases > MySQL Databases**
2. **Create New Database**:
   - Database Name: `kenalbum_prod`
   - **Create Database**

3. **Create New User**:
   - Username: `kenalbum_user`
   - Password: Güçlü bir şifre oluşturun
   - **Create User**

4. **Add User To Database**:
   - User: `kenalbum_user`
   - Database: `kenalbum_prod`
   - **ALL PRIVILEGES** seçin
   - **Make Changes**

5. **Veritabanı bilgilerini not alın:**
   ```
   DB_HOST=localhost
   DB_DATABASE=username_kenalbum_prod (tam adı)
   DB_USERNAME=username_kenalbum_user (tam adı)
   DB_PASSWORD=your-strong-password
   ```

### 4. .env Dosyası Hazırlama (cPanel'de)

1. **Files > File Manager** açın
2. Subdomain klasörüne gidin (`public_html/app`)
3. `.env.example` dosyasını bulun (GitHub Actions deploy ettikten sonra)
4. `.env.example` dosyasını kopyalayın ve `.env` olarak kaydedin
5. `.env` dosyasını düzenleyin:

```env
APP_NAME=KenAlbum
APP_ENV=production
APP_KEY=base64:... (php artisan key:generate ile oluşturulacak)
APP_DEBUG=false
APP_URL=https://app.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_kenalbum_prod
DB_USERNAME=username_kenalbum_user
DB_PASSWORD=your-database-password

# AWS/DigitalOcean Spaces
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_BUCKET=kenalbums
AWS_DEFAULT_REGION=fra1
AWS_ENDPOINT=https://fra1.digitaloceanspaces.com

# Mailjet
MAILJET_APIKEY=your-mailjet-key
MAILJET_APISECRET=your-mailjet-secret

# Mail
MAIL_MAILER=mailjet
MAIL_FROM_ADDRESS=info@kenalbum.com.tr
MAIL_FROM_NAME="KenAlbüm"

# Everest Cargo
EVEREST_CARGO_AUTH=your-cargo-auth
EVEREST_CARGO_EMAIL=your-cargo-email
EVEREST_CARGO_API_URL=https://webpostman.everestkargo.com/restapi/client

FILESYSTEM_DISK=s3
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

### 5. Terminal/SSH ile İlk Kurulum

SSH ile bağlanın:
```bash
ssh username@yourdomain.com
```

Proje klasörüne gidin:
```bash
cd public_html/app
```

Composer bağımlılıklarını yükleyin:
```bash
composer install --optimize-autoloader --no-dev
```

APP_KEY oluşturun:
```bash
php artisan key:generate
```

Storage link oluşturun:
```bash
php artisan storage:link
```

Migration'ları çalıştırın:
```bash
php artisan migrate --force
php artisan db:seed --class=RoleSeeder
```

İzinleri ayarlayın:
```bash
chmod -R 755 storage bootstrap/cache
```

Cache'leri oluşturun:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Document Root Ayarlama

Subdomain'in `public` klasörüne işaret etmesi gerekiyor:

1. **cPanel > Domains > Domains** bölümüne gidin
2. Subdomain'inizi bulun ve **Manage** butonuna tıklayın
3. **Document Root** kısmını değiştirin:
   - Eski: `/home/username/public_html/app`
   - Yeni: `/home/username/public_html/app/public`
4. **Update** butonuna tıklayın

---

## 🚀 Deployment

### İlk Deployment (GitHub Actions ile Otomatik)

1. **GitHub Desktop'ta:**
   - Değişikliklerinizi commit edin
   - **Push origin** butonuna tıklayın

2. **GitHub'da:**
   - Repository sayfanızda **Actions** sekmesine gidin
   - "Deploy to cPanel" workflow'unun çalıştığını göreceksiniz
   - İşlem tamamlanana kadar bekleyin (genellikle 2-5 dakika)

3. **cPanel'de Kontrol:**
   - File Manager'dan dosyaların yüklendiğini kontrol edin
   - SSH ile bağlanıp migration'ları çalıştırın (ilk sefer)

### Manuel Deployment (GitHub Actions olmadan)

Eğer GitHub Actions çalışmazsa manuel deployment:

1. **Local'de:**
   ```bash
   git pull origin main
   ```

2. **cPanel File Manager'da:**
   - Projeyi zip olarak yükleyin
   - Extract edin
   - veya FTP ile dosyaları yükleyin

3. **SSH'da:**
   ```bash
   cd public_html/app
   composer install --no-dev
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## 🔄 Güncelleme İşlemleri

### Kod Değişikliği Sonrası

1. **GitHub Desktop'ta:**
   ```
   - Changes'i gözden geçirin
   - Commit message yazın
   - Commit to main
   - Push origin
   ```

2. **Otomatik deploy** başlayacak (GitHub Actions)

3. **cPanel SSH'da** (eğer migration varsa):
   ```bash
   php artisan migrate --force
   ```

### Acil Düzeltme (Hotfix)

1. Değişikliği yapın
2. Commit ve push edin
3. GitHub Actions'ın tamamlanmasını bekleyin
4. cPanel'de cache'leri temizleyin:
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan cache:clear
   ```

---

## 🐛 Sorun Giderme

### 500 Internal Server Error

**SSH'da:**
```bash
cd public_html/app
php artisan config:clear
php artisan cache:clear
chmod -R 755 storage bootstrap/cache
tail -n 50 storage/logs/laravel.log
```

### Permission Denied

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R username:username storage bootstrap/cache
```

### Class Not Found

```bash
composer dump-autoload --optimize
php artisan clear-compiled
php artisan optimize
```

### GitHub Actions Fail

1. **GitHub > Actions** sekmesinde hatayı kontrol edin
2. Secrets'in doğru girildiğinden emin olun
3. cPanel'de SSH/FTP erişiminin açık olduğunu kontrol edin

---

## ✅ Checklist

### GitHub Hazırlık
- [ ] .gitignore doğru yapılandırıldı
- [ ] .env dosyası gitignore'da
- [ ] README.md oluşturuldu
- [ ] DEPLOYMENT.md oluşturuldu
- [ ] GitHub Desktop ile repository oluşturuldu
- [ ] İlk commit yapıldı
- [ ] GitHub'a push edildi
- [ ] Private repository olarak ayarlandı

### GitHub Actions
- [ ] Secrets eklendi (FTP, SSH)
- [ ] Workflow dosyası doğru yapılandırıldı
- [ ] İlk deployment test edildi

### cPanel Hazırlık
- [ ] Subdomain oluşturuldu
- [ ] PHP 8.2+ seçildi
- [ ] Gerekli PHP extension'ları aktif
- [ ] MySQL veritabanı oluşturuldu
- [ ] Veritabanı kullanıcısı oluşturuldu
- [ ] .env dosyası yapılandırıldı
- [ ] Composer install yapıldı
- [ ] APP_KEY oluşturuldu
- [ ] Storage link oluşturuldu
- [ ] Migration'lar çalıştırıldı
- [ ] İzinler ayarlandı
- [ ] Document root public/ olarak ayarlandı
- [ ] SSL sertifikası kuruldu (Let's Encrypt)

### Test
- [ ] Site açılıyor
- [ ] Login çalışıyor
- [ ] Veritabanı bağlantısı çalışıyor
- [ ] S3 upload çalışıyor
- [ ] Mail gönderimi çalışıyor

---

## 📞 Destek

Sorun yaşarsanız:
1. `storage/logs/laravel.log` dosyasını kontrol edin
2. cPanel error_log dosyasını kontrol edin
3. GitHub Actions loglarını inceleyin

---

**Hazırlayan:** KenAlbüm Development Team  
**Tarih:** 2026-02-13  
**Versiyon:** 1.0
