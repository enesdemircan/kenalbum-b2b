@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h3">Tahsilat İşlemleri</h1>
        <p class="text-muted mb-0">{{ $customer->unvan }} firması için tahsilat listesi</p>
    </div>
    <div>
        <a href="{{ route('admin.customers.collections.create', $customer->id) }}" class="btn btn-primary me-2">
            <i class="bi bi-plus-circle"></i> Yeni Tahsilat Ekle
        </a>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Firmalara Dön
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<!-- Firma Bilgileri -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Firma Bilgileri</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong>Firma ID:</strong> {{ $customer->firma_id }}
            </div>
            <div class="col-md-3">
                <strong>Ünvan:</strong> {{ $customer->unvan }}
            </div>
            <div class="col-md-3">
                <strong>Mevcut Bakiye:</strong> 
                <span class="badge {{ $customer->balance > 0 ? 'bg-success' : ($customer->balance < 0 ? 'bg-danger' : 'bg-secondary') }}">
                    {{ $customer->formatted_balance }}
                </span>
            </div>
            <div class="col-md-3">
                <strong>Toplam Tahsilat:</strong> 
                <span class="badge bg-info">{{ $customer->formatted_total_collections }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Tahsilat Listesi -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Tahsilat Geçmişi</h5>
    </div>
    <div class="card-body">
        @if($collections->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
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
            </div>
            
            <!-- Sayfalama -->
            <div class="d-flex justify-content-center mt-4">
                {{ $collections->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h5 class="text-muted mt-3">Henüz tahsilat bulunmuyor</h5>
                <p class="text-muted">Bu firmaya ait tahsilat kaydı bulunmamaktadır.</p>
                <a href="{{ route('admin.collections.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> İlk Tahsilatı Ekle
                </a>
            </div>
        @endif
    </div>
</div>

@endsection 