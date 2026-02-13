# cPanel Git™ Version Control ile Deployment

## 🚀 Çok Daha Kolay Yöntem!

cPanel'de Git™ Version Control özelliği varsa GitHub Actions'a gerek yok! Direkt cPanel'den yönetebilirsin.

---

## 📋 Adım Adım Kurulum

### 1️⃣ cPanel'de Git Version Control'ü Aç

1. cPanel'e giriş yap
2. **Files** bölümünden **Git™ Version Control** seçeneğini bul
3. Aç

### 2️⃣ Repository Oluştur veya Clone Et

#### Yöntem A: Mevcut Klasörü Repository Yap

1. **Create** butonuna tıkla
2. Ayarlar:
   - **Clone a Repository:** İşaretle
   - **Clone URL:** `https://github.com/enesdemircan/kenalbum-b2b.git`
   - **Repository Path:** `/home/username/public_html/app`
   - **Repository Name:** `kenalbum-b2b` (otomatik gelir)

3. **Create** butonuna tıkla

#### Yöntem B: GitHub Token ile (Önerilen - Private Repo için)

**GitHub'da Token Oluştur:**
1. GitHub'da: Settings > Developer settings > Personal access tokens > Tokens (classic)
2. **Generate new token (classic)**
3. İzinler:
   - ✅ `repo` (Full control of private repositories)
4. Token'ı kopyala (sadece bir kez gösterilir!)

**cPanel'de:**
- **Clone URL:** `https://YOUR-TOKEN@github.com/enesdemircan/kenalbum-b2b.git`
- Veya username ile: `https://enesdemircan:YOUR-TOKEN@github.com/enesdemircan/kenalbum-b2b.git`

### 3️⃣ İlk Pull ve Kurulum

Clone işlemi tamamlandıktan sonra:

```bash
# SSH ile bağlan
ssh username@yourdomain.com

# Proje klasörüne git
cd ~/public_html/app

# Bağımlılıkları yükle
composer install --optimize-autoloader --no-dev

# .env dosyasını oluştur
cp .env.example .env
nano .env  # veya File Manager'dan düzenle

# Gerekli ayarları yap
php artisan key:generate
php artisan storage:link
php artisan migrate --force
chmod -R 755 storage bootstrap/cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4️⃣ Document Root Ayarla

cPanel > Domains > Domains:
- Subdomain'ini bul
- **Manage** > **Document Root**
- Değiştir: `/home/username/public_html/app/public`
- Update

---

## 🔄 Güncelleme İşlemi (Süper Kolay!)

### GitHub Desktop'tan Push Yaptıktan Sonra:

1. **cPanel'e git**
2. **Git™ Version Control** aç
3. Repository'ni bul
4. **Manage** butonuna tıkla
5. **Pull or Deploy** sekmesine git
6. **Update from Remote** butonuna tıkla ✨

**Bu kadar!** Kodlar otomatik güncellenir.

### Veya SSH'dan (Daha Hızlı):

```bash
cd ~/public_html/app
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🤖 Otomatik Güncelleme (Deploy Script)

cPanel Git Version Control'de **Deploy Script** özelliği varsa:

### Deploy Script Oluştur

**Git™ Version Control > Manage > Deploy** sekmesinde:

```bash
#!/bin/bash

# Bağımlılıkları güncelle
/usr/local/bin/ea-php82 /opt/cpanel/composer/bin/composer install --no-dev --optimize-autoloader --no-interaction

# Migration
/usr/local/bin/ea-php82 artisan migrate --force

# Cache temizle ve oluştur
/usr/local/bin/ea-php82 artisan config:cache
/usr/local/bin/ea-php82 artisan route:cache
/usr/local/bin/ea-php82 artisan view:cache

# İzinler
chmod -R 755 storage bootstrap/cache

echo "Deployment tamamlandı!"
```

> **Not:** `/usr/local/bin/ea-php82` kısmı cPanel'deki PHP versiyonuna göre değişebilir. `ea-php82` veya `ea-php83` olabilir.

**Bu script sayesinde:** Her pull işleminden sonra otomatik çalışır!

---

