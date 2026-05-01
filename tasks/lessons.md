# Lessons Learned

Bu dosya kullanıcının düzeltmelerinden ve onayladığı non-trivial kararlardan çıkan örüntüleri tutar.
Her oturumda yeni iş başlamadan önce baştan oku.

---

## 2026-04-30 — Major refactor öncesi `git fetch origin` ile remote'u kontrol et

**Pattern:** Uzun süren bir feature branch / refactor çalışmasına başlamadan önce, lokal main'in remote'la senkron olduğunu doğrula. Lokal `git log` ile session başında gördüğün durum, çalışırken senin (veya başka makineden) commit'lerinle değişebilir.

**Why:** Bu projede session başında lokal main `5c4d6a1`'deydi. R2 implementasyonunu o base üstüne kurarken, kullanıcı paralel olarak başka makineden 45+ commit push etmişti (500MB hard limit, ken-XXXXXXXXX format, orphan cleanup, vb. iş kuralı eklemeleri). Push etmeye çalışınca conflict ortaya çıktı. Tüm iş baştan yapılmak zorunda kaldı (~700 satırlık iki şu refactor).

**How to apply:**
- Major refactor / feature işine başlamadan önce: `git fetch origin && git log HEAD..origin/main --oneline`
- Eğer remote'da farklı commit'ler varsa, **kullanıcıya sor**: "Bu commit'lerde neler var? Lokal başlamadan önce pull edip yeni base'e mi geçelim?"
- "Status: clean" görmek yeterli değil — base'in fresh olduğunu **fetch ile** doğrula.

---

## 2026-04-30 — Büyük JS bloğu silerken Edit tool ile dikkatli ol

**Pattern:** Büyük (500+ satır) ardışık bir kod bloğunu silmek/yeniden yazmak istediğinde, tek bir `Edit` çağrısında **partial old_string verme**. Eğer old_string sadece bloğun başını veya başını + kısmen içini kapsarsa, geri kalan satırlar dosyada kalır ve bozuk kod üretirsin (örn. dengesiz brace, hayalet fonksiyonlar, function-name-rename'li body'ler).

**Why:** Bu projede frontend chunk upload sistemini silerken iki kez aynı tuzağa düştüm: ilk Edit yalnızca giriş fonksiyonunun başını yakaladı, kalan 5 fonksiyonun gövdesi dosyada kaldı. Düzeltmek için ek 1-2 Edit gerekti.

