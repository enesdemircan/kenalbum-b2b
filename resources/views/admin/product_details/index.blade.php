@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Ürün Detayları - {{ $product->title }}</h1>
    <div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary me-2">← Ürünlere Dön</a>
        <a href="{{ route('admin.product-details.create', $product->id) }}" class="btn btn-primary">
            + Yeni Detay Ekle
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        @if($details->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                         
                            <th>Başlık</th>
                            
                           
                            <th width="200">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($details as $detail)
                            <tr>
                               
                                <td>
                                    <strong>{{ $detail->title }}</strong>
                                </td>
                           
                               
                                <td>
                                    <a href="{{ route('admin.product-details.edit', [$product->id, $detail->id]) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Düzenle
                                    </a>
                                    <form action="{{ route('admin.product-details.destroy', [$product->id, $detail->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bu detayı silmek istediğinize emin misiniz?')">
                                            <i class="bi bi-trash"></i> Sil
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">Henüz detay eklenmemiş</h5>
                <p class="text-muted">Bu ürün için henüz detay eklenmemiş. Yeni detay eklemek için yukarıdaki butonu kullanın.</p>
            </div>
        @endif
    </div>
</div>

@endsection
