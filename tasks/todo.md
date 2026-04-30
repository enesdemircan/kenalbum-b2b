# R2 Direct Upload Migration — V2 (yeni base üstüne)

**Hedef:** Browser → R2 doğrudan multipart upload kur, mevcut iş kurallarını **bozmadan**.
**Sebep:** 300 MB+ dosyalarda Cloudflare timeout + chunk drop. Eski stale base ile çalışılan ilk implementasyon (`r2-attempt-1-backup` branch'inde duruyor) drop edildi, remote'taki 45+ commit ile uyumsuzdu.

---

## Mevcut Sistemde Korunacak Kritik İş Kuralları

| Kural | Yer | R2'de korunma stratejisi |
|---|---|---|
| **500 MB hard limit** | `create.blade.php::handleZipFileUpload` (1390), `handleFileUpload` (1460) | Frontend kontrol kalır + R2 initiate endpoint'inde server-side validation eklenir |
| **Upload tamamlanmadan sipariş engelle** | `CartController` checkout/complete guard'ları (s3_zip NULL kontrolü) | s3_zip alanı yine R2 URL ile dolacak — guard otomatik çalışır |
| **Orphan cart cleanup** | `CartController::cleanupInvalidCartItems` (762) | s3_zip NULL kontrolü aynı şekilde çalışır — değişiklik gerekmez |
| **`is_required` zorunlu kategori guard** | `CartController` checkout (commit 6801f2d) | DB validation aynı, R2 ile alakasız |
| **Order numarası `ken-XXXXXXXXX`** | `Order::generateOrderNumber()` | Değişmiyor |
| **Cart_id rename at checkout** | `Cart::renameS3Zip($old, $new)` (313) — **ZIP'i indirip içindeki klasör adını değiştirip yeniden upload ediyor** | ⚠️ **KARAR GEREKİYOR** — aşağıdaki Açık Soru #1'e bak |
| **Admin download** | `Admin\OrderController::downloadCartFiles` — `redirect()->away($cart->s3_zip)` | R2 public URL ile aynı şekilde çalışır — değişiklik gerekmez ✅ |
| **PDF order detail in ZIP** | `CreateZipAndUploadToS3::generateOrderDetailPdf` | Server-side ZIP processing kalkacağı için bu da gider — ilgili PDF artık üretilmez (kullanıcı kararı 2026-04-30: "zip yapmayalım") |

---

## Mimari Karar Özeti

| Konu | Karar |
|---|---|
| Storage | Cloudflare R2 (`kenb2b` bucket) — yeni `r2` disk, eski `s3` disk legacy için kalır |
| Upload yöntemi | S3 Multipart Upload + Presigned URLs |
| Object key | `orders/{cart.id}/{file_index}_{cart.id}.{ext}` (cart'ın primary id'si, slug değil) |
| Server-side ZIP | KALDIRILIYOR — `CreateZipAndUploadToS3` job ve `ChunkUploadController` siliniyor |
| Server-side merge | KALDIRILIYOR — multipart R2'de assemble |
| Cart_id rename at checkout | **Açık Soru #1'e bağlı** — varsayılan: R2 object KEY rename yap (S3 CopyObject + Delete), ZIP içeriği dokunulmaz |
| Public URL kullanımı | Devam — `R2_PUBLIC_LINK` (kullanıcı 2026-04-30 onayı) |

---

## ✅ Açık Sorular — Cevaplandı (2026-04-30)

- **1. Cart_id rename:** **A** — R2 object KEY'i CopyObject + Delete ile yeniden adlandır. ZIP içeriği dokunulmaz.
- **2. R2 disk:** Yeni `r2` disk, eski `s3` disk legacy için kalır.
- **3. PDF order detail:** Kaldır. Admin panelden manuel alıyor.
- **+ Anti-manipülasyon:** JS'e güvenmeden server-side defenses (500 MB hard, throttle, cart ownership, key prefix, HeadObject size check, file_index range).

### Object Key Formatı
```
orders/{cart.id}/{cart.cart_id}.{ext}              (file_index = 0)
orders/{cart.id}/{cart.cart_id}-{idx}.{ext}        (file_index > 0)
```
- **Path:** `cart.id` (numeric, collision-free)
- **Filename:** `cart.cart_id` (slug — admin indirince kim/ne olduğunu görüyor)
- Checkout'ta cart_id slug değişince R2 CopyObject + DeleteObject

## Açık Sorular — onayını bekliyor

### 1. Cart_id rename at checkout — ne yapalım?

**Mevcut davranış:** Müşteri `/products/order/7` sayfasında ZIP yükler → cart oluşur → S3'e `zips/{cart_id}/{cart_slug}.zip` olarak yazılır. Müşteri checkout yaptığında `Order::generateOrderNumber()` ile `ken-000000123` üretilir → cart_id slug yeniden hesaplanır → **`Cart::renameS3Zip` çağrılır**, ZIP S3'ten indirilir, içindeki klasör adı `eski-cart_id` → `yeni-cart_id` olarak değiştirilir, yeni isimle yeniden upload edilir.

**R2 direct upload ile bu davranışı sürdürmenin 3 yolu var:**

| Seçenek | Nasıl | Artısı | Eksisi |
|---|---|---|---|
| **A. ZIP içeriğine dokunma, sadece R2 object key'i rename et** | Checkout'ta S3 `CopyObject` + `DeleteObject` ile R2'de obje yeni isme kopyalanır | ⚡ Hızlı (sunucu transferi yok), mantıklı | ZIP içindeki kök klasör adı **eski cart_id** kalır — admin extract ederken eski klasör adını görür |
| **B. ZIP'i R2'den indir, içeriği güncelle, yeniden upload et** | Mevcut davranışın tam kopyası | ✅ Eski sistemle birebir | 🐌 300 MB ZIP için checkout timeout riski + sunucu memory yükü (R2 download + repack + upload) |
| **C. Hiç rename yapma — cart_id slug DB'de değişir ama R2 dosyası primary id ile kalır** | `Cart::renameS3Zip` artık no-op | 🧹 En basit | Admin R2 console'da dosyayı `cart.id`-sayısıyla görür, slug ile değil |

**Önerim:** **A (Object key rename, ZIP içeriği dokunulmaz).**
- Hızlı (S3 CopyObject milisaniye)
- ZIP içindeki klasör adı admin için pratikte önemli değil — admin extract ederken `7-Zip → "Extract to folder named after archive"` ile zaten yeni isim altına açar (önceki konuşmada üzerinde anlaşmıştık)
- DB'deki s3_zip URL'i otomatik güncellenir (yeni key → yeni public URL)

**Cevabın:** A / B / C ?

### 2. R2 disk konfigürasyonu — yeni mi, mevcut mi?

Mevcut `s3` disk `Storage::disk('s3')` Spaces'e bağlı. R2 için yeni `r2` disk eklemek vs `s3` disk'ini R2'ye taşımak.

**Önerim:** **Yeni `r2` disk ekle**, eski `s3` disk Spaces'e bağlı kalsın.
- Geçmiş upload'larla işlem yapan kodlar (`Cart::deleteAssociatedFiles`, vb.) bozulmaz
- Yeni upload'lar `disk('r2')` kullanır
- İleride Spaces içeriği boşalınca `s3` disk silinebilir

Onaylıyor musun?

### 3. PDF order detail — gerçekten kaldırılıyor mu?

Mevcut sistem ZIP içine `siparis-detay-{cart_id}.pdf` ekliyor (admin için faydalı bilgi: müşteri bilgileri, ürün, customization, fiyat).

**Üç seçenek:**
- **A. Tamamen kaldır** — admin DB'den / admin paneli üstünden bilgileri görür (önceki kararın bu yöndeydi)
- **B. R2'ye ayrı obje olarak yaz** — checkout'ta PDF üret, `orders/{cart.id}/order-detail.pdf` olarak R2'ye yükle
- **C. On-demand stream** — admin "PDF indir" tıkladığında runtime'da generate edilir, hiç saklamaz

**Önerim:** **A (kaldır).** Sade. Eğer admin gerçekten ihtiyaç duyarsa C ileride eklenir.

Onaylıyor musun?

---

## Phase 0 — Hazırlık & Setup

- [ ] `.env`'e R2 credential'ları ekle (R2_ACCESS_KEY_ID, R2_SECRET_ACCESS_KEY, R2_BUCKET, R2_ENDPOINT, R2_PUBLIC_LINK)
- [ ] `.env.example`'e R2_* placeholder'ları ekle
- [ ] **Kullanıcı action:** R2 bucket'ında CORS policy yapılandır (`ExposeHeaders: ["ETag"]` zorunlu)
- [ ] **Kullanıcı action:** İş bitince mevcut R2 secret key'i revoke + yenisini oluştur (konuşma logu güvenliği)

## Phase 1 — Backend: R2 Disk Config & Service

- [ ] `config/filesystems.php`'e `r2` disk ekle (eski `s3` disk dokunulmaz)
- [ ] `app/Services/R2UploadService.php` oluştur
  - `initiateMultipart`, `presignPartUrls`, `completeMultipart`, `abortMultipart`
  - `copyObject($oldKey, $newKey)` — Cart_id rename için (Açık Soru #1 = A ise)
  - `deleteObject($key)`
  - (Backup branch `r2-attempt-1-backup`'tan bu kodun büyük kısmı kullanılabilir)

## Phase 2 — Backend: R2DirectUploadController + Validation

- [ ] `app/Http/Controllers/R2DirectUploadController.php` oluştur
  - 3 endpoint: `initiate`, `complete`, `abort`
  - **500 MB hard limit** validation: `file_size <= 524288000`
  - Cart ownership + `ensure.customer` middleware
  - Initiate → presigned PUT URL'ler (10 MB part, 1h TTL)
  - Complete → `cart->s3_zip` güncelle (virgülle ayrılmış URL listesi, multi-file desteği için)
  - Abort → R2 multipart cleanup
- [ ] `routes/web.php`: 3 yeni route, **eski 5 chunk route'u sil**
  - `POST /upload/r2/initiate`
  - `POST /upload/r2/complete`  
  - `POST /upload/r2/abort`

## Phase 3 — Cart::renameS3Zip — R2 uyumlu hale getir (Açık Soru #1'e göre)

- [ ] **Eğer A:** Method'u R2 CopyObject + Delete kullanacak şekilde yeniden yaz
  - Eski URL'den key çıkar → yeni cart_id ile yeni key oluştur → R2'de copy+delete → cart->s3_zip yeni URL ile güncelle
- [ ] **Eğer B:** Mevcut ZIP-içerik-rewrite mantığını R2 disk üzerinde çalışacak şekilde uyarla (download + ZipArchive + upload)
- [ ] **Eğer C:** Method'u no-op yap (sadece DB'de cart_id slug güncelle)

## Phase 4 — Frontend: Direct Upload Implementation

- [ ] `resources/views/frontend/orders/create.blade.php`
  - `uploadFilesWithChunks` → `uploadFilesToR2` ile değiştir
  - `uploadFileInChunks` / `uploadChunkWithRetry` / `uploadChunk` / `mergeAllFiles` / `createZipFile` / `startZipStatusPollingSilent` — sil
  - Yeni: `uploadFileToR2` — initiate → 3 paralel part PUT → complete (backup branch'ten kod alınabilir)
  - **500 MB frontend limit** mantığı (`handleZipFileUpload`, `handleFileUpload`) **dokunulmaz** — zaten doğru çalışıyor
  - **Polling akışı kaldırılır** — R2 complete sonrası direkt success / extra-sales modal
  - Çağıran satır (line ~1273) → `uploadFilesToR2`'a güncelle

## Phase 5 — Cleanup

- [ ] `app/Http/Controllers/ChunkUploadController.php` — sil
- [ ] `app/Jobs/CreateZipAndUploadToS3.php` — sil (çağıran tek yer ChunkUploadController'dı)
- [ ] `storage/app/public/chunks/`, `merged/`, `zips/` — varsa temizle
- [ ] Diğer dosyalardaki ölü referanslar grep ile kontrol → temizle

## Phase 6 — Verification

- [ ] `php -l` syntax check tüm değişen PHP dosyalarına
- [ ] `composer dump-autoload`
- [ ] `php artisan optimize:clear`
- [ ] `php artisan route:list` → 3 yeni R2 route + eski chunk route yok
- [ ] R2UploadService smoke test (tinker ile buildKey + publicUrl)
- [ ] Frontend JS `node --check` (bladе extract)
- [ ] **Manuel test (kullanıcıda):** 50 MB / 300 MB / 1 GB upload, abort, yetki kontrolü

## Phase 7 — Commit & Push

- [ ] Stage edilecek dosyaları sırala (Bash `git status` ile)
- [ ] Tek anlamlı commit (Türkçe başlık + body, neden + risk + kullanıcı action)
- [ ] `git push origin main` — push permission'ı `enesdemircan` hesabına geçildikten sonra

## Phase 8 — Post-deploy (kullanıcıda)

- [ ] cPanel'de `.env` güncellenir (R2 credentials)
- [ ] `composer dump-autoload && php artisan optimize:clear` production'da
- [ ] R2 bucket CORS policy
- [ ] Backup branch `r2-attempt-1-backup` test sonrası silinir

---

## Review Bölümü (2026-04-30 itibariyle V2 tamamlandı)

### Yapılan Değişiklikler

**Yeni Dosyalar:**
- `app/Services/R2UploadService.php` — multipart helpers + `headObject` (size validation) + `copyObject` + `renameSlugInKey` (cart_id rename) + `extractKeyFromUrl`
- `app/Http/Controllers/R2DirectUploadController.php` — 3 endpoint (initiate/complete/abort) + 500 MB hard validation + cart ownership + key prefix check + `MAX_FILE_INDEX` (max 10 dosya/cart) + HeadObject post-upload size enforcement

**Değiştirilen Dosyalar:**
- `.env` — R2 credentials (gitignored, local'de zaten vardı)
- `.env.example` — R2_* placeholder'lar
- `config/filesystems.php` — yeni `r2` disk (eski `s3` Spaces disk dokunulmadı)
- `routes/web.php` — eski 5 chunk route silindi, 3 yeni R2 route + `throttle:30,1` initiate'te + `throttle:60,1` complete'te
- `resources/views/frontend/orders/create.blade.php` — chunk sistemi (~600 satır) silindi, R2 direct multipart (~245 satır) + `csrfToken`/`r2FormatBytes` helpers ile değiştirildi
- `app/Models/Cart.php` — `renameS3Zip` artık R2 CopyObject + DeleteObject ile object KEY'i yeniden adlandırıyor (sunucu transferi yok), `ZipArchive` import kaldırıldı

**Silinen Dosyalar:**
- `app/Http/Controllers/ChunkUploadController.php`
- `app/Jobs/CreateZipAndUploadToS3.php`

### Object Key Formatı

```
orders/{cart.id}/{cart.cart_id}.{ext}              (file_index = 0)
orders/{cart.id}/{cart.cart_id}-{idx}.{ext}        (file_index > 0)
```

- **Path:** `cart.id` (numeric, primary key — collision-free, stable)
- **Filename:** `cart.cart_id` (slug — admin indirince anlamlı: kim/ne olduğu belli)
- Slug sanitize ediliyor (`sanitizeKeySegment`) — ASCII + `._-` dışındakiler `-`'ye dönüşür
- Checkout'ta cart_id slug değişince R2 CopyObject + DeleteObject

### Korunan İş Kuralları (mevcut sistemle uyumlu)

- ✅ **500 MB hard limit** — frontend (`handleZipFileUpload`/`handleFileUpload`) + backend (`MAX_FILE_BYTES` validation + post-upload `HeadObject` size check w/ 5% tolerance)
- ✅ **Upload tamamlanmadan sipariş engelleme** — `cart->s3_zip` NULL kontrolü `CartController` checkout/complete'te zaten var, R2 complete s3_zip'i dolduruyor
- ✅ **Orphan cart cleanup** — `cleanupInvalidCartItems` aynı şekilde çalışır (s3_zip NULL kontrolü)
- ✅ **`is_required` zorunlu kategori guard** — `CartController` checkout DB validation'ı R2 ile alakasız
- ✅ **Order numarası `ken-XXXXXXXXX`** — `Order::generateOrderNumber` dokunulmadı
- ✅ **Cart_id rename at checkout** — `Cart::renameS3Zip` R2 CopyObject ile yeniden adlandırıyor (ZIP içeriği dokunulmaz, kullanıcı kararı 2026-04-30: extract sırasında 7-Zip "Extract to folder" ile çözülür)
- ✅ **Admin download** — `Admin\OrderController::downloadCartFiles` zaten `redirect()->away($cart->s3_zip)` yapıyor, R2 public URL ile çalışır

### Anti-Manipülasyon Korumaları (kullanıcı isteği)

| Kontrol | Yer | Etki |
|---|---|---|
| **500 MB server-validation** | `initiate` validation `max:524288000` | Client manipule edemez |
| **500 MB post-upload check** | `complete` HeadObject size check (×1.05 tolerance, üzerinde DeleteObject) | Presigned URL ile büyük dosya yüklemeyi engeller |
| **File_index range** | `min:0|max:9` | Max 10 dosya/cart |
| **Rate limit** | `throttle:30,1` initiate, `throttle:60,1` complete | Brute force / abuse engelleme |
| **Cart ownership** | Her endpoint'te `cart.user_id == auth()->id()` | Başkasının cart'ına yükleme imkansız |
| **Key prefix** | Complete/abort'ta `orders/{cart.id}/` zorunlu + `..` reddi | Path traversal engelleme |
| **CSRF + auth + ensure.customer** | Route middleware | Standard Laravel guards + firma yetki kuralı |

### Verification Sonuçları

- ✅ Tüm değişen PHP dosyaları `php -l` syntax check geçti (5 dosya)
- ✅ `composer dump-autoload` temiz çalıştı (8880 sınıf)
- ✅ `php artisan optimize:clear` cache temizlendi
- ✅ `php artisan route:list --path=upload` 3 yeni R2 route'u gösteriyor; eski chunk route yok
- ✅ Tinker smoke test: `buildKey(7, '7-260430-test-firma-15x20-album', 0, 'zip')` → `orders/7/7-260430-test-firma-15x20-album.zip` ✓
- ✅ Tinker smoke test: file_index=2 ile `orders/7/7-260430-test-firma-15x20-album-2.zip` ✓
- ✅ Tinker smoke test: `extractKeyFromUrl(public_url)` doğru key dönüyor ✓
- ✅ Frontend JS `node --check` geçti (syntax hatası yok)
- ⚠️ **Manuel test gereken:** gerçek R2 bucket'a 50/300/1024 MB upload + checkout cart_id rename + abort akışı + yetki kontrolü

### Senin Yapman Gerekenler (Post-deploy)

1. **R2 Bucket CORS** — Cloudflare dashboard → R2 → kenb2b → Settings → CORS:
   ```json
   [{
     "AllowedOrigins": ["https://b2b.kenalbum.com.tr"],
     "AllowedMethods": ["PUT", "POST", "GET", "HEAD"],
     "AllowedHeaders": ["*"],
     "ExposeHeaders": ["ETag"],
     "MaxAgeSeconds": 3600
   }]
   ```
   **`ExposeHeaders: ["ETag"]` zorunlu** — yoksa browser ETag okuyamaz, complete fail.

2. **R2 Public Access** — bucket settings → public access açık (R2.dev subdomain).

3. **cPanel Production Deploy:**
   ```
   composer dump-autoload
   php artisan optimize:clear
   ```
   `.env` production'da R2 credentials ile güncellenmeli.

4. **Güvenlik:** Bu konuşmada paylaşılan R2 secret key'i Cloudflare'den revoke et, yenisini oluştur, .env'i güncelle.

5. **Backup branch:** Test sonrası `r2-attempt-1-backup` branch'i silinebilir (`git branch -D r2-attempt-1-backup`).

### Pre-existing Bug Notları (scope dışı)

- `Cart::deleteAssociatedFiles` (line 372): URL ile `Storage::disk('s3')->delete()` çağırıyor — buggy ama legacy, dokunulmadı.
