@extends('frontend.master')
@section('meta')

@endsection
@section('content')
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

  
        @foreach($mainCategories as $category)
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

    



  </main>

@endsection
