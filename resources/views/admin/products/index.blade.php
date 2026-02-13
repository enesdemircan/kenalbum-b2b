@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Ürünler</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Yeni Ürün Ekle
    </a>
</div>

<!-- Filtre Formu -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filtrele</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Ürün Adı</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Ürün adı ara...">
            </div>
            <div class="col-md-4">
                <label for="category_id" class="form-label">Kategori</label>
                <select class="form-select" id="category_id" name="category_id">
                    <option value="">Tüm Kategoriler</option>
                    @foreach($allCategories as $category)
                        <option value="{{ $category->id }}" {{ ($filters['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Filtrele
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Temizle
                </a>
            </div>
        </form>
    </div>
</div>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Resim</th>
            <th>Başlık</th>
            <th>Kategori</th>
       
            <th>Stok Durumu</th>
           
            <th style="width: 190px;"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
            <tr>
                <td>
                    @php
                        $firstImage = $product->images ? explode(',', $product->images)[0] : null;
                        $firstThumbnail = $product->thumbnails ? explode(',', $product->thumbnails)[0] : null;
                    @endphp
                    @if($firstThumbnail)
                        <img src="{{ trim($firstThumbnail) }}" alt="{{ $product->title }}" width="60" height="60" style="object-fit: cover;">
                    @elseif($firstImage)
                        <img src="{{ trim($firstImage) }}" alt="{{ $product->title }}" width="60" height="60" style="object-fit: cover;">
                    @else
                        <div style="width: 60px; height: 60px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; border: 1px solid #dee2e6;">
                            <i class="bi bi-image text-muted"></i>
                        </div>
                    @endif
                </td>
                <td>{{ $product->title }}</td>
                <td>{{ $product->mainCategory ? $product->mainCategory->title : '-' }}</td>
               
                <td>
                    @if($product->stock_status == 'in_stock')
                        <span class="badge bg-success">Stokta Var</span>
                    @else
                        <span class="badge bg-danger">Stokta Yok</span>
                    @endif
                </td>
         
             
               
                <td>
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-warning" title="Düzenle">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <a href="{{ route('admin.product-details.index', $product->id) }}" class="btn btn-sm btn-info" title="Ek Detaylar">
                        <i class="bi bi-info-circle"></i>
                    </a>
                    <a href="{{ route('admin.product-customization-params.hierarchical', $product->id) }}" class="btn btn-sm btn-secondary" title="Özelleştirme Parametreleri">
                        <i class="bi bi-gear"></i>
                    </a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğinize emin misiniz?')" title="Sil">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Sayfalama -->
<div class="d-flex justify-content-center mt-4">
    {{ $products->appends(request()->query())->links() }}
</div>

@endsection 