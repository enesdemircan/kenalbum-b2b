@extends('frontend.master')

@section('content')
<main>
    <div class="mb-4 pb-lg-3"></div>

    <section class="shop-main container">
        <div class="d-flex justify-content-between mb-4 pb-md-2">
            <div class="breadcrumb mb-0 d-none d-md-block flex-grow-1">
                <a href="{{ route('home') }}" class="menu-link menu-link_us-s text-uppercase fw-medium">Ana Sayfa</a>
                <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
                <span class="menu-link menu-link_us-s text-uppercase fw-medium">Arama</span>
            </div>
        </div>

        <h3 class="mb-3">Arama Sonuçları</h3>

        <form method="GET" action="{{ route('search') }}" class="mb-4">
            <div class="input-group" style="max-width: 600px;">
                <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Ürün veya kategori ara..." minlength="2" maxlength="100" required>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Ara
                </button>
            </div>
        </form>

        @if(mb_strlen($q) < 2)
            <div class="col-12 text-center py-5">
                <h5 class="text-muted">Aramak için en az 2 karakter giriniz.</h5>
            </div>
        @elseif($products->isEmpty() && $categories->isEmpty())
            <div class="col-12 text-center py-5">
                <h5>"{{ $q }}" için sonuç bulunamadı.</h5>
                <p class="text-muted">Farklı bir kelimeyle aramayı deneyin.</p>
                <a href="{{ route('home') }}" class="btn btn-primary mt-2">Ana Sayfaya Dön</a>
            </div>
        @else
            <p class="text-muted mb-4">"<strong>{{ $q }}</strong>" için {{ $categories->count() }} kategori ve {{ $products->count() }} ürün bulundu.</p>

            @if($categories->isNotEmpty())
                <h5 class="mb-3">Kategoriler</h5>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 mb-5">
                    @foreach($categories as $category)
                        <div class="col mb-3">
                            <a href="{{ route('category.show', $category->slug) }}" class="btn btn-outline-primary w-100 text-start">
                                <i class="fas fa-folder"></i> {{ $category->title }}
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($products->isNotEmpty())
                <h5 class="mb-3">Ürünler</h5>
                <div class="products-grid row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">
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
                                    <a href="{{ route('products.show', $product->slug) }}" class="pc__atc anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium" title="Görüntüle">Görüntüle</a>
                                </div>

                                <div class="pc__info position-relative">
                                    <h6 class="pc__title"><a href="{{ route('products.show', $product->slug) }}">{{ $product->title }}</a></h6>
                                    <p class="pc__category">{{ $product->mainCategory->title ?? 'Belirtilmemiş' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </section>
</main>
@endsection
