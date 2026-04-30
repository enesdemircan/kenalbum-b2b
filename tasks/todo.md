# Sipariş Akışı V3 — Step Wizard + Order-Level File Upload

**Hedef:** Sipariş sayfasını step-by-step wizard'a dönüştür, dosya yüklemeyi cart-item seviyesinden order seviyesine taşı, extras popup'ını wizard step'i yap.

---

## Kullanıcı Net İstekleri

1. ✅ **Wizard step yapısı:** Ebat & Paket → Model → Kumaş → Sipariş Detayı → Ekstralar → Sipariş Özeti
2. ✅ **Extras popup KALDIRILACAK** — wizard'ın "Ekstralar" step'i olacak
3. ✅ **Cart-add seviyesinde dosya yükleme YOK** — sepete birden fazla ürün eklenebilsin, dosya istemiyoruz
4. ✅ **Tek ZIP for entire cart** — checkout anında müşteri tüm sepetin görsellerini tek ZIP'le yükler
5. ✅ **`orders.s3_zip` kolonu** — dosya order seviyesinde saklanır
6. ✅ **Carts tablosundaki `s3_zip` kolonu KALSIN** — eski veriler için, sadece yeni order'larda yazmıyoruz
7. ✅ **Çöp kodları temizle**

## Mevcut Durum (Inceleme Özeti)

- `create.blade.php` 2445 satır, tek-form
- `customization-section.blade.php` file/files type render var (silinecek)
- `customization_categories` tablosu type alanı: radio, hidden, checkbox, file, files, input, select
- `Order` tablosunda **s3_zip kolonu YOK** → migration gerek
- `extra-sales.blade.php` modal mevcut (kaldırılacak)
- `cleanupInvalidCartItems` — cart-seviyesi s3_zip guard (refactor edilecek)

---

## Açık Sorular — Onayını Bekliyor

### 1. Wizard Step Mapping — Nasıl Belirlenecek?

Sen 6 step verdin (Ebat & Paket, Model, Kumaş, Sipariş Detayı, Ekstralar, Sipariş Özeti). Bunlar customization_categories'le nasıl eşleşecek?

| Seçenek | Nasıl | Artısı | Eksisi |
|---|---|---|---|
| **A. Yeni `step_label` kolonu** | Migration ile `customization_categories.step_label` ekle, her kategori bir step'e ait. Adminden değiştirilebilir. | Esnek, ürün-bazlı farklılaştırılabilir | Migration + admin UI gerekiyor |
| **B. Hardcoded mapping (kategori adıyla)** | Code'da `'Ebat' → 'Ebat & Paket'`, `'Model' → 'Model'`, vb. | Hızlı, migration yok | Yeni kategori eklenince kod düzenleme gerekir |
| **C. Order-based grouping** | Category `order` 1-3 → step 1, 4-6 → step 2... | Migration yok, mekanik | Step adları ürün-bazlı netleşmez |

**Tavsiyem: A** — temiz çözüm, multi-product için sürdürülebilir.

→ **A / B / C ?**

### 2. Dosya Yükleme Zorunluluğu

Checkout'ta dosya zorunlu mu?

| Seçenek | Davranış |
|---|---|
| **A. Her zaman zorunlu** | Sepette ürün varsa ZIP yüklemeden checkout tamamlanmaz |
| **B. Sadece "dosya gerektiren" ürünler için zorunlu** | Sepette en az bir "fotoğraf gerektiren" ürün varsa zorunlu (örn. albüm). Sade ürünler (kalem, kutu) için isteğe bağlı. |
| **C. Hiç zorunlu değil** | Müşteri yüklemese de sipariş geçer |

**Tavsiyem: B** — esnek + iş kuralına uygun. Hangi ürünler "dosya gerektirir" tanımı için: ürün ana kategori ID'sine göre veya yeni bir `requires_files` kolonu.

→ **A / B / C ?**

### 3. R2 Object Key Stratejisi

Sipariş oluşturulmadan ÖNCE upload edilen dosya için:

| Seçenek | Akış |
|---|---|
| **A. Geçici key + rename** | Upload anında: `orders/temp/{user.id}/{timestamp}.zip`. Order create olunca CopyObject + Delete ile `orders/{order.id}/{order.order_number}.zip`'e taşı. |
| **B. Order önce oluştur, sonra upload** | Pending order oluştur, ID al, sonra `orders/{order.id}/...` key'e upload. Başarısız olursa order silinir. |
| **C. Final key direkt + reservation** | Frontend bir UUID üretir, `orders/uploads/{uuid}.zip` olarak yazılır, complete'te DB'ye kaydedilir. |

**Tavsiyem: A** — basit, mevcut R2DirectUploadController hafif değişikle çalışır.

→ **A / B / C ?**

### 4. Extras (Ekstralar) Step UX

Ekstralar adımında ürünler nasıl gösterilsin?

| Seçenek | UX |
|---|---|
| **A. Grid + miktar inputu** | Her ekstra ürün kart şeklinde, "Sepete Ekle (3)" gibi miktar seçimi |
| **B. Liste + checkbox** | Liste, her satır checkbox + miktar (isteğe bağlı) |
| **C. Atla butonu zorunlu** | "Devam et" demeden adlı ekstra seçimi yapamaz, mecbur "Hayır, geç" |

**Tavsiyem: A** — modern e-ticaret pattern. "İstemiyorum" butonu ile direkt sonraki step.

→ **A / B / C ?**

### 5. Customization File/Files Type — Tamamen Kaldırılsın mı?

`customization_categories.type` değerleri arasında `file` ve `files` var. Bunları kullanan kategorilerden ne yapalım?

| Seçenek | Davranış |
|---|---|
| **A. DB'den tamamen sil** | `type=file` veya `type=files` olan tüm kategoriler kaldırılır. Eski cart_items'taki notes JSON'u etkilenmez. |
| **B. Render etme, DB'de kalsın** | Type alanı kalsın ama wizard'da skip edilsin. Eski sistem ile uyumlu kalır. |
| **C. Type alanını dönüştür** | `file/files` type olanları `hidden` yapıp pasif et |

**Tavsiyem: B** — minimal impact, geri dönüş kolay.

→ **A / B / C ?**

---

## Mimari Karar Özeti (default)

