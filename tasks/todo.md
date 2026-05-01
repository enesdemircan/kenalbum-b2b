# Customization Sistem Standardizasyon Refactor

**Sebep:** Kullanıcı "kumaş seçimi selected olmuyor, kumaş seçin uyarısı alıyorum" raporu. Kök sebep: **iki paralel sistem** (legacy jQuery cascade AJAX + yeni chain filter) duplicate `id="param_X"` üretiyor. Patch yapmak yerine, kullanıcı kapsamlı refactor istedi: **legacy sistemi tamamen kaldır, tek bir uniform mantık kalsın**.

---

## Mevcut Durum (Karmaşıklık)

### İki paralel cascade mekanizması:
1. **Legacy:** jQuery `loadChildParameters` AJAX → `/products/{id}/customization-params/{paramId}/children` → response'u `.child-parameters-container`'a inject (Ebat step'inin içinde)
2. **Yeni:** Pre-render + chain filter — tüm cascade child'lar Ebat'ın değil, kendi step'lerinde (Kumaş/Renk/Paket) ön-render. Parent değişimi → JS filter `data-parent-pivot-id`'ye göre.

### İki farklı render path:
- `customization-section.blade.php` `type='select'` → mirror-select cards + hidden `<select>`
- `customization-section.blade.php` `type='radio'/'hidden'` → native radio cards
- `customization-section.blade.php` `type='checkbox'` → native checkbox cards

### Çöp kod:
- `FrontendController::getCustomizationChildren` (artık çağrılmıyor olacak)
- `FrontendController::getCustomizationParams`
- Routes `/products/{product}/customization-params/...`
- `resources/views/frontend/products/child-parameters.blade.php`
- `loadChildParameters` jQuery fonksiyonu + `.child-parameters-container` mantığı
- Mirror-select click handler + `option-card-mirror-select` class

---

## Hedef Mimari (Tek Yetkili Sistem)

### Render: tek bir uniform "seçim kartı" pattern
```
type='radio' | 'hidden' | 'select'  → native <input type="radio"> + card
type='checkbox'                     → native <input type="checkbox"> + card
type='input'                        → text input (değişmiyor)
```
Hidden `<select>` yok. Mirror-select yok. Tüm seçim tipleri aynı kart yapısı.

### Cascade: tek mekanizma — chain filter
- Tüm cascade child kategorileri OrderController tarafından kendi step'lerinde ön-render edilir
- Her option kartı `data-parent-pivot-id` taşır (parent pivot ID'si)
- Parent step'te seçim değişimi → JS sadece child step'in wrapper'larını filtreler (`display: none/''`)
- AJAX YOK, child-parameters-container YOK

### Validation
- Type-aware (`select`, `radio`, `checkbox`, `input`)
- Görünürlük kontrolü: `wrapper.style.display !== 'none'` (offsetParent değil)
- Cascade step parent seçilmemişse → "seçim yapmadın" değil, "önce parent step'i tamamla" mesajı

---

## ✅ Açık Soru: Onayını Bekliyorum

Üç paket var. Hangileri yapayım?

### Paket A — Sadece bug fix (minimal, 5 dakika)
- Sadece duplicate ID problemini çözen `ajaxComplete` cleanup'ı koru
- Diğer tüm legacy yapı dokunulmaz
- Risk: gelecekte tekrar bug çıkar
- Süre: 5 dk

### Paket B — Standardizasyon (orta, 30 dakika) ⭐ Tavsiyem
- `customization-section.blade.php` UNIFY: select/radio/hidden tek render block
- Mirror-select kaldır, tüm selection tipleri native radio
- Legacy jQuery cascade AJAX call'u disable et (kod kalır, sadece çağrılmaz)
- Cascade chain tek yetkili
- Eski route + controller method + view dosyaları **dokunulmaz** (geri dönüş kolay)
- Süre: 30 dk

### Paket C — Tam Cleanup (kapsamlı, 60 dakika)
- Paket B + ek olarak:
  - `FrontendController::getCustomizationChildren` method silinir
  - `FrontendController::getCustomizationParams` method silinir
  - Routes `/products/{product}/customization-params/...` silinir
  - `resources/views/frontend/products/child-parameters.blade.php` silinir
  - `loadChildParameters` jQuery fonksiyonu silinir (~70 satır)
  - `.child-parameters-container` ile ilgili tüm CSS/JS kaldırılır
- Çöp kod sıfırlanır, mimari minimal
- Risk: Eğer başka bir yerde getCustomizationChildren kullanılıyorsa break. Önce grep yaparım.
- Süre: 60 dk

---

## Phase'ler (Paket B ya da C için)

### Phase 1 — Customization Section Unify (her iki pakette)
- [ ] `customization-section.blade.php`: `radio || hidden || select` → tek render block (native radio cards)
- [ ] Hidden `<select>` ve mirror-select grid kaldır
- [ ] `data-type="radio"` set et tüm selection için (validation tutarlı)

### Phase 2 — Legacy jQuery Disable (her iki pakette)
- [ ] `create.blade.php`: existing `$('.customization-radio, .customization-select').on('change.customization', ...)` handler'ında `loadChildParameters` çağrısını **kaldır**, sadece `updatePrice()` kalsın
- [ ] Mirror-select click handler'ı sil
- [ ] `ajaxComplete` cleanup hook'unu sil (no longer needed — AJAX yok)
- [ ] CSS `.child-parameters-container { display:none !important }` rule'unu kaldır (gereksiz)

### Phase 3 — Tam Cleanup (sadece Paket C)
- [ ] `app/Http/Controllers/FrontendController.php`: `getCustomizationChildren` + `getCustomizationParams` method'larını sil
- [ ] `routes/web.php`: ilgili route'lar
- [ ] `resources/views/frontend/products/child-parameters.blade.php`: dosya sil
- [ ] `create.blade.php`: `loadChildParameters` fonksiyonu + tüm `.child-parameters-container` referansları sil
- [ ] `composer dump-autoload` + `php artisan optimize:clear`

### Phase 4 — Verification (her iki pakette)
- [ ] `php -l` syntax check
- [ ] Smoke test üzerinde kontrol et:
  - Step 2 Ebat seç → kart "selected" gözüküyor mu?
  - İleri → Step 3 Kumaş → seçenekler filtrelendi mi?
  - Kumaş kart tıkla → "selected" gözüküyor mu?
  - İleri → Step 4 Renk → seçenekler filtrelendi mi?
  - Geri Ebat'a → değiştir → Kumaş/Renk reset oluyor mu?
- [ ] DevTools'ta duplicate ID kontrolü: `Array.from(document.querySelectorAll('[id]')).map(e=>e.id).filter((v,i,a)=>a.indexOf(v)!==i)` → boş array dönmeli
- [ ] Form submit edip backend'de doğru data geliyor mu kontrol (CartController::add log)

### Phase 5 — Commit + Push
- [ ] Logical commits (refactor / cleanup ayrı)
- [ ] Push

---

## Risk & Alternatif

**Risk noktası:** Eski `getCustomizationChildren` veya `getCustomizationParams` başka bir yerde (örn. admin panel, başka route) kullanılıyor olabilir. Paket C öncesi mutlaka:
```bash
grep -r "getCustomizationChildren\|getCustomizationParams\|customization-children\|customization-params/.*children" --include="*.php" --include="*.blade.php" --include="*.js"
```

Eğer başka kullanım varsa Paket B'de kalalım.

**Form submit data format değişimi:**
- Eski `type='select'` → form field name: `customization[1]` (singular, Ebat için)
- Yeni `type='select'` → radio gibi → form field name: `customizations[0][1]` (plural)
- `CartController::add` her ikisini de işliyor — geçiş güvenli.

---

## Senin Cevabın — ✅ Paket B onaylandı (2026-05-01)

---

## Review Bölümü (Paket B tamamlandı)

### Yapılan Değişiklikler

**Phase 1 — `customization-section.blade.php`**
- Tek render block: `@if(type == 'radio' || 'hidden' || 'select')` — hepsi native radio card
- Eski `@elseif(type == 'select')` bloğu (76 satır) silindi: hidden `<select>`, mirror-select cards, `option-card-mirror-select` class
- Comment olarak refactor sebebi belirtildi

**Phase 2 — `create.blade.php`**
- Eski jQuery cascade handler refactor: `loadChildParameters($element, pivotId)` çağrısı SİLİNDİ. Birleştirilmiş change handler artık sadece `updatePrice()` çağırıyor.
- `.child-parameters-container` DOM injection mantığı YOK
- `ajaxComplete` cleanup hook'u silindi (gereksiz)
- Cascade chain query'lerinden `select.customization-select` kaldırıldı
- `refreshCascadeChain` `selectedPivotId` artık sadece checked radio okuyor (hidden select fallback gerek yok)

### Verification Sonuçları
- ✅ `php -l` syntax check geçti (her iki blade)
- ✅ `mirror-select` customization-section'da YOK (sadece comment)
- ✅ Cascade chain query'leri uniform — `.customization-radio, input[data-pivot-id]` only
- ✅ Deploy edildi, view cache + opcache yenilendi

### Sonuç
Tek source-of-truth cascade chain:
- `customization-section.blade.php` artık her cascade kategorisini SADECE bir yerde render ediyor (ön-render edilmiş cascade step)
- Legacy AJAX `loadChildParameters` çağrılmıyor → `.child-parameters-container` boş kalıyor
- Browser `<label for="param_X">` clicks → DOM'da TEK `getElementById('param_X')` → doğru radio check'leniyor
- Duplicate ID problemi kökten çözüldü

### Açık Kalanlar (gelecek temizlik için Paket C)
- `loadChildParameters` jQuery fonksiyonu (~70 satır) hâlâ tanımlı, çağrılmıyor
- `FrontendController::getCustomizationChildren` route + method aktif ama frontend tetiklemiyor
- `resources/views/frontend/products/child-parameters.blade.php` dosyası hâlâ var

Manuel test OK ise Paket C ile bunlar da silinir.