## 🎯 Webhook ile Tam Otomatik (En Gelişmiş)

GitHub'dan push yaptığında otomatik pull yapması için:

### 1. cPanel'de Webhook URL'sini Al

Git™ Version Control > Manage:
- **Deploy** sekmesinde webhook URL'ini göreceksin
- Örnek: `https://yourdomain.com:2083/cpsess123456/cgi/webhook_autodeploy.pl?repository=kenalbum-b2b&key=abc123xyz`

### 2. GitHub'da Webhook Ekle

1. Repository'de: **Settings > Webhooks > Add webhook**
2. **Payload URL:** cPanel'den aldığın webhook URL
3. **Content type:** `application/json`
4. **Which events:** `Just the push event`
5. **Active:** ✅ İşaretle
6. **Add webhook**

**Artık GitHub'a her push yaptığında cPanel otomatik pull yapacak!** 🚀

---

## 📊 Karşılaştırma

### cPanel Git Version Control ✅ (Önerilen)
**Avantajlar:**
- ✅ Çok kolay ve hızlı
- ✅ cPanel arayüzünden yönetim
- ✅ Deploy script'i çalıştırma
- ✅ Webhook ile tam otomatik
- ✅ GitHub Actions secret'lerine gerek yok
- ✅ SSH bilgisi gerekmez (webhook ile)

**Dezavantajlar:**
- ⚠️ cPanel'de özellik olması gerekiyor
- ⚠️ Bazı hosting'lerde kısıtlı olabilir

### GitHub Actions
**Avantajlar:**
- ✅ Her yerden çalışır
- ✅ Karmaşık workflow'lar
- ✅ Test ve build işlemleri

**Dezavantajlar:**
- ⚠️ Secret'ler eklenmeli
- ⚠️ FTP/SSH bilgileri gerekli
- ⚠️ Daha karmaşık

---

## 🎬 Kullanım Senaryosu

### Günlük İş Akışı (cPanel Git ile):

1. **Kodda değişiklik yap**
2. **GitHub Desktop'tan commit ve push**
3. **cPanel'e git** (veya webhook varsa otomatik!)
4. **"Update from Remote" butonuna tıkla**
5. **Bitti!** ✨

Veya webhook kuruluysa:
1. **Kodda değişiklik yap**
2. **GitHub Desktop'tan commit ve push**
3. **Otomatik deploy olur!** 🎉

---

## 🛠️ Sorun Giderme

### "Authentication failed" hatası:

**Çözüm:** GitHub Personal Access Token kullan:
```
https://YOUR-GITHUB-TOKEN@github.com/enesdemircan/kenalbum-b2b.git
```

### Composer bulunamıyor:

**Deploy Script'te tam path kullan:**
```bash
/opt/cpanel/composer/bin/composer install
```

### PHP bulunamıyor:

**Deploy Script'te ea-php kullan:**
```bash
/usr/local/bin/ea-php82 artisan migrate
```

### İzin hataları:

```bash
chmod -R 755 storage bootstrap/cache
chown -R username:username storage bootstrap/cache
```

---

## ✅ Kurulum Checklist

- [ ] cPanel'de Git™ Version Control özelliği var
- [ ] GitHub Personal Access Token oluşturuldu
- [ ] cPanel'de repository clone edildi
- [ ] Composer install yapıldı
- [ ] .env dosyası oluşturuldu ve düzenlendi
- [ ] Migration'lar çalıştırıldı
- [ ] Document Root /public'e ayarlandı
- [ ] Deploy script eklendi (opsiyonel)
- [ ] GitHub webhook eklendi (tam otomasyon için)
- [ ] Test pull yapıldı
- [ ] Site çalışıyor

---

## 🎉 Sonuç

**cPanel Git™ Version Control kesinlikle daha kolay!**

GitHub Actions'ı silmene gerek yok (gelecekte kullanabilirsin) ama şimdilik cPanel Git kullan. Çok daha pratik ve hızlı! 🚀

**Önerilen Workflow:**
1. GitHub Desktop'tan push yap
2. cPanel'den "Update from Remote" tıkla (veya webhook ile otomatik)
3. Bitti! ✨
