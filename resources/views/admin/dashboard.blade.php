@extends('admin.layout')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1 class="h3 mb-4">Admin Dashboard</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Ana Kategoriler</h5>
                <p class="card-text display-6">{{ $mainCategoriesCount }}</p>
                <a href="{{ route('admin.main-categories.index') }}" class="btn btn-light btn-sm">Görüntüle</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Ürünler</h5>
                <p class="card-text display-6">{{ $productsCount }}</p>
                <a href="{{ route('admin.products.index') }}" class="btn btn-light btn-sm">Görüntüle</a>
            </div>
        </div>
    </div>
    

    
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title">Özelleştirme Parametreleri</h5>
                <p class="card-text display-6">{{ $customizationParamsCount }}</p>
                <a href="#" class="btn btn-light btn-sm">Görüntüle</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Son Eklenen Ürünler</h5>
            </div>
            <div class="card-body">
                @if($recentProducts->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentProducts as $product)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $product->title }}</h6>
                                    <small class="text-muted">{{ $product->price }} TL</small>
                                </div>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">Henüz ürün eklenmemiş.</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Hızlı İşlemler</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Yeni Ürün Ekle</a>
                    <a href="{{ route('admin.main-categories.create') }}" class="btn btn-success">Yeni Kategori Ekle</a>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection 