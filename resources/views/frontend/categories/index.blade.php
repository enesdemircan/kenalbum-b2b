@extends('frontend.master')

@section('content')
<main>
    <div class="mb-4 pb-lg-3"></div>

    <section class="shop-main container">
        <div class="d-flex justify-content-between mb-4 pb-md-2">
            <div class="breadcrumb mb-0 d-none d-md-block flex-grow-1">
                <a href="{{ route('home') }}" class="menu-link menu-link_us-s text-uppercase fw-medium">Ana Sayfa</a>
                <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
                <a href="#" class="menu-link menu-link_us-s text-uppercase fw-medium">{{ $category->title }}</a>
            </div>
        </div>

        @if($products->count() > 0)

        <!-- Ürünler -->
        <div class="products-grid row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5" id="products-grid">
            @foreach($products as $product)
                <div class="product-card-wrapper">
                    <div class="product-card mb-3 mb-md-4 mb-xxl-5">
                        <div class="pc__img-wrapper">
                            <div class="swiper-container background-img js-swiper-slider" data-settings='{"resizeObserver": true}'>
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <a href="{{ route('products.show', $product->slug) }}">
                                            @php
                                                $firstImage = $product->images ? explode(',', $product->images)[0] : null;
                                                $firstThumbnail = $product->thumbnails ? explode(',', $product->thumbnails)[0] : null;
                                            @endphp
                                            @if($firstThumbnail)
                                                <img loading="lazy" src="{{ trim($firstThumbnail) }}" width="258" height="313" alt="{{ $product->title }}" class="pc__img">
                                            @elseif($firstImage)
                                                <img loading="lazy" src="{{ trim($firstImage) }}" width="258" height="313" alt="{{ $product->title }}" class="pc__img">
                                            @else
                                                <img loading="lazy" src="{{ asset('images/products/product_0.jpg') }}" width="258" height="313" alt="{{ $product->title }}" class="pc__img">
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('products.show', $product->slug) }}" class="pc__atc anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium" data-aside="cartDrawer" title="Sepete Ekle">Görüntüle</a>
                        </div>

                        <div class="pc__info position-relative">
                            <h6 class="pc__title"><a href="{{ route('products.show', $product->slug) }}">{{ $product->title }}</a></h6>
                            <p class="pc__category">{{ $product->mainCategory->title ?? 'Belirtilmemiş' }}</p>
                            <div class="product-card__price d-flex">
                            
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @else
        <div class="col-12 text-center py-5">
            <h4>Bu kategoride henüz ürün bulunmamaktadır.</h4>
            <p class="text-muted">Başka kategorileri inceleyebilirsiniz.</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Ana Sayfaya Dön</a>
        </div>
        @endif
        
    </section>
</main>
@endsection