**How to apply:** Büyük blok silmeden önce
1. `Read` ile bloğun **başını ve sonunu** doğrula (unique anchor satırlarını belirle, son fonksiyonun closing `}`'ı + ondan sonra gelen kept-block ilk satırı dahil)
2. Tek bir `Edit` ile `old_string`'i blok başından **sonuna kadar tam olarak** ver
3. Edit sonrası mutlaka `Grep` ile silinmiş olması gereken anahtar kelimelerin (fonksiyon adları, endpoint URL'leri, sabit isimler) gerçekten gittiğini **doğrula**
4. Eğer Edit'in old_string limiti aşarsa, blok'u **mantıksal alt-sınırlarda** ikiye böl — her alt-Edit kendi içinde bütün fonksiyonu kapsasın

---

## 2026-04-30 — Composer/Laravel cache: dosya silindikten sonra autoload + cache temizle

**Pattern:** PHP controller veya Job dosyası `rm` ile silindikten sonra, `composer dump-autoload` ve Laravel route/config/view cache temizliği yapılmazsa `php artisan route:list` gibi komutlar fail eder ("Class does not exist"). Production'a deploy ederken de aynı.

**Why:** Composer'in `vendor/composer/autoload_classmap.php` dosyası silinen sınıfı hâlâ map'te tutuyor. Laravel route cache'i de silinen controller'a referans tutabilir.

**How to apply:** Bir Controller / Job / Service sınıfı silindiğinde:
```
composer dump-autoload
php artisan optimize:clear
```
Production'da: deploy script'inin sonunda mutlaka bu adım olmalı.

---

## 2026-05-01 — İki paralel sistem (legacy AJAX + yeni chain filter) duplicate ID üretir

**Pattern:** Aynı domain'de iki farklı yaklaşım birlikte koşturursa (örn. legacy AJAX cascade child loading + yeni pre-rendered chain filter), her iki sistem aynı parametreyi DOM'a inject ediyorsa **duplicate id="..." attribute'ları oluşur**. Browser `<label for="...">` çağrısı `getElementById()`'ye düşer, o da DOM'daki ilk eşleşeni döner — kullanıcının tıkladığı görsel kart başka bir gizli element ile bağlanmış olabilir → click hiçbir şey yapmıyor gibi görünür.

**Why:** Bu projede cascade child kategorileri (Kumaş/Renk/Paket) hem yeni "ön-render + chain filter" wizard step'lerinde hem de eski jQuery `loadChildParameters` AJAX response'unun gizli `.child-parameters-container`'ında **aynı `id="param_X"` ile** mevcuttu. Kart label'ının `for=` attribute'u DOM'daki ilkine (eski legacy hidden element) bağlanıyordu → görünür kart radio'su asla check edilmiyordu → validation "seçim yapmadın" diyordu.

**How to apply:**
1. Bir parça kodu refactor ederken **paralel/yedek/legacy yolu işleyişini bilerek bırakma**. Ya tamamen kaldır ya da ondan üretilecek DOM çıktısını kontrolünde tut (örn. immediately empty inject olan container).
2. **Duplicate ID kontrolü** için browser DevTools console'da `Array.from(document.querySelectorAll('[id]')).map(e=>e.id).filter((v,i,a)=>a.indexOf(v)!==i)` çalıştırılabilir.
3. Yeni bir feature ile eski mantığı değiştirirken, "geçici olarak ikisini birlikte koşturalım" kararı sonradan ciddi bug'lara yol açar. Migration'ı tamamla, eskiyi kaldır.
4. Cascade/dynamic UI sistemleri için **tek "source of truth"** kuralı uygula — birden fazla sistem aynı state'i yönetmesin.

**How to detect:**
- Kullanıcı "kart tıklıyorum ama seçilmiyor" derse → DevTools'ta o kartın label'ının `for=` attribute'u → karşılık gelen ID'yi `document.querySelectorAll('#paramX').length` ile saysın. > 1 ise duplicate var.

---

## 2026-05-01 — `param->id` ile `pivot->id` farkı: many-to-many durumunda HTML id duplicate olur

**Pattern:** Bir customization param'ı (örn. "Beyaz" rengi) farklı pivot'lar tarafından **birden fazla kez** referans edilebilir (farklı parent context'lerde). Eğer view'de `id="param_{{ $param->id }}"` kullanırsan, aynı param farklı pivot'lar tarafından çağrıldığında HTML'de duplicate id oluşur.

**Why:** Bu projede `customization_pivot_params` tablosu N:N pivot — her satır farklı bir parent altında aynı param'ı temsil edebilir. Render edilirken `$pivot->param->id` kullanmak yanılgı. Kullanılması gereken `$pivot->id` (her satır için unique).

**How to apply:** Pivot tablosu içinden render ederken HTML id'leri için **PIVOT row id'yi** kullan, param FK'sini değil.

```blade
{{-- YANLIŞ --}}
<input id="param_{{ $param->id }}" ...>
<label for="param_{{ $param->id }}" ...>

{{-- DOĞRU --}}
<input id="pivot_{{ $pivot->id }}" ...>
<label for="pivot_{{ $pivot->id }}" ...>
```

**How to verify:** Server-side render testi yaz:
```php
preg_match_all('/(?:^|\s)id="([^"]+)"/', $html, $matches);
$counts = array_count_values($matches[1]);
$dupes = array_filter($counts, fn($c) => $c > 1);
// $dupes 0 olmalı
```
Regex'te `\bid=` yerine `(?:^|\s)id=` kullan — `data-XYZ-id="..."` attribute'larıyla yanılma.

---

## 2026-05-01 — Görsel akış değişikliklerinde lint yetmez, sayfayı sayfayı gez

**Pattern:** Multi-step / wizard / breadcrumb / nav gibi **paylaşılan UI parçalarını** değiştirdiğinde, sadece syntax/lint/blade-compile/route-list ile "geriye dönük kontrol yaptım" deme. Bunlar kodun *çalıştığını* doğrular, akışın *anlamlı* olduğunu doğrulamaz.

**Why:** Bu projede A2 multi-step checkout (5 step) tamamlandı, ben "tüm regresyon testleri geçti" dedim. Ama `/cart` sayfasında hâlâ eski 3-step bar görünüyordu, `/cart/checkout` ise yeni 5-step. Aralarındaki kopukluk derleyici testleriyle yakalanmaz çünkü her iki dosya kendi başına çalışıyor. Kullanıcı canlıda `/cart`'a girince fark etti ve "bunu nasıl fark etmezsin?" dedi.

**How to apply:** Bir UI akışının bir adımını değiştirdiğinde, kontrol listesi şunu içermeli:
1. **Akıştaki her sayfayı user-flow sırasıyla gez** (örn. /cart → /cart/checkout → /orders/{id})
2. Step bar / breadcrumb / nav gibi **paylaşılan UI parçaları her sayfada görsel olarak tutarlı mı?**
3. Yeni component'in eski varyantına atıfta bulunan dosyaları `grep` ile bul: `grep -rn "checkout-steps\|step__item\|wizard-step"` — her birinin yeni akışla uyumlu olduğunu doğrula.
4. Yeni alanları kullanmayan eski view'lar (orders/show vs.) yeni alanlar null olduğunda hâlâ render oluyor mu?

**Sonuç:** "Tek dosya değiştirdim" yanılsamasına kapılma. Aynı bileşenin (örn. `checkout-steps`, `cm-step`) referans alındığı tüm yerleri bul ve her birinin yeni akışla uyumlu olduğunu doğrula. Görsel tutarlılık ≠ kod doğruluğu.