| Konu | Karar |
|---|---|
| Wizard | Vanilla JS state machine, step show/hide, localStorage backup |
| Step mapping | (Onay 1'e göre) |
| File upload location | `cart.checkout` sayfasında, R2 direct multipart |
| File required | (Onay 2'ye göre) |
| R2 key | (Onay 3'e göre) |
| `orders.s3_zip` | Yeni migration ile string nullable |
| Old `carts.s3_zip` | Korunuyor, sadece yazma kapalı |
| Extras | Wizard step (Onay 4'e göre) |
| File/files customization | (Onay 5'e göre) |
| Cart cleanup | Order-seviyesinde guard (orders.s3_zip kontrolü) — cart-level cleanup kaldırılır |

---

## Phase 1 — Database Migrations

- [ ] `add_s3_zip_to_orders_table` — orders'a nullable string s3_zip kolonu ekle
- [ ] (Onay 1=A ise) `add_step_label_to_customization_categories_table` — step_label nullable string
- [ ] (Onay 1=A ise) Seed: mevcut kategorilerin step_label'larını set et

## Phase 2 — Backend: R2 Upload Refactor

- [ ] R2DirectUploadController:
  - `initiateOrder` endpoint — order için (cart_id yerine)
  - `completeOrder` endpoint — orders.s3_zip set
  - `abortOrder` endpoint — temp key cleanup
- [ ] R2UploadService:
  - `buildOrderTempKey($userId, $timestamp)` — `orders/temp/{userId}/{timestamp}.{ext}`
  - `buildOrderFinalKey($orderId, $orderNumber)` — `orders/{orderId}/{orderNumber}.{ext}`
  - `moveToFinal($tempKey, $finalKey)` — CopyObject + DeleteObject

## Phase 3 — Backend: CartController & Order Flow

- [ ] `CartController::add` — extras parametresini de işle (atomik olarak ekstra ürünleri de cart'a ekle)
- [ ] `CartController::complete` — önce R2 temp key kontrolü, sonra Order create, sonra R2 move-to-final + orders.s3_zip set
- [ ] `cleanupInvalidCartItems` — KALDIR (artık order-level guard var)
- [ ] `Cart::renameS3Zip` — KULLANILMIYOR artık (orders.s3_zip standalone)

## Phase 4 — Frontend: Wizard UI

- [ ] `create.blade.php` rewrite (modular):
  - Step indicator (üst bar)
  - Step container divs (her step bir div, JS show/hide)
  - Wizard state JS (vanilla)
  - Validation per step
  - Final "Sepete Ekle" butonu
- [ ] `customization-section.blade.php`:
  - file/files type render bloklarını kaldır
- [ ] `extras-step.blade.php` — yeni partial (wizard step için)
- [ ] `summary-step.blade.php` — yeni partial (özet step için)

## Phase 5 — Frontend: Checkout Page File Upload

- [ ] `checkout.blade.php`:
  - Dosya yükleme bölümü ekle (drag-drop opsiyonel)
  - R2 direct upload progress UI
  - "Siparişi Onayla" butonu sadece dosya yüklendiyse aktif (Onay 2=B ise koşullu)

## Phase 6 — Cleanup

- [ ] `create.blade.php`: handleZipFileUpload, handleFileUpload, uploadFilesToR2, uploadFileToR2, getAllFilesFromForm, r2CsrfToken, r2FormatBytes — kaldır (checkout'a gitti)
- [ ] `extra-sales.blade.php` modal — sil
- [ ] `FrontendController::extraSalesModal` method + route — sil
- [ ] CartController::add içinde `extra_sales` response key — kaldır

## Phase 7 — Test & Verify

- [ ] Tek ürünlü sipariş wizard akışı
- [ ] Çoklu ürünlü sepet → tek ZIP upload akışı
- [ ] Dosya yüklemeden checkout deneme (Onay 2=B ise sade ürün için ge çer mi?)
- [ ] R2 temp → final move
- [ ] Eski siparişler (carts.s3_zip dolu) hâlâ admin panelinde görünüyor mu

## Phase 8 — Commit & Push

- [ ] Mantıksal commit'lere böl (migrations, backend, frontend, cleanup)
- [ ] Anlamlı Türkçe commit mesajları
- [ ] Push origin main

---

## ✅ Açık Sorular — Cevaplandı (2026-04-30)

| # | Konu | Karar |
|---|---|---|
| 1 | Step mapping | **A** — `step_label` kolonu, ürün-bazlı dinamik |
| 2 | File required | **A** — her zaman zorunlu (sepette ürün varsa ZIP yüklemeden checkout geçmez) |
| 3 | R2 key | **A** — geçici `orders/temp/{user_id}/{ts}.zip` → CopyObject + Delete ile final `orders/{order.id}/{order_number}.zip` |
| 4 | Extras UX | **A** — grid + miktar inputu |
| 5 | File/files type | **B** — render skip, DB'de kalır (eski cart_items.notes etkilenmez) |

## Bonus: Step Mapping Stratejisi

Sen onayladın: **"customization'a bağlı dinamik"**. `step_label` kolonu her kategoride. Wizard step'leri ürün-bazlı dinamik üretilir. Albüm ürünü (mevcut DB):

| Kategori (DB) | Type | Yeni step_label seed |
|---|---|---|
| Ebat (id=1) | select | Ebat & Paket |
| Paket (id=5) | radio | Ebat & Paket |
| Kumaş (id=3) | radio | Kumaş |
| Renk (id=4) | radio | Kumaş |
| Albüm Üzerine Yazılacak Yazı (id=8) | input | Sipariş Detayı |
| Pvc Kalınlığı (id=13) | hidden | Sipariş Detayı |
| Not (id=14) | input | Sipariş Detayı |
| Extra Ürün (id=9) | checkbox | Sipariş Detayı |
| Kapak Fotoğrafı (id=11) | file | NULL (skip) |
| Albüm Fotoğrafları (id=12) | files | NULL (skip) |
| Görsel (id=15) | files | NULL (skip) |
| Adet (id=16) | files | NULL (skip) |

**Not:** Sen "Model" step'i listeledin ama DB'de "Model" kategorisi yok. Şimdilik o step gözükmeyecek (ürün customization'ında olmadığı için). Eğer ileride eklersen step otomatik gelir.

Step sıralaması: her step_label grubunun **en düşük `order` değerine** göre.

Sabit son 2 step:
- **Ekstralar** (extras_sales tablosundan, varsa)
- **Sipariş Özeti** (her zaman)

Implement başlıyorum.
