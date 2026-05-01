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

    {{-- ======== B2B Tanıtım / Marka Hikayesi ======== --}}
    <section class="container py-5 my-3">
      <div class="row align-items-center g-4">
        <div class="col-lg-6">
          <small class="text-uppercase brand-divider fw-bold d-block mb-2">— B2B Baskı Atölyesi</small>
          <h2 class="section-title text-uppercase fs-25 fw-medium mb-3">
            Fotoğrafçıların güvendiği<br>
            <span class="brand-text">albüm &amp; baskı çözümü</span>
          </h2>
          <p class="text-secondary mb-3">
            {{ $siteSettings->company_title ?? $siteSettings->title ?? 'KenAlbüm' }} olarak yıllardır profesyonel fotoğrafçılar,
            stüdyolar ve baskı atölyeleri için yüksek kaliteli albüm ve fotoğraf baskısı üretiyoruz.
            Bayi panelimizden seçimlerinizi yapın, dosyalarınızı yükleyin — siz müşterilerinizle ilgilenirken
            biz üretimi tamamlayıp doğrudan size veya nihai müşterinize ulaştırırız.
          </p>
          <ul class="list-unstyled mb-4">
            <li class="mb-2"><i class="fas fa-check-circle brand-icon me-2"></i> Toplu siparişlere özel <strong>bayi indirim grupları</strong></li>
            <li class="mb-2"><i class="fas fa-check-circle brand-icon me-2"></i> Onlarca <strong>kapak / kumaş / renk</strong> seçeneği</li>
            <li class="mb-2"><i class="fas fa-check-circle brand-icon me-2"></i> İhtiyaç anında <strong>acil üretim</strong> ve <strong>tasarım hizmeti</strong></li>
          </ul>
          @auth
            <a href="#" class="btn btn-primary border-0 fs-base text-uppercase fw-normal btn-50 me-2" data-bs-toggle="modal" data-bs-target="#orderProductPickerModal">
              <span>HEMEN SİPARİŞ VER</span>
            </a>
            <a href="{{ route('profile.index') }}" class="btn-link text-uppercase fw-medium text-decoration-underline">
              Bayi Panelim
            </a>
          @else
            <a href="{{ route('login') }}" class="btn btn-primary border-0 fs-base text-uppercase fw-normal btn-50 me-2">
              <span>BAYİ GİRİŞİ</span>
            </a>
            <a href="{{ route('register') }}" class="btn-link text-uppercase fw-medium text-decoration-underline">
              Bayi Başvurusu
            </a>
          @endauth
        </div>
        <div class="col-lg-6">
          <div class="row g-3">
            <div class="col-6">
              <div class="bg-light p-4 h-100 text-center">
                <div class="fs-25 brand-stat">10+</div>
                <small class="text-uppercase text-secondary">Yıllık Tecrübe</small>
              </div>
            </div>
            <div class="col-6">
              <div class="bg-light p-4 h-100 text-center">
                <div class="fs-25 brand-stat">500+</div>
                <small class="text-uppercase text-secondary">Aktif Bayi</small>
              </div>
            </div>
            <div class="col-6">
              <div class="bg-light p-4 h-100 text-center">
                <div class="fs-25 brand-stat">100K+</div>
                <small class="text-uppercase text-secondary">Tamamlanan Albüm</small>
              </div>
            </div>
            <div class="col-6">
              <div class="bg-light p-4 h-100 text-center">
                <div class="fs-25 brand-stat">2-5</div>
                <small class="text-uppercase text-secondary">İş Günü Üretim</small>
              </div>
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
              <i class="fas fa-percentage brand-icon fs-25 mb-3 d-block"></i>
              <h6 class="fw-medium text-uppercase mb-2">Bayiye Özel Fiyat</h6>
              <p class="text-secondary small mb-0">Sipariş hacminize göre otomatik uygulanan kademeli indirim grupları. Listede gördüğünüz fiyat, sizin net bayi fiyatınızdır.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="text-center p-3 h-100">
              <i class="fas fa-bolt brand-icon fs-25 mb-3 d-block"></i>
              <h6 class="fw-medium text-uppercase mb-2">Acil Üretim</h6>
              <p class="text-secondary small mb-0">Düğün tarihi yaklaşan müşteriniz mi var? Acil üretim seçeneğiyle siparişinizi öne alıyor, gününde teslim ediyoruz.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="text-center p-3 h-100">
              <i class="fas fa-pen-fancy brand-icon fs-25 mb-3 d-block"></i>
              <h6 class="fw-medium text-uppercase mb-2">Tasarım Hizmeti</h6>
              <p class="text-secondary small mb-0">Tasarımı kendiniz yapabilir veya bizim ekibimize bırakabilirsiniz. Dizgi ve rötüş hizmetleri de mevcuttur.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="text-center p-3 h-100">
              <i class="fas fa-shipping-fast brand-icon fs-25 mb-3 d-block"></i>
              <h6 class="fw-medium text-uppercase mb-2">Türkiye Geneli Kargo</h6>
              <p class="text-secondary small mb-0">Anlaşmalı kargo firmalarımızla siparişiniz hızlıca yola çıkar. Dilerseniz nihai müşterinizin adresine gönderilebilir.</p>
            </div>
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
