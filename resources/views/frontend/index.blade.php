@extends('frontend.master')
@section('meta')

@endsection
@section('content')

{{-- CSS public/css/custom.css'e taşındı, master.blade.php'den yükleniyor. --}}

<main>
    {{-- Eski swiper slider kaldırıldı — modern arayüzlerde basit kaçıyordu.
         Yerine doğrudan B2B editorial hero ile başlıyoruz. --}}

    {{-- ======== B2B Hero — editorial dark hero (yatay) ======== --}}
    <section class="container pt-1">
      <div class="b2b-hero">
        <div class="b2b-hero-bg">
          <img src="/images/1758117104_ORFb8xojb0.JPG" alt="" loading="lazy">
        </div>
        <div class="b2b-hero-grid">
          <div class="b2b-hero-content">
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
            <div class="b2b-hero-tag b2b-hero-tag--1">
              <div class="b2b-hero-tag-icon"><i class="fas fa-percentage"></i></div>
              <h6 class="b2b-hero-tag-title">Bayiye Özel Fiyat</h6>
              <p class="b2b-hero-tag-desc">Sipariş hacminize göre kademeli indirim. Listede gördüğünüz, ödediğiniz net fiyat.</p>
            </div>
            <div class="b2b-hero-tag b2b-hero-tag--2">
              <div class="b2b-hero-tag-icon"><i class="fas fa-bolt"></i></div>
              <h6 class="b2b-hero-tag-title">Acil Üretim</h6>
              <p class="b2b-hero-tag-desc">Yaklaşan tarihiniz mi var? Önceliklendirip planlanan günde teslim ediyoruz.</p>
            </div>
            <div class="b2b-hero-tag b2b-hero-tag--3">
              <div class="b2b-hero-tag-icon"><i class="fas fa-pen-fancy"></i></div>
              <h6 class="b2b-hero-tag-title">Tasarım & Dizgi</h6>
              <p class="b2b-hero-tag-desc">Tasarımı kendiniz yapın ya da bize bırakın. Dizgi, rötüş hizmeti de var.</p>
            </div>
            <div class="b2b-hero-tag b2b-hero-tag--4">
              <div class="b2b-hero-tag-icon"><i class="fas fa-shipping-fast"></i></div>
              <h6 class="b2b-hero-tag-title">Türkiye Geneli Kargo</h6>
              <p class="b2b-hero-tag-desc">Anlaşmalı kargolarla hızlı teslimat. Dilerseniz nihai müşterinize gönderilir.</p>
            </div>
          </div>
        </div>

        {{-- Canlı Destek WhatsApp widget — mesai saatleri 08:00-18:00 --}}
        @php
          $hour = now()->hour;
          $isOnline = $hour >= 8 && $hour < 18;
          $waPhone = preg_replace('/[^0-9]/', '', $siteSettings->phone ?? '');
          $waMessage = rawurlencode('Merhaba, destek alabilir miyim?');
        @endphp
        <a href="https://wa.me/{{ $waPhone }}?text={{ $waMessage }}"
           target="_blank"
           rel="noopener"
           class="b2b-hero-floating support-widget {{ $isOnline ? 'is-online' : 'is-offline' }}"
           aria-label="WhatsApp Canlı Destek">
          <div class="support-widget-icon">
            <i class="fab fa-whatsapp"></i>
          </div>
          <div class="b2b-hero-floating-info">
            <span class="b2b-hero-floating-dot"></span>
            <div>
              <strong>Canlı Destek</strong>
              <em>{{ $isOnline ? 'Çevrimiçi · Hemen yanıtlıyoruz' : 'Mesai dışı · 08:00-18:00' }}</em>
            </div>
          </div>
        </a>
      </div>
    </section>

    {{-- 'Bayi Avantajları' detaylı section'ı kaldırıldı, içerik hero'nun
         sağ tag'lerine taşındı (floating cloud animasyonlu). --}}
    {{-- 'Numune Albümlerimizden' galeri kaldırıldı (gereksiz). --}}

    {{-- ======== Nasıl Çalışır — hero ile feature highlight arasında ======== --}}
    <section class="container py-5">
      <div class="section-head section-head--center">
        <h2 class="section-h2">Kolay Sipariş Adımları</h2>
        <p class="section-lead">
          Birkaç tıklamayla siparişinizi oluşturun: ürünü seçin, yapılandırın, dosyalarınızı yükleyin — gerisini bize bırakın.
        </p>
      </div>

      <div class="flow-steps">
        <div class="flow-step">
          <div class="flow-step-num">01</div>
          <h6 class="flow-step-title">Ürünü seçin</h6>
          <p>Sipariş Ver menüsünden veya kategorilerden ürünü seçin. Aynı siparişten yeniden vermek için <em>Geçmişim</em> sekmesini kullanın.</p>
        </div>
        <div class="flow-step">
          <div class="flow-step-num">02</div>
          <h6 class="flow-step-title">Özelleştirin</h6>
          <p>Ebat, kumaş, renk, paket ve diğer detayları adım adım seçin. Toplam fiyatınız anlık güncellenir.</p>
        </div>
        <div class="flow-step">
          <div class="flow-step-num">03</div>
          <h6 class="flow-step-title">Dosyaları yükleyin</h6>
          <p>Kapak ve iç sayfa tasarımlarınızı tek bir ZIP olarak yükleyin. Hatalı dosyalar tarafımızdan kontrol edilir.</p>
        </div>
        <div class="flow-step">
          <div class="flow-step-num">04</div>
          <h6 class="flow-step-title">Üretim &amp; kargo</h6>
          <p>Onay sonrası üretim başlar. Durum takibini panelden anlık görür, kargo takibinizi yaparsınız.</p>
        </div>
      </div>
    </section>

    {{-- ======== Feature highlight — Nasıl Çalışır ile Kategoriler arasında, CTA'sız ======== --}}
    <section class="container py-3">
      <div class="feature-highlight">
        <img src="/images/1758117104_ORFb8xojb0.JPG" alt="Premium baskı" loading="lazy">
        <div class="feature-highlight-content">
          <small class="text-uppercase fw-bold d-block mb-2" style="color:#fdba74; letter-spacing:.18em; font-size:.72rem;">— Yeni nesil baskı</small>
          <h3>Müşterilerinizi etkileyecek<br>premium kalite</h3>
          <p>Düğün, nişan, doğum ve özel anlar için profesyonel kalitede albüm üretimi. Yıllarca koruma sağlayan dayanıklı malzemeler, modern bağlama teknikleri ve renk derinliği yüksek baskı.</p>
        </div>
      </div>
    </section>

    {{-- 'Kategoriler intro' ve homepageCategories carousel'ları kaldırıldı — anasayfada artık ürün listelemiyoruz. --}}

    {{-- ======== Sıkça Sorulan Sorular ======== --}}
    <section class="container py-5">
      <div class="row g-5 align-items-start">
        <div class="col-lg-4">
          <div class="faq-aside">
            <h2 class="section-h2">Aklınızda<br>soru var mı?</h2>
            <p class="section-lead-tight">Bayi sürecimizle ilgili sıkça sorulan sorulara verdiğimiz cevaplar. Başka bir konu olursa <a href="#" data-bs-toggle="modal" data-bs-target="#orderProductPickerModal" class="faq-aside-link">bize ulaşın</a>.</p>
          </div>
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
    </section>

    {{-- ======== Final CTA — koyu kompakt bant ======== --}}
    <section class="container py-5">
      <div class="cta-band">
        <div class="cta-band-text">
          <h3 class="cta-band-title">Bayi olun,<br>baskıyı bize bırakın.</h3>
          <p>
            Stüdyonuza özel bayi indirimleri ve öncelikli üretim avantajları için başvurun.
            @if($siteSettings->phone)
              Sorularınız için <a href="tel:{{ preg_replace('/[^0-9+]/','',$siteSettings->phone) }}">{{ $siteSettings->phone }}</a>
            @endif
          </p>
        </div>
        <div class="cta-band-action">
          <a href="{{ route('register') }}" class="b2b-hero-cta">
            <span>Kayıt Ol</span>
            <i class="fas fa-arrow-right"></i>
          </a>
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
