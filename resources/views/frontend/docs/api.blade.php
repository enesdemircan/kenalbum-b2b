@extends('frontend.master')

@section('content')
<main>
  <div class="mb-4 pb-4"></div>
  <section class="apidocs container">

    <div class="apidocs__head">
      <h1 class="apidocs__title"><i class="fas fa-code"></i> KenAlbüm API Dökümantasyonu</h1>
      <p class="apidocs__sub">Bayi sistemleri için entegrasyon rehberi. Tüm endpoint'ler <code>https://kenalbum.com.tr/api</code> base URL'i altında çalışır ve auth gerektirenler Sanctum Bearer token kullanır.</p>
      <div class="apidocs__meta">
        <span><i class="fas fa-clock"></i> Son güncelleme: {{ now()->format('d.m.Y') }}</span>
        <span><i class="fas fa-key"></i> Auth: Sanctum + role <code>11</code></span>
        <span><i class="fas fa-server"></i> Base: <code>{{ url('/api') }}</code></span>
      </div>
    </div>

    <div class="row g-4">
      {{-- TOC sidebar --}}
      <div class="col-lg-3">
        <aside class="apidocs__toc">
          <h6>İçindekiler</h6>
          <ol>
            <li><a href="#start">Hızlı Başlangıç</a></li>
            <li><a href="#auth">Auth & Token</a></li>
            <li><a href="#orders">Orders</a></li>
            <li><a href="#carts">Carts</a></li>
            <li><a href="#products">Products</a></li>
            <li><a href="#customizations">Customizations</a></li>
            <li><a href="#errors">Hata Yanıtları</a></li>
            <li><a href="#changelog">Changelog</a></li>
          </ol>
        </aside>
      </div>

      <div class="col-lg-9">
        {{-- ============ HIZLI BAŞLANGIÇ ============ --}}
        <section id="start" class="apidocs__section">
          <h2><i class="fas fa-bolt"></i> Hızlı Başlangıç</h2>
          <p>1. Yöneticiden API erişim rolü (<code>role_id = 11</code>) atanmış bir kullanıcı isteyin.</p>
          <p>2. <a href="#auth-login">Login endpoint</a>'inden Bearer token alın.</p>
          <p>3. Tüm korumalı isteklerde <code>Authorization: Bearer {token}</code> header'ı gönderin.</p>
          <p>4. Sipariş için <strong>önce</strong> cart oluşturun, sonra <code>carts_ids</code> ile order yaratın.</p>
        </section>

        {{-- ============ AUTH ============ --}}
        <section id="auth" class="apidocs__section">
          <h2><i class="fas fa-key"></i> Auth & Token</h2>

          @include('frontend.docs._endpoint', [
            'method' => 'POST', 'path' => '/api/login', 'name' => 'api.login', 'auth' => false,
            'desc' => 'E-posta ve şifre ile token alır. Yalnızca <code>role_id = 11</code> sahibi kullanıcılar.',
            'request' => '{
  "email": "bayi@firma.com",
  "password": "***",
  "device_name": "ERP-Sistemim"
}',
            'response' => '{
  "success": true,
  "user": {"id": 12, "name": "Bayi Adı", "email": "..."},
  "token": "1|aBc...",
  "token_type": "Bearer"
}',
            'id' => 'auth-login'
          ])

          @include('frontend.docs._endpoint', [
            'method' => 'POST', 'path' => '/api/logout', 'auth' => true,
            'desc' => 'Mevcut token\'ı iptal eder.',
            'response' => '{"success": true, "message": "Başarıyla çıkış yapıldı"}'
          ])

          @include('frontend.docs._endpoint', [
            'method' => 'GET', 'path' => '/api/me', 'auth' => true,
            'desc' => 'Token sahibinin bilgilerini döndürür.',
            'response' => '{"success": true, "user": {"id": 12, "email": "...", "customer": {...}}}'
          ])
        </section>

        {{-- ============ ORDERS ============ --}}
        <section id="orders" class="apidocs__section">
          <h2><i class="fas fa-receipt"></i> Orders — Sipariş İşlemleri</h2>

          @include('frontend.docs._endpoint', [
            'method' => 'GET', 'path' => '/api/orders', 'auth' => true,
            'desc' => 'Tüm siparişleri sayfalı listeler. Query: <code>per_page</code> (default 15).',
            'response' => '{
  "success": true,
  "data": {
    "current_page": 1, "per_page": 15, "total": 42,
    "data": [{ "id": 1, "order_number": "ken-000000001", ... }]
  }
}'
          ])

          @include('frontend.docs._endpoint', [
            'method' => 'POST', 'path' => '/api/orders', 'auth' => true,
            'desc' => 'Yeni sipariş oluşturur. Cart\'lar önceden oluşturulmuş ve <code>order_id</code> = null olmalı.',
            'request' => '{
  "customer_name": "Ahmet",
  "customer_surname": "Yılmaz",
  "customer_phone": "05551234567",
  "city": "İstanbul",
  "district": "Kadıköy",
  "shipping_address": "Bağdat Cad. No:123",
  "payment_method": "bakiye",
  "carts_ids": [1234, 1235],

  "s3_zip": "https://pub-...r2.dev/orders/temp/abc.zip",
  "shipping_method_id": 1,
  "shipping_cost": 60.00,

  "billing_same_as_shipping": false,
  "billing_name": "Mehmet",
  "billing_surname": "Demir",
  "billing_phone": "02121234567",
  "billing_city": "İstanbul",
  "billing_district": "Şişli",
  "billing_address": "Cumhuriyet Cad. No:1",
  "billing_company": "ABC Ltd.",
  "billing_tax_no": "1234567890",

  "discount_amount": 0,
  "notes": "Acil!",
  "api_archive_code": "ERP-2026-0042"
}',
            'response' => '{
  "success": true,
  "message": "Sipariş başarıyla oluşturuldu",
  "data": {
    "id": 4276, "order_number": "ken-000007827",
    "total_price": "1260.00", "shipping_cost": "60.00",
    "billing_same_as_shipping": false,
    "shipping_method": {"id": 1, "title": "Aras Kargo", "price": "60.00"},
    "cart_items": [...]
  }
}'
          ])

          @include('frontend.docs._endpoint', [
            'method' => 'GET', 'path' => '/api/orders/{id}', 'auth' => true,
            'desc' => 'Sipariş detayını + tüm cart kalemlerini + customizations + kargo metodu döndürür.'
          ])

          @include('frontend.docs._endpoint', [
            'method' => 'PUT', 'path' => '/api/orders/{id}', 'auth' => true,
            'desc' => 'Sipariş bilgilerini günceller. <code>order_number</code> hariç tüm alanlar opsiyonel (sometimes).',
            'request' => '{
  "status": 1,
  "shipping_method_id": 2,
  "shipping_cost": 50.00,
  "s3_zip": "https://...r2.dev/orders/4276/ken-000007827.zip"
}'
          ])

          @include('frontend.docs._endpoint', [
            'method' => 'DELETE', 'path' => '/api/orders/{id}', 'auth' => true,
            'desc' => 'Siparişi siler.'
          ])

          @include('frontend.docs._endpoint', [
            'method' => 'GET', 'path' => '/api/orders/{order}/items', 'auth' => true,
            'desc' => 'Siparişe bağlı cart kalemlerini ürün ve customization detaylarıyla döndürür.'
          ])

          @include('frontend.docs._endpoint', [
            'method' => 'GET', 'path' => '/api/orders/{order}/carts', 'auth' => true,
            'desc' => 'Siparişe bağlı cart\'ları (alternatif format) — total_items + total_quantity bilgisi ile.'
          ])
        </section>

        {{-- ============ CARTS ============ --}}
        <section id="carts" class="apidocs__section">
          <h2><i class="fas fa-cart-shopping"></i> Carts — Sipariş Kalemleri</h2>

          @include('frontend.docs._endpoint', [
            'method' => 'GET', 'path' => '/api/carts', 'auth' => true,
            'desc' => 'Sayfalı cart listesi. Filtre: <code>user_id, product_id, order_id, status, barcode, per_page</code>.'
          ])

          @include('frontend.docs._endpoint', [
            'method' => 'POST', 'path' => '/api/carts', 'auth' => true,
            'desc' => 'Yeni cart kalemi oluşturur. <code>pivot_ids</code> ile customization\'lar otomatik <code>notes</code> JSON\'una serileştirilir.',
            'request' => '{
  "product_id": 7,
  "quantity": 1,
  "page_count": 24,
  "pivot_ids": [12, 45, 67],
  "album_text": "Kemal & Aylin · 13.09.2025",
  "order_note": "Albüm sırtı altın yaldız",
  "file": "https://pub-...r2.dev/orders/temp/zip.zip",

  "urgent_production": true,
  "design_service": "with_design"
}',
            'response' => '{
  "success": true,
  "data": {
    "id": 3041, "cart_id": "3041-...", "barcode": "...",
    "price": "850.00", "original_price": "1000.00",
    "notes": "{\"customizations\":{...},\"urgent_production\":true,\"design_service\":\"with_design\"}",
    "s3_zip": "https://..."
  }
}'
          ])

          <div class="apidocs__note">
            <strong>Notes JSON yapısı:</strong>
            <pre><code>{
  "customizations": {
    "{category_id}": {"type": "radio", "value": "{pivot_id}"},
    "{category_id}": {"type": "checkbox", "values": [...]}
  },
  "total_customization_price": "120.00",
  "order_note": "...",                  // opsiyonel
  "urgent_production": true,            // opsiyonel
  "design_service": "with_design"       // opsiyonel: with_design | self_design
}</code></pre>
          </div>

          @include('frontend.docs._endpoint', [
            'method' => 'GET', 'path' => '/api/carts/{id}', 'auth' => true,
            'desc' => 'Cart detayını + product + order + status_histories + current_status ile döndürür.'
          ])

          @include('frontend.docs._endpoint', [
            'method' => 'PUT', 'path' => '/api/carts/{id}', 'auth' => true,
            'desc' => 'Cart bilgilerini günceller. <code>s3_zip, urgent_status, tracking_url, cargo_barcode</code> dahil.'
          ])

          @include('frontend.docs._endpoint', [
            'method' => 'DELETE', 'path' => '/api/carts/{id}', 'auth' => true,
            'desc' => 'Cart\'ı siler. R2\'deki ZIP otomatik temizlenir.'
          ])

          @include('frontend.docs._endpoint', [
            'method' => 'GET', 'path' => '/api/carts/barcode/{barcode}', 'auth' => true,
            'desc' => 'Barkod ile cart bul (üretim tarama akışı).'
          ])

          @include('frontend.docs._endpoint', [
            'method' => 'PATCH', 'path' => '/api/carts/{id}/status', 'auth' => true,
            'desc' => 'Cart durumunu günceller (0=bekleyen, 1=işlemde, 2=teslim, 3=iptal).',
            'request' => '{"status": 1}'
          ])
        </section>

        {{-- ============ PRODUCTS ============ --}}
        <section id="products" class="apidocs__section">
          <h2><i class="fas fa-box"></i> Products — Ürünler</h2>

          @include('frontend.docs._endpoint', [
            'method' => 'GET', 'path' => '/api/products', 'auth' => true,
            'desc' => 'Tüm ürünleri sayfalı listeler. <em>Public alternatif: <code>/api/public/products</code></em>'
          ])

          @include('frontend.docs._endpoint', [
            'method' => 'GET', 'path' => '/api/products/{id}', 'auth' => true,
            'desc' => 'Ürün detayı + customization params. <em>Public: <code>/api/public/products/{id}</code></em>'
          ])

          @include('frontend.docs._endpoint', [
            'method' => 'GET', 'path' => '/api/products/{product}/customization-params', 'auth' => true,
            'desc' => 'Ürünün tüm customization kategorilerini ve pivot param\'larını döndürür. Frontend wizard step\'lerini bu listeyle çizebilirsiniz.'
          ])
        </section>

        {{-- ============ CUSTOMIZATIONS ============ --}}
        <section id="customizations" class="apidocs__section">
          <h2><i class="fas fa-sliders"></i> Customizations</h2>

          @include('frontend.docs._endpoint', [
            'method' => 'GET', 'path' => '/api/customization-pivot-params', 'auth' => true,
            'desc' => 'Tüm pivot param listesi.'
          ])

          @include('frontend.docs._endpoint', [
            'method' => 'GET', 'path' => '/api/customization-pivot-params/{id}', 'auth' => true,
            'desc' => 'Tek pivot param + parametre + kategori ilişkileri.'
          ])

          @include('frontend.docs._endpoint', [
            'method' => 'GET', 'path' => '/api/customization-pivot-params/{param}/children', 'auth' => true,
            'desc' => 'Cascade alt seçenekler. Renk → kumaş → paket gibi hiyerarşilerde kullanılır.'
          ])
        </section>

        {{-- ============ ERRORS ============ --}}
        <section id="errors" class="apidocs__section">
          <h2><i class="fas fa-triangle-exclamation"></i> Hata Yanıtları</h2>

          <div class="apidocs__errors">
            <div class="apidocs__error-row"><strong>200</strong> Başarılı</div>
            <div class="apidocs__error-row"><strong>201</strong> Oluşturuldu (POST)</div>
            <div class="apidocs__error-row"><strong>401</strong> Token yok / geçersiz</div>
            <div class="apidocs__error-row"><strong>403</strong> Rol yetkisiz (role_id != 11)</div>
            <div class="apidocs__error-row"><strong>404</strong> Kayıt bulunamadı</div>
            <div class="apidocs__error-row"><strong>422</strong> Validasyon hatası — <code>errors</code> alanı ayrıntı verir</div>
            <div class="apidocs__error-row"><strong>500</strong> Sunucu hatası — <code>error</code> alanına bakın</div>
          </div>

          <div class="apidocs__note mt-3">
            <strong>Standart yanıt formatı:</strong>
            <pre><code>{
  "success": true | false,
  "message": "Açıklama",
  "data": {...},          // başarılıysa
  "errors": {...},        // 422 validasyon
  "error": "..."          // 500 hata
}</code></pre>
          </div>
        </section>

        {{-- ============ CHANGELOG ============ --}}
        <section id="changelog" class="apidocs__section">
          <h2><i class="fas fa-clock-rotate-left"></i> Changelog</h2>

          <div class="apidocs__changelog">
            <div class="apidocs__changelog-item">
              <span class="apidocs__changelog-date">{{ now()->format('Y-m-d') }}</span>
              <ul>
                <li><strong>Orders POST/PUT:</strong> <code>s3_zip</code>, <code>shipping_method_id</code>, <code>shipping_cost</code>, <code>billing_*</code> alanları eklendi.</li>
                <li><strong>Orders response:</strong> <code>shippingMethod</code> ilişkisi her zaman load edilir.</li>
                <li><strong>Carts POST:</strong> <code>urgent_production</code> ve <code>design_service</code> notes JSON'a otomatik aktarılır.</li>
                <li><strong>Carts PUT:</strong> <code>s3_zip</code>, <code>urgent_status</code> validasyon kuralları eklendi.</li>
                <li><strong>Cart model:</strong> <code>urgent_status</code> fillable bug fix.</li>
              </ul>
            </div>
            <div class="apidocs__changelog-item">
              <span class="apidocs__changelog-date">2026-04-30</span>
              <ul>
                <li>Order R2 multipart upload akışı eklendi (<code>/upload/r2/order/initiate|complete|abort</code>) — public endpoint, web auth gerekir.</li>
                <li>Cart Identifier (<code>cart_id</code>) künye formatı stabilize edildi.</li>
              </ul>
            </div>
          </div>
        </section>
      </div>
    </div>

  </section>
</main>
<br>
@endsection
