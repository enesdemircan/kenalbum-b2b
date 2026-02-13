@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Tahsilat İşlemleri</h1>
    <a href="{{ route('admin.collections.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Yeni Tahsilat Ekle
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<!-- Filtre Formu -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filtrele</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.collections.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="customer_name" class="form-label">Firma Adı</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ $filters['customer_name'] ?? '' }}" placeholder="Firma adı ara...">
            </div>
            <div class="col-md-3">
                <label for="payment_method" class="form-label">Tahsilat Şekli</label>
                <select class="form-select" id="payment_method" name="payment_method">
                    <option value="">Tümü</option>
                    <option value="kredi_karti" {{ ($filters['payment_method'] ?? '') == 'kredi_karti' ? 'selected' : '' }}>Kredi Kartı</option>
                    <option value="havale" {{ ($filters['payment_method'] ?? '') == 'havale' ? 'selected' : '' }}>Havale</option>
                    <option value="nakit" {{ ($filters['payment_method'] ?? '') == 'nakit' ? 'selected' : '' }}>Nakit</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">Başlangıç</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">Bitiş</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Filtrele
                </button>
                <a href="{{ route('admin.collections.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Temizle
                </a>
            </div>
        </form>
    </div>
</div>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Firma</th>
            <th>Miktar</th>
            <th>Tahsilat Şekli</th>
            <th>Tarih</th>
            <th>Notlar</th>
            <th style="width: 150px;"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($collections as $collection)
            <tr>
                <td>{{ $collection->id }}</td>
                <td>
                    <a href="{{ route('admin.customers.show', $collection->customer->id) }}" class="text-decoration-none">
                        {{ $collection->customer->unvan }}
                    </a>
                </td>
                <td>
                    <strong class="text-success">{{ $collection->formatted_amount }}</strong>
                </td>
                <td>
                    <span class="badge {{ $collection->payment_method_badge_class }}">
                        {{ $collection->payment_method_text }}
                    </span>
                </td>
                <td>{{ $collection->formatted_date }}</td>
                <td>
                    @if($collection->notes)
                        <span class="text-muted">{{ Str::limit($collection->notes, 50) }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.collections.show', $collection->id) }}" class="btn btn-sm btn-info" title="Görüntüle">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.collections.edit', $collection->id) }}" class="btn btn-sm btn-warning" title="Düzenle">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('admin.collections.destroy', $collection->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bu tahsilatı silmek istediğinizden emin misiniz?')" title="Sil">
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
    {{ $collections->appends(request()->query())->links() }}
</div>

@endsection 