@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Ürünler</h1>
        <p class="page-subtitle">Tüm ürünleri görüntüleyin ve yönetin</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn-material btn-material-primary">
        <span class="material-icons">add</span>
        Yeni Ürün Ekle
    </a>
</div>

<!-- Filtre Accordion -->
<div class="filter-accordion material-card">
    <div class="filter-header" id="filterHeader" onclick="toggleFilterAccordion()">
        <span class="material-icons">filter_list</span>
        <span>Filtrele</span>
        <span class="material-icons expand-icon">expand_more</span>
    </div>
    <div class="filter-body" id="filterBody">
        <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3">
            <div class="col-md-6">
                <label for="search" class="form-label">Ürün Adı</label>
                <input type="text" class="form-control form-control-material" id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Ürün adı ara...">
            </div>
            <div class="col-md-6">
                <label for="category_id" class="form-label">Kategori</label>
                <select class="form-select form-control-material" id="category_id" name="category_id">
                    <option value="">Tüm Kategoriler</option>
                    @foreach($allCategories as $category)
                        <option value="{{ $category->id }}" {{ ($filters['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <button type="submit" class="btn-material btn-material-primary">
                    <span class="material-icons">search</span>
                    Filtrele
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn-material btn-material-secondary">
                    <span class="material-icons">clear</span>
                    Temizle
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Ürünler Tablosu -->
<div class="material-table-wrapper">
    <table class="material-table">
        <thead>
            <tr>
                <th style="width: 80px">Resim</th>
                <th>Başlık</th>
                <th>Kategori</th>
                <th>Stok Durumu</th>
                <th style="width: 220px">İşlemler</th>
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
                            <img src="{{ trim($firstThumbnail) }}" alt="{{ $product->title }}" width="60" height="60" style="object-fit: cover; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1)">
                        @elseif($firstImage)
                            <img src="{{ trim($firstImage) }}" alt="{{ $product->title }}" width="60" height="60" style="object-fit: cover; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1)">
                        @else
                            <div style="width: 60px; height: 60px; background-color: #f5f5f5; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                <span class="material-icons" style="color: #bdbdbd">image</span>
                            </div>
                        @endif
                    </td>
                    <td>{{ $product->title }}</td>
                    <td>{{ $product->mainCategory ? $product->mainCategory->title : '-' }}</td>
                    <td>
                        @if($product->stock_status == 'in_stock')
                            <span class="material-badge material-badge-success">Stokta Var</span>
                        @else
                            <span class="material-badge material-badge-danger">Stokta Yok</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <button class="btn-material-icon btn-material-icon-warning" title="Düzenle" onclick="window.location.href='{{ route('admin.products.edit', $product) }}'">
                                <span class="material-icons">edit</span>
                            </button>
                            <button class="btn-material-icon btn-material-icon-info" title="Ek Detaylar" onclick="window.location.href='{{ route('admin.product-details.index', $product->id) }}'">
                                <span class="material-icons">info</span>
                            </button>
                            <button class="btn-material-icon btn-material-icon-secondary" title="Özelleştirme" onclick="window.location.href='{{ route('admin.product-customization-params.hierarchical', $product->id) }}'">
                                <span class="material-icons">tune</span>
                            </button>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-material-icon btn-material-icon-danger" title="Sil">
                                    <span class="material-icons">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Sayfalama -->
<div class="material-pagination">
    {{ $products->appends(request()->query())->links() }}
</div>

<script>
    // Filter Accordion Toggle - varsayılan kapalı
    function toggleFilterAccordion() {
        const header = document.getElementById('filterHeader');
        const body = document.getElementById('filterBody');
        const isOpen = body.classList.contains('open');
        
        if (isOpen) {
            body.classList.remove('open');
            header.classList.remove('active');
        } else {
            body.classList.add('open');
            header.classList.add('active');
        }
    }
</script>

@endsection
