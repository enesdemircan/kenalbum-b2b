@extends('frontend.master')

@section('content')
<main>
    <div class="mb-md-1 pb-md-3"></div>
    <section class="product-single container">
      <div class="row">
        <div class="col-lg-7">
          <div class="product-single__media" data-media-type="vertical-thumbnail">
            <div class="product-single__image">
              <div class="swiper-container">
                <div class="swiper-wrapper">
                  @php
                    $images = $product->images ? explode(',', $product->images) : [];
                    $thumbnails = $product->thumbnails ? explode(',', $product->thumbnails) : [];
                    if (empty($images) && $product->images) {
                        $images = [$product->images];
                    }
                    if (empty($thumbnails) && $product->thumbnails) {
                        $thumbnails = [$product->thumbnails];
                    }
                  @endphp
                  
                  @if(count($images) > 0)
                    @foreach($images as $index => $image)
                      <div class="swiper-slide product-single__image-item">
                        <img loading="lazy" class="h-auto" src="{{ trim($image) }}" width="674" height="674" alt="{{ $product->title }}">
                        @php
                            $thumbnailForZoom = isset($thumbnails[$index]) ? trim($thumbnails[$index]) : trim($image);
                        @endphp
                       
                      </div>
                    @endforeach
                  @else
                    <div class="swiper-slide product-single__image-item">
                      <img loading="lazy" class="h-auto" src="{{ asset('images/products/product_0.jpg') }}" width="674" height="674" alt="{{ $product->title }}">
                   
                    </div>
                  @endif
                </div>
                <div class="swiper-button-prev"><svg width="7" height="11" viewBox="0 0 7 11" xmlns="http://www.w3.org/2000/svg"><use href="#icon_prev_sm" /></svg></div>
                <div class="swiper-button-next"><svg width="7" height="11" viewBox="0 0 7 11" xmlns="http://www.w3.org/2000/svg"><use href="#icon_next_sm" /></svg></div>
              </div>
            </div>
            <div class="product-single__thumbnail">
              <div class="swiper-container">
                <div class="swiper-wrapper">
                  @if(count($thumbnails) > 0)
                    @foreach($thumbnails as $index => $thumbnail)
                      <div class="swiper-slide product-single__image-item"><img loading="lazy" class="h-auto" src="{{ trim($thumbnail) }}" width="104" height="104" alt="{{ $product->title }}"></div>
                    @endforeach
                  @elseif(count($images) > 0)
                    @foreach($images as $image)
                      <div class="swiper-slide product-single__image-item"><img loading="lazy" class="h-auto" src="{{ trim($image) }}" width="104" height="104" alt="{{ $product->title }}"></div>
                    @endforeach
                  @else
                    <div class="swiper-slide product-single__image-item"><img loading="lazy" class="h-auto" src="{{ asset('images/products/product_0.jpg') }}" width="104" height="104" alt="{{ $product->title }}"></div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5">
      
          <h1 class="product-single__name">{{ $product->title }}</h1>
         
        
          <div class="product-single__short-desc">
            <p>{{ $product->description ?? 'Açıklama bulunmuyor.' }}</p>
          </div>
          
          @if($product->stock_status == 'out_of_stock')
            <div class="alert alert-warning">
              <strong>Stokta Yok!</strong> Bu ürün şu anda stokta bulunmamaktadır.
            </div>
            @else
            <div>
              <a href="{{ route('products.ordercreate', $product->id) }}" class="btn btn-primary">SEPETE EKLE</a>
            </div>
          @endif
         
          @if($product->template_url)
            <div class="mt-3">
              <a href="{{ Storage::url($product->template_url) }}" class="btn btn-outline-secondary" download>
                <i class="bi bi-download"></i> Şablonu İndir
              </a>
              <small class="d-block text-muted mt-2">Bu ürün için tasarım şablonunu indirip, ürününüzü tasarlayabilirsiniz.</small>
            </div>
          @endif
         
          <br>
      
          <div class="product-single__meta-info">
            <div class="meta-item">
              <label>Stok Durumu:</label>
              <span>
                @if($product->stock_status == 'in_stock')
                  <span class="text-success-2">Stokta Var</span>
                @else
                  <span class="text-danger-2">Stokta Yok</span>
                @endif
              </span>
            </div>
            <div class="meta-item">
              <label>Kategori:</label>
              <span>{{ $product->mainCategory->title ?? 'Belirtilmemiş' }}</span>
            </div>
            @if($product->tags)
            <div class="meta-item">
              <label>Etiketler:</label>
              <span>{{ $product->tags }}</span>
            </div>
            @endif
         
          </div>
        </div>
      </div>
      @if($product->details && $product->details->count() > 0)
        <div class="product-single__details-tab">
          <ul class="nav nav-tabs" id="myTab1" role="tablist">
            @foreach($product->details as $index => $detail)
              <li class="nav-item" role="presentation">
                <a class="nav-link nav-link_underscore {{ $index === 0 ? 'active' : '' }}" 
                   id="tab-detail-{{ $detail->id }}-tab" 
                   data-bs-toggle="tab" 
                   href="#tab-detail-{{ $detail->id }}" 
                   role="tab" 
                   aria-controls="tab-detail-{{ $detail->id }}" 
                   aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                  {{ $detail->title }}
                </a>
              </li>
            @endforeach
          </ul>
          <div class="tab-content">
            @foreach($product->details as $index => $detail)
              <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                   id="tab-detail-{{ $detail->id }}" 
                   role="tabpanel" 
                   aria-labelledby="tab-detail-{{ $detail->id }}-tab">
                <div class="product-detail-content">
                  <div class="detail-text">
                    {!! $detail->text !!}
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </section>

<style>
.product-detail-content {
    padding: 20px 0;
}

.detail-text {
    line-height: 1.6;
    color: #333;
}

.detail-text p {
    margin-bottom: 15px;
}

.detail-text h1, .detail-text h2, .detail-text h3, .detail-text h4, .detail-text h5, .detail-text h6 {
    margin-top: 20px;
    margin-bottom: 10px;
    color: #222;
}

.detail-text ul, .detail-text ol {
    margin-bottom: 15px;
    padding-left: 20px;
}

.detail-text li {
    margin-bottom: 5px;
}

.detail-text img {
    max-width: 100%;
    height: auto;
    margin: 10px 0;
}

.detail-text blockquote {
    border-left: 4px solid #007bff;
    padding-left: 15px;
    margin: 15px 0;
    font-style: italic;
    color: #666;
}

.detail-text table {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
}

.detail-text table th,
.detail-text table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.detail-text table th {
    background-color: #f8f9fa;
    font-weight: bold;
}
</style>
   
  </main>
  <br>
  <br>  
 
  @endsection