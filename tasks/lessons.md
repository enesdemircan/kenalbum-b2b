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