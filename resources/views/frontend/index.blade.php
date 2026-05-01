@extends('frontend.master')
@section('meta')

@endsection
@section('content')

{{-- Anasayfa için yerel utility'ler — temanın .text-success'i pastel yeşile (#def2d7)
     override edildiği için yeni .brand-* class'ları ekledim. Mevcut class'lar
     (.text-success, .bg-light vs) dokunulmadı; sadece anasayfa içerik bölümleri
     bu yeni class'ları kullanıyor. --}}
<style>
.brand-text       { color: #198754 !important; }
.brand-text-dark  { color: #146c43 !important; }
.brand-bg-soft    { background-color: #f0f9f4 !important; }
.brand-divider    { color: #198754; letter-spacing: .12em; }
.brand-stat       { color: #198754; font-weight: 700; }
.brand-step-num   { color: #198754; font-weight: 700; }
.brand-cta {
    background-color: #198754 !important;
    border-color: #198754 !important;
    color: #fff !important;
}
.brand-cta:hover, .brand-cta:focus {
    background-color: #146c43 !important;
    border-color: #146c43 !important;
    color: #fff !important;
}
.brand-icon { color: #198754; }

/* ============ B2B Hero — editorial dark card ============ */
.b2b-hero {
    position: relative;
    border-radius: 24px;
    overflow: hidden;
    background: #0a0a0a;
    min-height: 480px;
    isolation: isolate;
}
.b2b-hero-bg {
    position: absolute;
    inset: 0;
    z-index: 0;
}
.b2b-hero-bg img {
    width: 100%; height: 100%;
    object-fit: cover;
    opacity: 0.45;
    filter: grayscale(20%) contrast(1.05);
}
.b2b-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    z-index: 1;
    background:
        radial-gradient(circle at 75% 20%, rgba(124,58,237,.45), transparent 55%),
        linear-gradient(135deg, rgba(10,10,10,.92) 0%, rgba(30,27,75,.85) 50%, rgba(10,10,10,.85) 100%);
}
.b2b-hero-grid {
    position: relative;
    z-index: 2;
    display: grid;
    grid-template-columns: 1.6fr 1fr;
    gap: 56px;
    padding: 64px 56px;
    align-items: center;
}
.b2b-hero-content { color: #fff; max-width: 560px; }
.b2b-hero-eyebrow {
    display: inline-block;
    color: #c4b5fd;
    text-transform: uppercase;
    letter-spacing: .22em;
    font-size: .72rem;
    font-weight: 600;
    margin-bottom: 18px;
    padding: 4px 12px;
    border: 1px solid rgba(196,181,253,.30);
    border-radius: 999px;
    background: rgba(124,58,237,.10);
    backdrop-filter: blur(8px);
}
.b2b-hero-title {
    color: #fff;
    font-weight: 800;
    font-size: clamp(2rem, 4.5vw, 3.4rem);
    line-height: 1.05;
    letter-spacing: -.025em;
    margin: 0 0 22px;
    font-family: inherit !important;
}
.b2b-hero-title-accent {
    background: linear-gradient(135deg, #c4b5fd 0%, #a855f7 60%, #f0abfc 100%);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}
.b2b-hero-lead {
    color: rgba(255,255,255,.85);
    font-size: 1.02rem;
    line-height: 1.65;
    margin-bottom: 28px;
    max-width: 540px;
}
.b2b-hero-actions {
    display: flex;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
}
.b2b-hero-cta {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #fff;
    color: #0a0a0a;
    padding: 13px 24px;
    border-radius: 999px;
    font-weight: 700;
    font-size: .88rem;
    text-transform: uppercase;
    letter-spacing: .04em;
    transition: transform .2s ease, box-shadow .2s ease, background .15s ease;
    box-shadow: 0 12px 30px rgba(0,0,0,.20);
    text-decoration: none;
}
.b2b-hero-cta:hover {
    background: #f5f3ff;
    color: #5b21b6;
    transform: translateY(-2px);
    box-shadow: 0 18px 40px rgba(0,0,0,.30);
}
.b2b-hero-cta i { font-size: .8rem; transition: transform .2s ease; }
.b2b-hero-cta:hover i { transform: translateX(3px); }
.b2b-hero-link {
    color: #c4b5fd;
    font-weight: 600;
    font-size: .88rem;
    text-decoration: none;
    transition: color .15s ease;
}
.b2b-hero-link:hover { color: #fff; }

/* Sağdaki feature tag'ler (4 küçük kart) */
.b2b-hero-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
.b2b-hero-tag {
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    color: #fff;
    padding: 14px 16px;
    border-radius: 14px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    transition: background .2s ease, border-color .2s ease, transform .2s ease;
}
.b2b-hero-tag:hover {
    background: rgba(255,255,255,.10);
    border-color: rgba(196,181,253,.35);
    transform: translateY(-2px);
}
.b2b-hero-tag i {
    color: #c4b5fd;
    font-size: 1.1rem;
    margin-top: 2px;
}
.b2b-hero-tag span {
    font-size: .8rem;
    line-height: 1.35;
}
.b2b-hero-tag strong {
    color: #fff;
    font-weight: 600;
    display: block;
}
.b2b-hero-tag em {
    color: rgba(255,255,255,.65);
    font-style: normal;
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: .04em;
}

/* Sağ üst floating mini kart (overlap) */
.b2b-hero-floating {
    position: absolute;
    z-index: 3;
    right: 32px;
    bottom: 32px;
    background: #fff;
    border-radius: 14px;
    padding: 8px;
    box-shadow: 0 24px 60px rgba(0,0,0,.40);
    display: flex;
    align-items: center;
    gap: 12px;
    max-width: 280px;
    transform: rotate(-2deg);
    transition: transform .3s ease;
}
.b2b-hero-floating:hover { transform: rotate(0) translateY(-4px); }
.b2b-hero-floating img {
    width: 60px; height: 60px;
    object-fit: cover;
    border-radius: 10px;
}
.b2b-hero-floating-info {
    display: flex;
    align-items: center;
    gap: 8px;
    padding-right: 10px;
}
.b2b-hero-floating-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 4px rgba(16,185,129,.18);
    flex-shrink: 0;
}
.b2b-hero-floating-info strong {
    display: block;
    color: #0a0a0a;
    font-size: .82rem;
    font-weight: 700;
    line-height: 1.2;
}
.b2b-hero-floating-info em {
    display: block;
    color: #737373;
    font-style: normal;
    font-size: .7rem;
    margin-top: 2px;
}

@media (max-width: 991px) {
    .b2b-hero-grid {
        grid-template-columns: 1fr;
        padding: 44px 28px;
        gap: 36px;
    }
    .b2b-hero-floating { display: none; }
}
@media (max-width: 575px) {
    .b2b-hero-meta { grid-template-columns: 1fr; }
    .b2b-hero-grid { padding: 36px 24px; }
}

/* B2B intro asimetrik kolaj */
.b2b-collage {
    position: relative;
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 14px;
    height: 460px;
}
.b2b-collage-main {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 12px 28px rgba(15,23,42,.10);
    transition: transform .3s ease;
}
.b2b-collage-main:hover { transform: translateY(-4px); }
.b2b-collage-main img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}
.b2b-collage-tag {
    position: absolute;
    top: 16px; left: 16px;
    background: rgba(255,255,255,.95);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    padding: 6px 12px;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 600;
    color: #171717;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    letter-spacing: .03em;
    box-shadow: 0 4px 12px rgba(15,23,42,.08);
}
.b2b-collage-tag-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #7c3aed;
    box-shadow: 0 0 0 3px rgba(124,58,237,.20);
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%,100% { box-shadow: 0 0 0 3px rgba(124,58,237,.20); }
    50% { box-shadow: 0 0 0 6px rgba(124,58,237,.10); }
}
.b2b-collage-side {
    display: grid;
    grid-template-rows: 1fr 1fr 0.85fr;
    gap: 14px;
}
.b2b-collage-thumb {
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 6px 16px rgba(15,23,42,.08);
    transition: transform .3s ease;
    background: #f5f5f5;
}
.b2b-collage-thumb:hover { transform: translateY(-3px); }
.b2b-collage-thumb img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}
.b2b-collage-mini-stat {
    background: #0a0a0a;
    background-image: linear-gradient(135deg, #312e81 0%, #1e1b4b 50%, #0a0a0a 100%);
    color: #fff;
    border-radius: 14px;
    padding: 16px 18px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(15,23,42,.18);
}
.b2b-collage-stat-number {
    font-size: 1.65rem;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -.02em;
    background: linear-gradient(135deg, #c4b5fd, #a855f7);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    margin-bottom: 4px;
}
.b2b-collage-stat-label {
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: .12em;
    line-height: 1.3;
    color: rgba(255,255,255,.7);
    font-weight: 500;
}
@media (max-width: 768px) {
    .b2b-collage { height: 340px; }
}

/* Numune Albümler — fotograf galeri (mosaic) */
.showcase-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    grid-auto-rows: 140px;
    gap: 12px;
}
.showcase-grid .show-tile {
    border-radius: 12px;
    overflow: hidden;
    background: #f5f5f5;
    position: relative;
    box-shadow: 0 4px 12px rgba(15,23,42,.06);
    transition: transform .3s ease, box-shadow .3s ease;
}
.showcase-grid .show-tile:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px rgba(15,23,42,.10);
}
.showcase-grid .show-tile img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .5s ease;
}
.showcase-grid .show-tile:hover img { transform: scale(1.06); }
.showcase-grid .show-tile-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, transparent 50%, rgba(10,10,10,.65));
    display: flex;
    align-items: flex-end;
    padding: 14px;
    opacity: 0;
    transition: opacity .25s ease;
}
.showcase-grid .show-tile:hover .show-tile-overlay { opacity: 1; }
.showcase-grid .show-tile-title {
    color: #fff;
    font-weight: 600;
    font-size: .85rem;
    letter-spacing: -.005em;
}
.showcase-tile-1 { grid-column: span 3; grid-row: span 2; }
.showcase-tile-2 { grid-column: span 3; grid-row: span 2; }
.showcase-tile-3 { grid-column: span 2; grid-row: span 2; }
.showcase-tile-4 { grid-column: span 2; grid-row: span 2; }
.showcase-tile-5 { grid-column: span 2; grid-row: span 2; }

@media (max-width: 991px) {
    .showcase-grid { grid-template-columns: repeat(4, 1fr); grid-auto-rows: 120px; }
    .showcase-tile-1, .showcase-tile-2 { grid-column: span 4; }
    .showcase-tile-3, .showcase-tile-4, .showcase-tile-5 { grid-column: span 2; grid-row: span 2; }
}
@media (max-width: 575px) {
    .showcase-grid { grid-template-columns: repeat(2, 1fr); grid-auto-rows: 140px; }
    .showcase-tile-1, .showcase-tile-2, .showcase-tile-3, .showcase-tile-4, .showcase-tile-5 {
        grid-column: span 2; grid-row: span 1;
    }
}

/* Featured highlight — tek vurgulu görsel kartı */
.feature-highlight {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    min-height: 360px;
    background: #0a0a0a;
}
.feature-highlight img {
    position: absolute;
    inset: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    opacity: .55;
}
.feature-highlight-content {
    position: relative;
    z-index: 2;
    padding: 48px;
    color: #fff;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.feature-highlight-content h3 {
    color: #fff;
    font-weight: 800;
    font-size: 2rem;
    letter-spacing: -.025em;
    margin-bottom: 14px;
}
.feature-highlight-content p {
    color: rgba(255,255,255,.85);
    max-width: 460px;
    margin-bottom: 24px;
}
@media (max-width: 768px) {
    .feature-highlight-content { padding: 32px; }
    .feature-highlight-content h3 { font-size: 1.5rem; }
}
</style>

<main>
    <section class="swiper-container js-swiper-slider slideshow type4 slideshow-navigation-white-sm"
      data-settings='{
        "autoplay": {
          "delay": 5000
        },
        "navigation": {
          "nextEl": ".slideshow__next",
          "prevEl": ".slideshow__prev"
        },
        "pagination": false,
        "slidesPerView": 1,
        "effect": "fade",
        "loop": true
      }'>
      
      <div class="swiper-wrapper">  
        @foreach($sliders as $slider)
        <div class="swiper-slide">
          <div class="overflow-hidden position-relative h-100">
            <div class="slideshow-bg">
              <img loading="lazy" src="{{ asset('storage/'.$slider->image) }}" width="1920" height="600" alt="Pattern" class="slideshow-bg__img object-fit-cover">
            </div>
            <div class="slideshow-text container position-absolute start-50 top-50 translate-middle">
              <h2 class="fs-70 mb-2 mb-lg-3 animate animate_fade animate_btt animate_delay-5 text-uppercase fw-normal" style="font-family: 'Average Sans';">{{ $slider->title }}</h2>
              <p class="h6 mb-4 pb-2 animate animate_fade animate_btt animate_delay-5 lh-2rem">{{ $slider->description }}</p>
              @if($slider->link)
              <div class="animate animate_fade animate_btt animate_delay-7">
                <a href="{{ $slider->link }}" class="btn btn-primary border-0 fs-base text-uppercase fw-normal btn-50">
                  <span>{{ $slider->button_text }}</span>
                </a>
              </div>
              @endif
            </div>
          </div>
        </div><!-- /.slideshow-item -->
         @endforeach



      </div><!-- /.slideshow-wrapper js-swiper-slider -->

      <div class="slideshow__prev position-absolute top-50 d-flex align-items-center justify-content-center border-radius-0">
        <svg width="7" height="11" viewBox="0 0 7 11" xmlns="http://www.w3.org/2000/svg"><use href="#icon_prev_sm" /></svg>
      </div><!-- /.slideshow__prev -->
      <div class="slideshow__next position-absolute top-50 d-flex align-items-center justify-content-center border-radius-0">
        <svg width="7" height="11" viewBox="0 0 7 11" xmlns="http://www.w3.org/2000/svg"><use href="#icon_next_sm" /></svg>
      </div><!-- /.slideshow__next -->
    </section><!-- /.slideshow -->

    {{-- ======== B2B Hero — editorial dark hero (yatay) ======== --}}
    <section class="container py-5 my-3">
      <div class="b2b-hero">
        <div class="b2b-hero-bg">
          <img src="/images/1758117104_ORFb8xojb0.JPG" alt="" loading="lazy">
        </div>
        <div class="b2b-hero-grid">
          <div class="b2b-hero-content">
            <small class="b2b-hero-eyebrow">B2B Baskı Atölyesi</small>
            <h2 class="b2b-hero-title">
              Fotoğrafçıların güvendiği<br>
              <span class="b2b-hero-title-accent">albüm &amp; baskı çözümü</span>
            </h2>
            <p class="b2b-hero-lead">
              {{ $siteSettings->company_title ?? $siteSettings->title ?? 'KenAlbüm' }} olarak profesyonel fotoğrafçılar, stüdyolar ve baskı atölyeleri için yüksek kaliteli üretim yapıyoruz. Siz müşterilerinizle ilgilenirken biz üretimi tamamlayıp size veya nihai müşterinize ulaştırırız.
            </p>
            <div class="b2b-hero-actions">
              @auth
                <a href="#" class="b2b-hero-cta" data-bs-toggle="modal" data-bs-target="#orderProductPickerModal">
                  <span>Hemen Sipariş Ver</span>
                  <i class="fas fa-arrow-right"></i>
                </a>
                <a href="{{ route('profile.index') }}" class="b2b-hero-link">Bayi Panelim →</a>
              @else
                <a href="{{ route('login') }}" class="b2b-hero-cta">
                  <span>Bayi Girişi</span>
                  <i class="fas fa-arrow-right"></i>
                </a>
                <a href="{{ route('register') }}" class="b2b-hero-link">Bayi Başvurusu →</a>
              @endauth
            </div>
          </div>

          <div class="b2b-hero-meta">
            <div class="b2b-hero-tag">
              <i class="fas fa-percentage"></i>
              <span><strong>Bayi indirim</strong><br><em>grupları</em></span>
            </div>
            <div class="b2b-hero-tag">
              <i class="fas fa-bolt"></i>
              <span><strong>Acil üretim</strong><br><em>seçeneği</em></span>
            </div>
            <div class="b2b-hero-tag">
              <i class="fas fa-pen-fancy"></i>
              <span><strong>Tasarım</strong><br><em>hizmeti</em></span>
            </div>
            <div class="b2b-hero-tag">
              <i class="fas fa-shipping-fast"></i>
              <span><strong>Türkiye</strong><br><em>geneli kargo</em></span>
            </div>
          </div>
        </div>

        {{-- Floating mini-album görsel (sağ üst köşe) --}}
        <div class="b2b-hero-floating">
          <img src="/images/1758033483_b3GibIqYHN.jpg" alt="Lily Albüm" loading="lazy">
          <div class="b2b-hero-floating-info">
            <span class="b2b-hero-floating-dot"></span>
            <div>
              <strong>Premium Seri</strong>
              <em>10+ yıl tecrübe</em>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- ======== Bayi Avantajları ======== --}}
    <section class="bg-light py-5">
      <div class="container">
        <div class="text-center mb-4">
          <small class="text-uppercase brand-divider fw-bold d-block mb-2">— Neden KenAlbüm?</small>
          <h2 class="section-title text-uppercase fs-25 fw-medium">Bayi Olmanın Avantajları</h2>
        </div>
        <div class="row g-4">
          <div class="col-md-6 col-lg-3">
            <div class="text-center p-3 h-100">
              <i class="fas fa-percentage brand-icon fs-25 mb-3 d-block"></i>{{-- mor --}}
              <h6 class="fw-medium text-uppercase mb-2">Bayiye Özel Fiyat</h6>
              <p class="text-secondary small mb-0">Sipariş hacminize göre otomatik uygulanan kademeli indirim grupları. Listede gördüğünüz fiyat, sizin net bayi fiyatınızdır.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="text-center p-3 h-100">
              <i class="fas fa-bolt brand-icon icon-orange fs-25 mb-3 d-block"></i>
              <h6 class="fw-medium text-uppercase mb-2">Acil Üretim</h6>
              <p class="text-secondary small mb-0">Düğün tarihi yaklaşan müşteriniz mi var? Acil üretim seçeneğiyle siparişinizi öne alıyor, gününde teslim ediyoruz.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="text-center p-3 h-100">
              <i class="fas fa-pen-fancy brand-icon icon-pink fs-25 mb-3 d-block"></i>
              <h6 class="fw-medium text-uppercase mb-2">Tasarım Hizmeti</h6>
              <p class="text-secondary small mb-0">Tasarımı kendiniz yapabilir veya bizim ekibimize bırakabilirsiniz. Dizgi ve rötüş hizmetleri de mevcuttur.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="text-center p-3 h-100">
              <i class="fas fa-shipping-fast brand-icon icon-teal fs-25 mb-3 d-block"></i>
              <h6 class="fw-medium text-uppercase mb-2">Türkiye Geneli Kargo</h6>
              <p class="text-secondary small mb-0">Anlaşmalı kargo firmalarımızla siparişiniz hızlıca yola çıkar. Dilerseniz nihai müşterinizin adresine gönderilebilir.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- ======== Numune Albümlerimizden — görsel galeri (mosaic) ======== --}}
    <section class="container py-5">
      <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
        <div>
          <small class="text-uppercase brand-divider fw-bold d-block mb-2">— Galeri</small>
          <h2 class="section-title text-uppercase fs-25 fw-medium mb-0">Numune Albümlerimizden</h2>
        </div>
        <p class="text-secondary mb-0" style="max-width:420px;">
          Bayi siparişlerinden derlenmiş, baskı kalitemizi yansıtan örnekler. Üzerine tıklayarak her ürünün siparişine başlayabilirsiniz.
        </p>
      </div>

      <div class="showcase-grid">
        <a href="@auth#@else{{ route('register') }}@endauth" class="show-tile showcase-tile-1" @auth data-bs-toggle="modal" data-bs-target="#orderProductPickerModal" @endauth>
          <img src="/images/1758033483_b3GibIqYHN.jpg" alt="Lily Albüm" loading="lazy">
          <div class="show-tile-overlay"><span class="show-tile-title">Lily Albüm — Premium</span></div>
        </a>
        <a href="@auth#@else{{ route('register') }}@endauth" class="show-tile showcase-tile-2" @auth data-bs-toggle="modal" data-bs-target="#orderProductPickerModal" @endauth>
          <img src="/images/1758112544_0mgV9mENTd.jpg" alt="Velvet Albüm" loading="lazy">
          <div class="show-tile-overlay"><span class="show-tile-title">Velvet Albüm</span></div>
        </a>
        <a href="@auth#@else{{ route('register') }}@endauth" class="show-tile showcase-tile-3" @auth data-bs-toggle="modal" data-bs-target="#orderProductPickerModal" @endauth>
          <img src="/images/1758033299_WtuZuwrySN.jpg" alt="Daisy Albüm" loading="lazy">
          <div class="show-tile-overlay"><span class="show-tile-title">Daisy Albüm</span></div>
        </a>
        <a href="@auth#@else{{ route('register') }}@endauth" class="show-tile showcase-tile-4" @auth data-bs-toggle="modal" data-bs-target="#orderProductPickerModal" @endauth>
          <img src="/images/1758116669_2whgYKwame.JPG" alt="Leaf Albüm" loading="lazy">
          <div class="show-tile-overlay"><span class="show-tile-title">Leaf Albüm</span></div>
        </a>
        <a href="@auth#@else{{ route('register') }}@endauth" class="show-tile showcase-tile-5" @auth data-bs-toggle="modal" data-bs-target="#orderProductPickerModal" @endauth>
          <img src="/images/1758033733_e0uWnodq4t.jpg" alt="Lotus Albüm" loading="lazy">
          <div class="show-tile-overlay"><span class="show-tile-title">Lotus Albüm</span></div>
        </a>
      </div>
    </section>

    {{-- ======== Feature highlight — tam genişlik koyu görsel CTA ======== --}}
    <section class="container py-3">
      <div class="feature-highlight">
        <img src="/images/1758117104_ORFb8xojb0.JPG" alt="Premium baskı" loading="lazy">
        <div class="feature-highlight-content">
          <small class="text-uppercase fw-bold d-block mb-2" style="color:#c4b5fd; letter-spacing:.18em; font-size:.72rem;">— Yeni nesil baskı</small>
          <h3>Müşterilerinizi etkileyecek<br>premium kalite</h3>
          <p>Düğün, nişan, doğum ve özel anlar için profesyonel kalitede albüm üretimi. Yıllarca koruma sağlayan dayanıklı malzemeler, modern bağlama teknikleri ve renk derinliği yüksek baskı.</p>
          <div>
            @auth
              <a href="#" class="btn btn-outline-secondary fs-base text-uppercase fw-normal" data-bs-toggle="modal" data-bs-target="#orderProductPickerModal" style="background:#fff; color:#0a0a0a; border-color:#fff;">
                <span>Hemen Sipariş Ver</span>
              </a>
            @else
              <a href="{{ route('register') }}" class="btn btn-outline-secondary fs-base text-uppercase fw-normal" style="background:#fff; color:#0a0a0a; border-color:#fff;">
                <span>Bayi Başvurusu</span>
              </a>
            @endauth
          </div>
        </div>
      </div>
    </section>

    {{-- ======== Nasıl Çalışır? ======== --}}
    <section class="container py-5">
      <div class="text-center mb-4">
        <small class="text-uppercase brand-divider fw-bold d-block mb-2">— Sipariş Süreci</small>
        <h2 class="section-title text-uppercase fs-25 fw-medium">Sadece 4 Adımda Sipariş</h2>
      </div>
      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="text-center p-3 h-100">
            <div class="brand-step-num fs-25 mb-2">01</div>
            <h6 class="fw-medium text-uppercase mb-2">Ürünü Seçin</h6>
            <p class="text-secondary small mb-0">Header'daki <strong>Sipariş Ver</strong> menüsünden veya kategorilerden ürününüzü seçin. Aynı siparişten yeniden vermek için <em>Geçmişim</em> sekmesini kullanın.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="text-center p-3 h-100">
            <div class="brand-step-num fs-25 mb-2">02</div>
            <h6 class="fw-medium text-uppercase mb-2">Özelleştirin</h6>
            <p class="text-secondary small mb-0">Ebat, kumaş, renk, paket ve diğer detayları adım adım wizard'dan seçin. Her seçimde toplam fiyatınız anlık güncellenir.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="text-center p-3 h-100">
            <div class="brand-step-num fs-25 mb-2">03</div>
            <h6 class="fw-medium text-uppercase mb-2">Dosyaları Yükleyin</h6>
            <p class="text-secondary small mb-0">Kapak ve iç sayfa tasarımlarınızı sipariş özetinde tek bir ZIP olarak yükleyin. Hatalı dosyalar tarafımızdan kontrol edilir.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="text-center p-3 h-100">
            <div class="brand-step-num fs-25 mb-2">04</div>
            <h6 class="fw-medium text-uppercase mb-2">Üretim &amp; Kargo</h6>
            <p class="text-secondary small mb-0">Siparişiniz onaylandıktan sonra üretim sürecine girer. Durum takibini panelinizden anlık görebilir, kargo takibinizi yapabilirsiniz.</p>
          </div>
        </div>
      </div>
    </section>

    {{-- ======== Kategoriler intro ======== --}}
    <section class="container text-center pt-3">
      <small class="text-uppercase brand-divider fw-bold d-block mb-2">— Ürünlerimiz</small>
      <h2 class="section-title text-uppercase fs-25 fw-medium mb-2">Kategorilere Göz Atın</h2>
      <p class="text-secondary col-lg-7 mx-auto mb-0">Albümler, fotokitaplar, duvar çerçeveleri ve daha fazlası — tüm ürün kategorilerimizden bayi fiyatıyla hızlıca sipariş verebilirsiniz.</p>
    </section>

        @foreach($homepageCategories as $category)
        <div class="mb-3 mb-xl-5 pb-3 pt-1 pb-xl-5"></div>

        @if($category->products->count() > 0)
            <section class="products-carousel container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="section-title text-uppercase fs-25 fw-medium mb-2">{{ $category->title }}</h2>
                        <p class="fs-15 mb-0 text-secondary">{{ $category->title }} kategorisindeki en kaliteli ürünler.</p>
                    </div>
                    <div class="d-flex align-items-center">
                     
                      
                    </div>
                </div>

                <div class="position-relative">
                    <div class="swiper-container " >
                        <div class="swiper-wrapper">
                            @foreach($category->products as $product)
                                <div class="swiper-slide product-card">
                                    <div class="pc__img-wrapper">
                                        <a href="{{ route('products.show', $product->slug) }}">
                                            @php
                                                $firstImage = $product->images ? explode(',', $product->images)[0] : null;
                                                $firstThumbnail = $product->thumbnails ? explode(',', $product->thumbnails)[0] : null;
                                            @endphp
                                            @if($firstThumbnail)
                                                <img loading="lazy" src="{{ trim($firstThumbnail) }}" width="330" height="400" alt="{{ $product->title }}" class="pc__img">
                                            @elseif($firstImage)
                                                <img loading="lazy" src="{{ trim($firstImage) }}" width="330" height="400" alt="{{ $product->title }}" class="pc__img">
                                           
                                            @endif
                                        </a>
                                    </div>

                                    <div class="pc__info position-relative text-center">
                                       
                                        <h6 class="pc__title text-uppercase fw-medium mb-2"><a href="{{ route('products.show', $product->slug) }}">{{ $product->title }}</a></h6>
                                     
                                      
                                    </div>
                                </div>
                            @endforeach
                        </div><!-- /.swiper-wrapper -->
                    </div><!-- /.swiper-container js-swiper-slider -->

               
                </div><!-- /.position-relative -->
            </section><!-- /.products-carousel container -->

        @endif
    @endforeach

    {{-- ======== Sıkça Sorulan Sorular ======== --}}
    <section class="bg-light py-5">
      <div class="container">
        <div class="row">
          <div class="col-lg-4 mb-4 mb-lg-0">
            <small class="text-uppercase brand-divider fw-bold d-block mb-2">— SSS</small>
            <h2 class="section-title text-uppercase fs-25 fw-medium">Sık Sorulan Sorular</h2>
            <p class="text-secondary mt-3">Bayi sürecimizle ilgili merak ettikleriniz için cevapları derledik. Aklınıza takılan başka bir konu olursa bize ulaşın.</p>
          </div>
          <div class="col-lg-8">
            <div class="accordion" id="homeFaqAccordion">
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqB1">
                    Bayi başvurusunu nasıl yapabilirim?
                  </button>
                </h2>
                <div id="faqB1" class="accordion-collapse collapse" data-bs-parent="#homeFaqAccordion">
                  <div class="accordion-body small text-secondary">
                    Üst menüden <strong>Bayi Başvurusu</strong> bağlantısıyla kayıt formunu doldurup gönderebilirsiniz. Ekibimiz başvurunuzu inceleyip onay verdikten sonra panelinize giriş yapabilir, bayi fiyatlarıyla sipariş vermeye başlayabilirsiniz.
                  </div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqB2">
                    Üretim ve teslimat süresi ne kadar?
                  </button>
                </h2>
                <div id="faqB2" class="accordion-collapse collapse" data-bs-parent="#homeFaqAccordion">
                  <div class="accordion-body small text-secondary">
                    Standart üretim süremiz <strong>2-5 iş günü</strong> arasındadır. Acil üretim seçeneği ile bu süre 1 iş gününe iner. Kargolama anlaşmalı firmalar üzerinden yapılır; gönderim tarihi panelinizden takip edilebilir.
                  </div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqB3">
                    Hangi dosya formatlarını kabul ediyorsunuz?
                  </button>
                </h2>
                <div id="faqB3" class="accordion-collapse collapse" data-bs-parent="#homeFaqAccordion">
                  <div class="accordion-body small text-secondary">
                    Kapak ve iç sayfaları için <strong>PSD, AI, PDF, TIFF, JPG</strong> formatlarını kabul ediyoruz. Tüm dosyaları tek bir <strong>ZIP/RAR</strong> arşivi olarak siparişin son adımında yükleyebilirsiniz. Tasarım şablonlarımızı ürün detay sayfasından indirebilirsiniz.
                  </div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqB4">
                    Sık tekrarlayan siparişlerimi hızlandırabilir miyim?
                  </button>
                </h2>
                <div id="faqB4" class="accordion-collapse collapse" data-bs-parent="#homeFaqAccordion">
                  <div class="accordion-body small text-secondary">
                    Evet. Header'daki <strong>Sipariş Ver</strong> menüsünde <em>Geçmişim</em> sekmesinden önceki siparişlerinize ulaşabilir, tek tıkla aynısını sepete ekleyebilir veya küçük değişiklikler için "Özelleştir" diyerek wizard'ı önceki seçimlerle önceden doldurabilirsiniz.
                  </div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqB5">
                    Ödeme ve fatura nasıl işliyor?
                  </button>
                </h2>
                <div id="faqB5" class="accordion-collapse collapse" data-bs-parent="#homeFaqAccordion">
                  <div class="accordion-body small text-secondary">
                    Bayi panelinizdeki <strong>bakiye</strong> üzerinden ödeme yapılır. Bakiyenizi havale/EFT ile yükleyebilir, her sipariş tutarı bakiyenizden otomatik düşülür. Tüm siparişler için panelinizden e-fatura/dekont indirilebilir.
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- ======== Final CTA ======== --}}
    <section class="container py-5">
      <div class="row align-items-center justify-content-center text-center text-lg-start">
        <div class="col-lg-8">
          <h2 class="section-title text-uppercase fs-25 fw-medium mb-2">
            Bayi olun, baskıyı bize bırakın.
          </h2>
          <p class="text-secondary mb-3 mb-lg-0">
            Stüdyonuza, atölyenize özel bayi indirimleri ve öncelikli üretim avantajları için hemen başvurun.
            @if($siteSettings->phone)
              Sorularınız için <a href="tel:{{ preg_replace('/[^0-9+]/','',$siteSettings->phone) }}" class="brand-text-dark fw-medium text-decoration-none">{{ $siteSettings->phone }}</a> numarasından bize ulaşabilirsiniz.
            @endif
          </p>
        </div>
        <div class="col-lg-4 text-center text-lg-end mt-3 mt-lg-0">
          @auth
            <a href="#" class="btn btn-primary border-0 fs-base text-uppercase fw-normal btn-50" data-bs-toggle="modal" data-bs-target="#orderProductPickerModal">
              <span>SİPARİŞ VER</span>
            </a>
          @else
            <a href="{{ route('register') }}" class="btn btn-primary border-0 fs-base text-uppercase fw-normal btn-50">
              <span>BAYİ BAŞVURUSU YAP</span>
            </a>
          @endauth
        </div>
      </div>
    </section>

  </main>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Ana slider için Swiper initialize
    if ($('.slideshow.type4').length) {
        new Swiper('.slideshow.type4 .swiper-container', {
            autoplay: {
                delay: 5000
            },
            navigation: {
                nextEl: '.slideshow__next',
                prevEl: '.slideshow__prev'
            },
            pagination: false,
            slidesPerView: 1,
            effect: 'fade',
            loop: true
        });
    }

    // Kategori ürün swiperları için
    $('.products-carousel .swiper-container').each(function(index, element) {
        new Swiper(element, {
            slidesPerView: 1,
            spaceBetween: 30,
            breakpoints: {
                576: {
                    slidesPerView: 2,
                    spaceBetween: 30
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 30
                },
                992: {
                    slidesPerView: 4,
                    spaceBetween: 30
                }
            }
        });
    });
});
</script>
@endsection
